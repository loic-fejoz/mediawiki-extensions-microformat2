<?php

/** Extension: UFMicroFormat2
 * Simple extension to help user publish microformat2 documents.
 *
 * @author Loïc Fejoz
 *
 * @section LICENSE
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 * 
 * @file
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}

if ( defined( 'UF_MICROFORMAT_VERSION' ) ) {
   // Do not load UFMicroFormat2 more than once
   return 1;
}
define( 'UF_MICROFORMAT_VERSION', '0.0.0' );
 
define( 'SMW_VERSION', '2.1 alpha' );

$GLOBALS['wgExtensionCredits']['parserhook']['UFMicroFormat2'] = array(
    'path' => __FILE__,
    'name' => 'UFMicroFormat2',
    'version' => '0.0.0',
    'author' => array('Loïc Fejoz'),
    'url' => 'https://github.com/loic-fejoz/mediawiki-extensions-microformat2',
    'descriptionmsg' => 'ufmicroformat2-desc',
    'license-name' => 'GPL-2.0+', // GNU General Public License v2.0 or later
);
$dir = dirname(__FILE__) . '/';
$wgExtensionMessagesFiles['UFMicroFormat2'] = $dir . 'UFMicroFormat2.i18n.php';
$wgAutoloadClasses['UFMicroFormat2Parser'] = $dir . 'UFMicroFormat2Parser.php';
$wgHooks['ParserFirstCallInit'][] = 'initUFMicroFormat2';
$wgHooks['LanguageGetMagic'][]       = 'wfParserFunctionsLanguageGetMagic';
/**
 * Register parser hook
 */
function initUFMicroFormat2() {
    require_once('UFMicroFormat2Parser.php');
    UFMicroFormat2Parser::registerHooks();
    return true;
}

function wfParserFunctionsLanguageGetMagic( &$magicWords, $langCode ) {
	switch ( $langCode ) {
		default:
			$magicWords['uf2']    = array( 0, 'uf2' );
	}
	return true;
}
?>