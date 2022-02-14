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
	'ACP_SCSSCOMPILER_EXPLAIN'					=> '<p>Certain styles must be compiled to look differently. The styling commands are written in a SASS format. Changes are made to .scss files and compiled to .css files. <a href="https://sass-lang.com/">Learn more about SASS.</a> This extension will compile these kinds of styles.</p>
<p>The styles folder is not publicly writable. So after compilation, you must manually move the compiled .css files into the style’s theme folder, replacing the old stylesheets. The compiled styles are automatically be stored inside your board’s /store directory, which should be writable.</p>
<p>In general, compile the style’s stylesheet.scss if it exists (which should link in all other .sccs files for the style) and compile to the style’s stylesheet.css file if it exists. You can choose alternate files to compile from and to if they exist.</p>
<p><strong>Editing .scss files:</strong></p>
<ul>
<li>Download your style’s theme folder with FTP. <strong>You should save a backup copy after downloading.</strong></li>
<li>Edit the .scss files with a text editor on your computer.</li>
<li>Upload the changed .scss files to the /styles/<em>&lt;style&gt;</em>/theme folder, overwriting the old files. If a file is in a folder inside the theme folder, make sure you put it in the same folder.</li>
</ul>
<p><strong>Compiling styles:</strong></p>
<ul>
<li>Check those styles you want to recompile then press the “Recompile checked styles” button. Any installed styles containing .scss files are shown below. Any <strong>bolded style names</strong> should be recompiled because the compiled stylesheet.css file is older than the most recently written .scss file for the style. Styles <span style="color: #999;">in grey</span> are inactive. If there are any compilation errors, they will be shown. Fix any errors, and upload the changed .scss files again. Repeat until compilation succeeds.</li>
<li>The compiled .css files are placed in a /store/phpbbservices/scsscompiler/<em>&lt;style&gt;</em>/theme folder. You can use FTP to download the file inside the folder then upload it to the /styles/<em>&lt;style&gt;</em>/theme folder, overwriting the old .css files. You can also use a file manager provided by your web host to copy these into the correct folder, overwriting the old .css files.</li>
<li>View the style changes by going to the index. Generally, you do not have to purge the cache to see the style changes. If you don’t see the changes, reload the page. If you still don’t see the changes you made, <a href="https://refreshyourcache.com/en/cache/">refresh your browser’s cache</a> then reload the page. <em>Note</em>: on some servers, you may have to flush a CloudFlare or similar cache too.</li>
</ul>
<p>If you need to restore the old files, upload your backup copy of the theme folder to the same theme folder, overwriting any files you changed. You may need to also purge the cache on the main page of the ACP.</p>',
	'ACP_SCSSCOMPILER_FILE'						=> 'File',
	'ACP_SCSSCOMPILER_INSTALL_REQUIREMENTS'		=> 'Your version of PHP must be &gt; 3.3.0 and &lt; 4.0 to install this extension. Please address this issue, then try enabling the extension again.',
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
	'ACP_SCSSCOMPILER_WRITABLE'					=> 'Style writable'
]);
