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

	'ACP_SCSSCOMPILER_CANT_FIND_STYLE'			=> 'Can’t find style_id %d, so it cannot be compiled',
	'ACP_SCSSCOMPILER_CANT_OPEN_CSS_FILE'		=> 'Can’t open file %s. It either does not exist or is not publicly readable.',
	'ACP_SCSSCOMPILER_CANT_WRITE_CSS_FILE'		=> 'Can’t write to file %s. Make sure the file’s permissions are globally writeable (777)',
	'ACP_SCSSCOMPILER_CODE'						=> 'Code',
	'ACP_SCSSCOMPILER_CSS_LAST_SAVED'			=> 'CSS files last modified',
	'ACP_SCSSCOMPILER_EXPLAIN'					=> 'Certain phpBB styles need to be compiled from .scss source files to reflect new style changes. Any installed styles containing .scss files are shown below. Any <strong>bolded style names</strong> should be recompiled because the compiled .css file is older than the most recently written to .scss file. Check those styles you want to recompile then press the “Recompile checked styles” button.<br><br>To compile a style, a theme/stylesheet.scss file must exist and it must also be a style’s master SCSS file. A style’s theme/stylesheet.css must be publicly writeable and must be the style’s master CSS file.',
	'ACP_SCSSCOMPILER_FILE'						=> 'File',
	'ACP_SCSSCOMPILER_LINE'						=> 'Line',
	'ACP_SCSSCOMPILER_MARK'						=> 'Mark/unmark',
	'ACP_SCSSCOMPILER_NO_SCSS_STYLES'			=> 'No SCSS styles are active',
	'ACP_SCSSCOMPILER_NO_STYLES_TO_COMPILE'		=> 'No styles were selected to compile',
	'ACP_SCSSCOMPILER_OVERRIDE'					=> 'Style overrides other styles',
	'ACP_SCSSCOMPILER_RECOMPILE_CHECKED'		=> 'Recompile checked styles',
	'ACP_SCSSCOMPILER_RECOMPILE_STYLE'			=> 'Recompile style',
	'ACP_SCSSCOMPILER_SCSS_COMPILE_ERROR'		=> 'Can’t compile style “%1$s” due to compilation error(s):<br><br>%2$s',
	'ACP_SCSSCOMPILER_SCSS_FILE_DOES_NOT_EXIST'	=> 'Can’t compile style “%1$s” because file %2$s cannot be found.',
	'ACP_SCSSCOMPILER_SCSS_LAST_SAVED'			=> 'SCSS files last modified',
	'ACP_SCSSCOMPILER_SETTING_SAVED'			=> 'Selected styles were recompiled successfully',
	'ACP_SCSSCOMPILER_STYLE_INFO'				=> 'SCSS-based style',
	'ACP_SCSSCOMPILER_WRITEABLE'				=> 'stylesheet.css is writeable'

]);
