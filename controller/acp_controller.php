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

	protected $filesystem;
	protected $language;
	protected $request;
	protected $template;

	/**
	 * Constructor.
	 *
	 * @param \phpbb\config\config			$config				Config object
	 * @param \phpbb\db\driver\factory 		$db 				The database factory object
	 * @param \phpbb\filesystem\filesystem 	$filesystem 		Filesystem object
	 * @param \phpbb\language\language 		$language 			Language object
	 * @param \phpbb\request\request		$request			Request object
	 * @param \phpbb\template\template		$template			Template object
	 * @param \phpbb\user					$user				User object
	 * @param \phpbb\template\template 		$template 			Template object
	 * @param string 						$phpbb_root_path 	Relative path to phpBB root	 *
	 */
	public function __construct(\phpbb\config\config $config, \phpbb\language\language $language, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user, \phpbb\db\driver\factory $db, string $phpbb_root_path, \phpbb\filesystem\filesystem $filesystem)
	{
		$this->config			= $config;
		$this->db				= $db;
		$this->filesystem		= $filesystem;
		$this->language			= $language;
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
		// Add our common language file
		$this->language->add_lang('acp/styles');
		$this->language->add_lang('common', 'phpbbservices/scsscompiler');

		// Create a form key for preventing CSRF attacks
		add_form_key('phpbbservices_scsscompiler_acp');

		// Create an array to collect errors that will be output to the user
		$errors = [];

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

				$styles_to_compile = array();
				$submit_vars = $this->request->variable_names();
				foreach ($submit_vars as $submit_var)
				{
					if (substr($submit_var, 0, 2) == 's-')
					{
						$styles_to_compile[] = (int) substr($submit_var,2);
					}
				}

				if (count($styles_to_compile) === 0)
				{
					trigger_error($this->language->lang('ACP_SCSSCOMPILER_NO_STYLES_TO_COMPILE') . adm_back_link($this->u_action));
				}

				include('../ext/phpbbservices/scsscompiler/vendor/scssphp/scss.inc.php');

				foreach ($styles_to_compile as $style_id)
				{

					// Get style's metadata
					$sql = 'SELECT style_name, style_path 
							FROM ' . STYLES_TABLE . ' 
							WHERE style_id = ' . (int) $style_id;
					$result = $this->db->sql_query($sql);
					$rowset = $this->db->sql_fetchrowset($result);

					if (count($rowset) == 0)
					{
						// It would be very strange if the style_id could not be found, but if so let's capture the error
						$errors[] = $this->language->lang('ACP_SCSSCOMPILER_CANT_FIND_STYLE', $style_id);
					}
					else
					{
						// Check to make sure stylesheet.scss exists
						$path_to_style = '../styles/' . $rowset[0]['style_path'] . '/theme/';
						if (@file_exists($path_to_style . 'stylesheet.scss'))
						{
							// Compile the SCSS
							$css_writeable = true;

							// Write to stylesheet.css for the style
							if (!is_writable($path_to_style . 'stylesheet.css'))
							{
								// Attempt to change the file permissions to 755
								try
								{
									$this->filesystem->chmod(array($path_to_style . 'stylesheet.css'),755);
								}
								catch (\Exception $e2)
								{
									$css_writeable = false;
									$errors[] = $this->language->lang('ACP_SCSSCOMPILER_CANT_WRITE_CSS_FILE' , $path_to_style . 'stylesheet.css');
								}
							}

							if ($css_writeable)
							{
								try
								{
									// Compile the style
									$compiler = new Compiler();
									$compiler->setImportPaths($path_to_style);
									$compiled_css = $compiler->compileString('@import "stylesheet.scss";')->getCss();

									// Write the compiled CSS
									$handle = @fopen($path_to_style . 'stylesheet.css', 'w');
									@fwrite($handle, $compiled_css);
									@fclose($handle);
								}
								catch (\Exception $e)
								{
									$errors[] = $this->language->lang('ACP_SCSSCOMPILER_SCSS_COMPILE_ERROR', $rowset[0]['style_name'], $e->getMessage());
								}

							}
						}
						else
						{
							$errors[] = $this->language->lang('ACP_SCSSCOMPILER_SCSS_FILE_DOES_NOT_EXIST', $rowset[0]['style_name'], $path_to_style . 'stylesheet.scss');
						}

						unset($compiler);
					}

					$this->db->sql_freeresult($result);

				}

				// Option settings have been updated and logged
				// Confirm this to the user and provide link back to previous page
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

		// Get a list of enabled styles
		$sql = 'SELECT style_id, style_name, style_path 
				FROM ' . STYLES_TABLE . ' 
				WHERE style_active = 1';
		$result = $this->db->sql_query($sql);
		$rowset = $this->db->sql_fetchrowset($result);

		$s_errors = !empty($errors);

		// Set output variables for display in the template
		$this->template->assign_vars([
			'ERROR_MSG'						=> $s_errors ? implode('<br />', $errors) : '',
			'S_ERROR'						=> $s_errors,
			'S_INCLUDE_SCSS_COMPLIER_CSS'	=> true,
			'U_ACTION'						=> $this->u_action,
		]);

		$styles_path = $this->phpbb_root_path . 'styles/';

		foreach ($rowset as $row)
		{

			// Find all .scss files
			$files = $this->find_scss_files($styles_path . $row['style_path'] . '/');

			if (is_array($files) && count($files) > 0)
			{
				$scss_time = 0;
				foreach ($files as $file)
				{
					$filename = $styles_path . $row['style_path'] . '/' . $file;
					$scss_time = max(@file_exists($filename) ? @filemtime($filename) : 0, $scss_time);
				}

				// Get last stylesheet.css modification time
				$filename = $styles_path . $row['style_path'] . '/theme/' . 'stylesheet.css';
				$css_time = @file_exists($filename) ? @filemtime($filename) : 0;
				$css_writeable = is_writable($filename) ? $this->language->lang('YES') : '<strong>' . $this->language->lang('NO') . '</strong>';

				$recompile = (bool) $css_time < $scss_time;

				$this->template->assign_block_vars('styles', array(
						'ACP_SCSSCOMPILER_CSS_TIME'			=> date($this->user->data['user_dateformat'],$css_time),
						'ACP_SCSSCOMPILER_CSS_WRITEABLE'	=> $css_writeable,
						'ACP_SCSSCOMPILER_ID' 				=> $row['style_id'],
						'ACP_SCSSCOMPILER_NAME' 			=> $row['style_name'],
						'ACP_SCSSCOMPILER_SCSS_TIME'		=> date($this->user->data['user_dateformat'],$scss_time),
						'ACP_SCSSCOMPILER_YES_NO'			=> $override_other_styles && ((int) $row['style_id'] == $active_style) ? $this->language->lang('YES') : $this->language->lang('NO'),
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
	 * @param string $styles_path Path to directory
	 * @param string $prefix Prefix for files in resulting array
	 *
	 * @return array
	 */
	protected function find_scss_files($styles_path, $prefix = '')
	{
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
				$files = array_merge($files, $this->find_scss_files($fileInfo->getPathname(), $prefix . $fileInfo->getFilename() . '/'));
			}
			elseif ($fileInfo->isFile() && $fileInfo->getExtension() == 'scss')
			{
				$files[] = $prefix . $fileInfo->getFilename();
			}
		}

		return $files;
	}

}
