<?php
/**
 *
 * SCSS Compiler. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2021, MarkDHamill, https://www.phpbbservices.com/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbservices\scsscompiler\controller;

use ScssPhp\ScssPhp\Compiler;

/**
 * SCSS Compiler ACP controller.
 */
class acp_controller
{

	protected $config;
	protected $db;
	protected $filesystem;
	protected $language;
	protected $phpbb_log;
	protected $phpbb_root_path;
	protected $request;
	protected $template;
	protected $user;

	/**
	 * Constructor.
	 *
	 * @param \phpbb\config\config			$config				Config object
	 * @param \phpbb\db\driver\factory 		$db 				The database factory object
	 * @param \phpbb\filesystem\filesystem 	$filesystem 		Filesystem object
	 * @param \phpbb\language\language 		$language 			Language object
	 * @param \phpbb\log\log           		$phpbb_log          Log object
	 * @param string 						$phpbb_root_path 	Relative path to phpBB root
	 * @param \phpbb\request\request		$request			Request object
	 * @param \phpbb\template\template		$template			Template object
	 * @param \phpbb\user					$user				User object
	 */
	public function __construct(\phpbb\config\config $config, \phpbb\language\language $language, \phpbb\log\log $phpbb_log, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user, \phpbb\db\driver\factory $db, string $phpbb_root_path, \phpbb\filesystem\filesystem $filesystem)
	{
		$this->config			= $config;
		$this->db				= $db;
		$this->filesystem		= $filesystem;
		$this->language			= $language;
		$this->phpbb_log 		= $phpbb_log;
		$this->phpbb_root_path 	= $phpbb_root_path;
		$this->request			= $request;
		$this->template			= $template;
		$this->user				= $user;
	}

	/**
	 * Display the options a user can configure for this extension.
	 *
	 * @return void
	 */
	public function display_options()
	{
		// Add our common language files
		$this->language->add_lang('acp/styles');	// We need a few language variables from phpBB that are not loaded by default
		$this->language->add_lang('common', 'phpbbservices/scsscompiler');

		ob_start();

		// Create a form key for preventing CSRF attacks
		add_form_key('phpbbservices_scsscompiler_acp');

		// Create an array to collect errors that will be output to the user
		$errors = array();

		// Is the form being submitted to us?
		if ($this->request->is_set_post('submit'))
		{
			// Test if the submitted form is valid
			if (!check_form_key('phpbbservices_scsscompiler_acp'))
			{
				$errors[] = $this->language->lang('FORM_INVALID');
			}

			// If no errors, compile any selected styles
			if (empty($errors))
			{

				// Was the download checkbox checked?
				$download = (bool) ($this->request->variable('phpbbservices_scsscompiler_download', 'off') == 'on');

				// Get the style_id values to be compiled
				$styles_to_compile = array();
				$submit_vars = $this->request->variable_names();
				foreach ($submit_vars as $submit_var)
				{
					if (substr($submit_var, 0, 2) == 's-')
					{
						$styles_to_compile[] = (int) substr($submit_var,2);	// This is the style_id
					}
				}

				if (count($styles_to_compile) === 0)
				{
					trigger_error($this->language->lang('ACP_SCSSCOMPILER_NO_STYLES_TO_COMPILE') . adm_back_link($this->u_action));
				}

				// Include the 3rd party PHP SCSS library
				include('../ext/phpbbservices/scsscompiler/vendor/scssphp/scss.inc.php');

				// Get metadata for styles to be compiled
				$sql_array = array(
					'SELECT'	=> 'style_id, style_name, style_path',
					'FROM'		=> array(
						STYLES_TABLE => 's',
					),
					'WHERE'		=> $this->db->sql_in_set('style_id', $styles_to_compile)
				);
				$sql = $this->db->sql_build_query('SELECT', $sql_array);

				$result = $this->db->sql_query($sql);
				$styles = $this->db->sql_fetchrowset($result);

				$make_archive = count($styles) > 1;

				// Remove files and folders under store/phpbbservices/scsscompiler/
				@$this->filesystem->remove($this->phpbb_root_path . 'store/phpbbservices/scsscompiler/');

				$css_file_paths = array();
				$archive_path = $this->phpbb_root_path . 'store/phpbbservices/scsscompiler/compiled-styles.zip';

				// Create a new archive
				if ($make_archive)
				{
					$zip_archive = new \ZipArchive();
					$zip_archive->open($archive_path, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
				}

				foreach ($styles as $style)
				{

					// What .scss file was picked to compile? The input control's value provides the path needed.
					$scss_file_path = $this->request->variable('scss-' . $style['style_id'], '');

					// What .css file was picked to compile to? The input control's value provides the path needed.
					$css_file_path = $this->request->variable('css-' . $style['style_id'], '');
					$css_file = basename($css_file_path);
					$css_file_dir = substr(dirname($css_file_path),38);

					// Check to make sure the SCSS stylesheet wanted exists
					if (@file_exists($scss_file_path))
					{

						// Make the needed directories if necessary and correct any permission issues
						$success = $this->make_directories($css_file_path);

						if ($success)
						{
							// Attempt to compile this style
							try
							{
								$scss_file = basename($scss_file_path);
								$scss_directory = dirname($scss_file_path);

								// Compile the style
								$compiler = new Compiler();
								$compiler->setImportPaths($scss_directory);
								$compiled_css = $compiler->compileString('@import "' . $scss_file . '";')->getCss();

								// Write the compiled CSS
								$handle = @fopen($css_file_path, 'w+');
								@fwrite($handle, $compiled_css);
								@fclose($handle);
								unset($compiler);

								// Note the CSS file compiled
								$css_file_paths[] = $css_file_path;

								// Add to an archive
								if ($make_archive)
								{
									$zip_archive->addEmptyDir($css_file_dir);
									$zip_archive->addFile($css_file_path, $css_file_dir . DIRECTORY_SEPARATOR . $css_file);
								}
							}
							catch (\Exception $e)
							{
								// Save the compilation error for display so the error can be fixed
								$errors[] = $this->language->lang('ACP_SCSSCOMPILER_SCSS_COMPILE_ERROR', $style['style_name'], $e->getMessage());
								break;
							}
						}
						else
						{
							trigger_error(strip_tags($this->language->lang('ACP_SCSSCOMPILER_CREATE_ERROR')) . adm_back_link($this->u_action), E_USER_WARNING);
							return false;
						}
					}
					else
					{
						$errors[] = $this->language->lang('ACP_SCSSCOMPILER_SCSS_FILE_DOES_NOT_EXIST', $style['style_name'], $scss_file_path);
					}

				}

				if ($make_archive)
				{
					$zip_archive->close();
				}

				$this->db->sql_freeresult($result);

				if ($download && count($errors) == 0)
				{
					// Download compiled stylesheet or an archive of compiled stylesheets
					$mime_type = ($make_archive) ? 'application/x-zip-compressed' : 'text/css';
					$download_file_name = ($make_archive) ? $archive_path : $css_file_path;

					try
					{
						header('Content-Description: File Transfer');
						header("Content-Type: {$mime_type}");
						header('Content-Disposition: attachment; filename="'. basename($download_file_name) .'"');
						header('Expires: 0');
						header('Cache-Control: must-revalidate');
						header('Pragma: public');
						header('Content-Length: ' . filesize($download_file_name));
						readfile($download_file_name);
					}
					catch (\Exception $e)
					{
						$errors[] = $this->language->lang('ACP_SCSSCOMPILER_DOWNLOAD_ERROR');
					}
				}

				// Option settings have been updated and logged
				// Confirm this to the user and provide link back to previous page

				// Normally the page will not refresh if files are to be downloaded. Some Javascript on form submittal
				// will warn the user in this case.
				if (count($errors) == 0)
				{
					trigger_error($this->language->lang('ACP_SCSSCOMPILER_SETTING_SAVED') . adm_back_link($this->u_action));
				}
				else
				{
					trigger_error(implode('<br>', $errors) . adm_back_link($this->u_action), E_USER_WARNING);
				}
			}
		}

		// Do all users use the same style?
		$override_other_styles = (bool) $this->config['override_user_style'];
		$active_style = (int) $this->config['default_style'];

		// Get a list of installed styles
		$sql = 'SELECT style_id, style_name, style_path, style_active 
				FROM ' . STYLES_TABLE;
		$result = $this->db->sql_query($sql);
		$styles = $this->db->sql_fetchrowset($result);

		$s_errors = !empty($errors);

		// Set output variables for display in the template
		$this->template->assign_vars([
			'ERROR_MSG'						=> $s_errors ? implode('<br />', $errors) : '',
			'S_ERROR'						=> $s_errors,
			'S_INCLUDE_SCSS_COMPLIER_CSS'	=> true,
			'U_ACTION'						=> $this->u_action,
		]);

		$styles_path = $this->phpbb_root_path . 'styles/';

		foreach ($styles as $style)
		{

			// Find all .scss files
			$scss_files = $this->find_scss_files($styles_path . $style['style_path']);

			// Find all .css files
			$css_files = $this->find_scss_files($styles_path . $style['style_path'], true);

			if (is_array($scss_files) && count($scss_files) > 0)
			{
				$scss_time = 0;
				foreach ($scss_files as $file)
				{
					$filename = $styles_path . $style['style_path'] . '/' . $file;
					$scss_time = max(@file_exists($filename) ? @filemtime($filename) : 0, $scss_time);
				}

				// Build options for a select control containing .scss files found
				$options_scss = '';
				foreach ($scss_files as $file)
				{
					if (substr($file, -21) === 'theme/stylesheet.scss')
					{
						$options_scss .= '<option selected="selected" value="' . $file . '">' . substr($file,12)  . '</option>';
					}
					else
					{
						$options_scss .= '<option value="' . $file . '">' . substr($file, 12) . '</option>';
					}
				}

				// Build options for a select control containing .css files found
				$options_css = '';
				foreach ($css_files as $file)
				{
					// The compilation is placed in the /store/ folder, so give the optins tags a valid path to write to
					$store_file = str_replace('/styles/','/store/phpbbservices/scsscompiler/', $file);
					if (substr($file, -20) === 'theme/stylesheet.css')
					{
						$options_css .= '<option selected="selected" value="' . $store_file . '">' . substr($file, 12) . '</option>';
					}
					else
					{
						$options_css .= '<option value="' . $store_file . '">' . substr($file,12) . '</option>';
					}
				}

				// Get last stylesheet.css modification time
				$filename = $styles_path . $style['style_path'] . '/theme/' . 'stylesheet.css';
				$css_time = @file_exists($filename) ? @filemtime($filename) : 0;

				$recompile = (bool) $css_time < $scss_time;

				$this->template->assign_block_vars('styles', array(
						'ACP_SCSSCOMPILER_CSS_SELECT'		=> $options_css,
						'ACP_SCSSCOMPILER_ID' 				=> $style['style_id'],
						'ACP_SCSSCOMPILER_NAME' 			=> $style['style_name'],
						'ACP_SCSSCOMPILER_SCSS_SELECT'		=> $options_scss,
						'ACP_SCSSCOMPILER_YES_NO'			=> $override_other_styles && ((int) $style['style_id'] == $active_style) ? $this->language->lang('YES') : $this->language->lang('NO'),
						'S_ACP_SCSSCOMPILER_INACTIVE'		=> !(bool) $style['style_active'],
						'S_ACP_SCSSCOMPILER_RECOMPILE'		=> $recompile,
					)
				);
			}

		}

		$this->db->sql_freeresult($result);
	}

	/**
	 * Set custom form action.
	 *
	 * @param string	$u_action	Custom form action
	 * @return void
	 */
	public function set_page_url($u_action)
	{
		$this->u_action = $u_action;
	}

	/**
	 * Find all .scss files in directory
	 *
	 * @param string $styles_path 			Path to directory
	 * @param boolean $find_css 			If true, finds CSS instead of SCSS files
	 *
	 * @return array
	 */
	protected function find_scss_files($styles_path, $find_css = false)
	{
		
		// Finds either all the .scss files in a folder or subfolder, or all the .css files if $find_css = true
		//
		// $styles_path - relative path to the style folder
		// $find_css - true|false. If true, finds .css files, if false finds .scss files

		$files = array();

		if (!file_exists($styles_path))
		{
			return false;
		}

		foreach (new \DirectoryIterator($styles_path) as $fileInfo)
		{
			if ($fileInfo->isDot())
			{
				continue;
			}

			if ($fileInfo->isDir())
			{
				$files = array_merge($files, $this->find_scss_files($fileInfo->getPathname(), $find_css));
			}
			else if ($fileInfo->isFile())
			{
				$file_extension = $fileInfo->getExtension();
				if ($find_css && $file_extension == 'css')
				{
					$files[] = $fileInfo->getPathname();
				}
				if (!$find_css && $file_extension == 'scss')
				{
					$files[] = $fileInfo->getPathname();
				}
			}
		}

		return $files;

	}

	private function make_directories($path)
	{
		// Makes the necessary folders, if it can, in the /store folder and ensures they are writable if they exist
		//
		// path - Path to CSS theme folder, relative, from the phpBB the adm folder. This is set in the form's option tag.

		$directories = array_slice(explode('/', $path),3, 4);
		$path_so_far = $this->phpbb_root_path . 'store/';

		foreach ($directories as $directory)
		{
			$path_so_far .= $directory . '/';
			if (!$this->filesystem->exists($path_so_far))
			{
				try
				{
					$this->filesystem->mkdir($path_so_far);
				}
				catch (\Exception $e)
				{
					$this->phpbb_log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_CONFIG_SCSSCOMPILER_MKDIR_EXCEPTION', false, array($e->getMessage()));
					return false;
				}
			}
			else
			{
				// Ensure directory is writable
				if (!$this->filesystem->is_writable($path_so_far))
				{
					try
					{
						$this->filesystem->chmod($path_so_far, '0777');
					}
					catch (\Exception $e)
					{
						$this->phpbb_log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_CONFIG_SCSSCOMPILER_CHDIR_EXCEPTION', false, array($e->getMessage()));
						return false;
					}
				}
			}
		}

		return true;

	}

}
