<?php
/**
 *
 * SCSS Compiler. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2021, MarkDHamill, https://www.phpbbservices.com/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = [];
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters you may want to copy&paste:
// ’ » “ ” …
//

$lang = array_merge($lang, [

	'ACP_SCSSCOMPILER_CANT_FIND_STYLE'			=> 'No styles were found to compile. The database may be corrupt.',
	'ACP_SCSSCOMPILER_CANT_WRITE_CSS_FILE'		=> 'Can’t write to file %s. Make sure the directory’s permissions are globally writeable (777).',
	'ACP_SCSSCOMPILER_CODE'						=> 'Code',
	'ACP_SCSSCOMPILER_CREATE_ERROR'				=> 'Unable to create directories in /store folder. This is likely a file permissions issue.',
	'ACP_SCSSCOMPILER_CSS_FILE'					=> 'CSS stylesheet to compile to',
	'ACP_SCSSCOMPILER_CSS_FILE_DOESNT_EXIST'	=> 'File does not exist',
	'ACP_SCSSCOMPILER_DOWNLOAD'					=> 'Download the compiled styles',
	'ACP_SCSSCOMPILER_DOWNLOAD_ERROR'			=> 'Unable to download compiled styles',
	'ACP_SCSSCOMPILER_DOWNLOAD_EXPLAIN'			=> 'Check to also download the compiled files. If more than one style is compiled, an archive compiled-styles.zip is downloaded. The compiled styles can always be found in the /store/phpbbservices/scsscompiler folder.',
	'ACP_SCSSCOMPILER_DOWNLOAD_WARNING'			=> 'The screen is not normally refreshed if downloads are selected. Look for downloaded files in your downloads folder.',
	'ACP_SCSSCOMPILER_EXPLAIN'					=> '<p>Certain styles must be compiled to look differently. The styling commands are written in a SASS format. Changes are made to .scss files and compiled to .css files. <a href="https://sass-lang.com/">Learn more about SASS.</a> This extension will compile these kinds of styles.</p>
<p><strong>Editing .scss files</strong></p>
<ul>
	<li>Make backup copies of the theme folders of the styles you will alter.</li>
	<li>Download your styles’ theme folders</li>
	<li>Edit the .scss files with a text editor on your computer</li>
	<li>Upload the changed .scss files to the /styles/<em>&lt;style&gt;</em>/theme folders, overwriting the old files</li>
</ul>
<p><strong>Compiling styles</strong></p>
<ul>
	<li>Generally, compile the style’s stylesheet.scss file and compile to the style’s stylesheet.css file</li>
	<li>With your edits uploaded, check those styles you want to recompile then press the “Recompile checked styles” button. Any installed styles containing .scss files are shown below. Any <strong>bolded style names</strong> should be recompiled because the compiled stylesheet.css file is older than the most recently written .scss file for the style. Styles <span style="color: #999;">in grey</span> are inactive.</li>
	<li>If there are any compilation errors, they will be shown. Fix the errors and replace the changed .scss files again. Repeat until compilation succeeds.</li>
</ul>
<p><strong>Placing the compiled styles</strong></p>	
<ul>
	<li>Download the compiled .css files from the /store/phpbbservices/scsscompiler/<em>&lt;style&gt;</em>/theme folder</li>
	<li>Then upload them to the /styles/<em>&lt;style&gt;</em>/theme folder, overwriting the old files</li>
</ul>
<p><strong>Viewing the style changes</strong></p>
<p>Go to the index. If you don’t see the changes, reload the page. If you still don’t see the changes you made, <a href="https://refreshyourcache.com/en/cache/">refresh your browser’s cache</a> then reload the page. <em>Note</em>: on some servers, you may have to flush a CloudFlare or similar cache too.</p>
<p><strong>Restoring the style changes</strong></p>
<p>Upload your backup copy of the theme folder to the same theme folder, overwriting any files you changed. You may need to also purge the cache on the main page of the ACP.</p>',
	'ACP_SCSSCOMPILER_FILE'						=> 'File',
	'ACP_SCSSCOMPILER_INSTALL_REQUIREMENTS'		=> 'Your version of PHP must be &gt; 3.3.0 and &lt; 4.0 to install this extension. In addition the zip extension is required. Please address this issue, then try enabling the extension again.',
	'ACP_SCSSCOMPILER_LINE'						=> 'Line',
	'ACP_SCSSCOMPILER_MARK'						=> 'Mark/unmark',
	'ACP_SCSSCOMPILER_NO_SCSS_STYLES'			=> 'No SCSS styles are active',
	'ACP_SCSSCOMPILER_NO_STYLES_TO_COMPILE'		=> 'No styles were selected to compile',
	'ACP_SCSSCOMPILER_OVERRIDE'					=> 'Style overrides other styles',
	'ACP_SCSSCOMPILER_RECOMPILE_CHECKED'		=> 'Recompile checked styles',
	'ACP_SCSSCOMPILER_RECOMPILE_STYLE'			=> 'Recompile style',
	'ACP_SCSSCOMPILER_SCSS_COMPILE_ERROR'		=> 'Can’t compile style “%1$s” due to compilation error(s):<br><br>%2$s',
	'ACP_SCSSCOMPILER_SCSS_FILE'				=> 'SCSS stylesheet to compile',
	'ACP_SCSSCOMPILER_SCSS_FILE_DOES_NOT_EXIST'	=> 'Can’t compile style “%1$s” because file %2$s cannot be found.',
	'ACP_SCSSCOMPILER_SETTING_SAVED'			=> 'Selected styles were recompiled successfully. They now need to be moved to the correct theme folders.',
	'ACP_SCSSCOMPILER_STORE'					=> 'Write to /store/phpbbservices/scsscompiler/<em>&lt;style&gt;</em>/theme folder',
	'ACP_SCSSCOMPILER_STYLE_INFO'				=> 'SCSS-based style',
]);
