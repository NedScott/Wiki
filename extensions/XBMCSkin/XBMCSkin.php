<?php
/**
 * Dear XBMC team member,
 * This skin is four PHP files:
 * 
 * skins/XBMC.php
 *   - The skin
 * 
 * skins/XBMC.deps.php
 *   - This file exists because PHP sucks
 * 
 * extensions/XBMCSkin.php
 *   - Contains hooks that modify page data before the skin sees it
 * 
 * extensions/XBMCSkin.i18n.php
 *   - Wiki-specific CSS and navigation link
 * 
 * @author Garrett Brown
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}

// XBMC Skin version
define( 'MEDIAWIKI_XBMCSKIN_VERSION', '1.0' );

// Add information about this extension to Special:Version
$wgExtensionCredits['skin'][] = array(
	'path'           => __FILE__,
	'name'           => 'XBMC Skin',
	'author'         => 'Garrett Brown',
	'url'            => 'http://wiki.xbmc.org',
	#'descriptionmsg' => 'xbmcskin-desc',
	'version'        => MEDIAWIKI_XBMCSKIN_VERSION,
);

// Install the extension (see the XBMCSkinInit class below)
$wgExtensionFunctions[] = 'XBMCSkinInit::init';

// Hooks are how we can intercept and modify page data and behavior
// Every public static function in XBMCSkinHooks should be listed here
$wgHooks['SkinTemplateOutputPageBeforeExec'][] = 'XBMCSkinHooks::SkinTemplateOutputPageBeforeExec';
$wgHooks['PersonalUrls'][]                     = 'XBMCSkinHooks::PersonalUrls';
$wgHooks['InternalParseBeforeLinks'][]         = 'XBMCSkinHooks::InternalParseBeforeLinks';
$wgHooks['ParserBeforeTidy'][]                 = 'XBMCSkinHooks::ParserBeforeTidy';

// Temporary - remove XBMC skin from non-admin preferences
$wgHooks['BeforePageDisplay'][]                = 'XBMCSkinHooks::BeforePageDisplay';

// skins/xbmc/css/xbmc.css is where the interface CSS is placed. In some places,
// like the main page, custom CSS is used to improve the rendered wiki text.
// Because page content can change, it is desirable to allow this CSS to be
// flexible as well. By placing the CSS in a message, we can allow an admin to
// modify the CSS without touching any skin files.
//
// Same goes for the navigation menu - it is placed in a message so that an
// admin can modify the links without modifying and files.
$dir = dirname( __FILE__ ) . '/';
$wgExtensionMessagesFiles['XBMCSkin'] = $dir . 'XBMCSkin.i18n.php';


/**
 * XBMCSkinInit::init() is called when MediaWiki starts up.
 */
class XBMCSkinInit {
	/**
	 * Initializes and configures the extension.
	 */
	public static function init() {
		global $wgResourceModules;
		
		// Install skin modules
		$moduleInfo = array(
				'localBasePath' => $GLOBALS['wgStyleDirectory'],
				'remoteBasePath' => $GLOBALS['wgStylePath'],
				#'remoteExtPath' => 'XBMCSkin/modules',
		);
		
		$wgResourceModules['skins.xbmc'] = array(
				'styles' => array( 'xbmc/css/xbmc.css' => array( 'media' => 'screen' ) ),
				'scripts' => 'xbmc/js/xbmc.js',
		) + $moduleInfo;
		
		$wgResourceModules['paradise.styles'] = array(
				'styles' => array( 'xbmc/css/styles.css' => array( 'media' => 'screen' ) ),
		) + $moduleInfo;
		
		$wgResourceModules['paradise.dark'] = array(
				'styles' => array( 'xbmc/css/dark.css' => array( 'media' => 'screen' ) ),
		) + $moduleInfo;
		
		$wgResourceModules['paradise.custom'] = array(
				'styles' => array( 'xbmc/css/custom.css' => array( 'media' => 'screen' ) ),
		) + $moduleInfo;
	}
}

/**
 * Class to hold the hooks installed above. 
 *
 */
class XBMCSkinHooks {
	
	public static $headerCount = -1;
	
	/**
	 * Temporary - hide XBMC skin from non-admins.
	 */
	public static function BeforePageDisplay( &$out, &$skin ) {
		if ( $skin->getSkinName() != 'xbmc' )
			$out->addModules( 'xbmc.hide' );
		return true;
	}
	
	/**
	 * Modify the footer links - expunge all icons and hrefs, leaving only some
	 * simple statistics for the very bottom of the page.
	 */
	public static function SkinTemplateOutputPageBeforeExec( &$sk, &$tpl ) {
		global $wgUser;
		if ( $wgUser->getSkin()->getSkinName() == 'xbmc' ) {
			$tpl->set( 'footerlinks', array(
				'info' => array(
					'lastmod',
					'viewcount',
				),
			) );
		}
		return true;
	}
	
	/**
	 * Personal URLs are the links next to the user's name and talk page. Because
	 * we got rid of most links in the navigation bar, some are restored into
	 * the personal URLs toolbar.
	 */
	public static function PersonalUrls( &$personal_urls, &$title ) {
		global $wgUser, $wgRequest;
		if ( $wgUser->getSkin()->getSkinName() == 'xbmc' ) {
			// Navigation bar has its own logout link
			if ( isset( $personal_urls['logout'] ) ) {
				$personal_urls = array_slice( $personal_urls, 0, -1, true );
			}
			
			// Render an Upload file button
			$personal_urls['upload'] = array(
					'text'   => wfMsg( 'upload' ),
					'href'   => SpecialPage::getSafeTitleFor( 'Upload' )->getLocalURL(),
					'active' => $title->isSpecial( 'Upload' ),
			);
			
			$action = $wgRequest->getVal( 'action', 'view' );
			if( ( $title->getNamespace() != NS_SPECIAL ) && ( $action == 'view' || $action == 'purge' ) ) {
				// Render a Printable version button
				$personal_urls['printableversion'] = array(
						'text'   => wfMsg( 'printableversion' ),
						'href'   => $title->getLocalURL( $wgRequest->appendQueryValue( 'printable', 'yes', true ) ),
						'active' => false,
				);
				
				global $wgOut;
				$revid = $wgOut->getRevisionId();
				if ( $revid) {
					// Need to kill some space, so include the Permalink button
					$personal_urls['permalink'] = array(
							'text'   => wfMsg( 'permalink' ),
							'href'   => $title->getLocalURL( "oldid=$revid" ),
							'active' => false,
					);
				}
			}
		}
		return true;
	}
	
	/**
	 * Force the table of contents to at the top of the article. By default, it
	 * occurs in the page just before the first <h2> tag, which might be a couple
	 * <p> tags away from the beginning. Adding __TOC__ forces the table of
	 * contents to always be at the beginning; if __TOC__ is already specified by
	 * the page, it will be ignored. __NOTOC__ is still respected. If the TOC
	 * only has < 3 links, it will be removed again by XBMCTemplate::execute(),
	 * similar to MW's default behavior.
	 */
	public static function InternalParseBeforeLinks( Parser &$parser, &$text ) {
		global $wgUser;
		if ( $wgUser->getSkin()->getSkinName() == 'xbmc' ) {
			// Don't show TOC if NOTOC was specified
			if ( strpos( $text, '__NOTOC__' ) === false ) {
				$text = '__TOC__' . "\n" . $text;
			}
		}
		return true;
	}
	
	/**
	 * Move the [edit] button outside the <h2> tags.
	 * Looks like <h2><mw:editsection ...>Title</mw:editsection> <span>Title</span></h2>.
	 * 
	 * <mw:editsection ...>Title</mw:editsection> gets parsed into the edit button after
	 * this hook is called.
	 */
	public static function ParserBeforeTidy( &$parser, &$text ) {
		global $wgUser;
		if ( $wgUser->getSkin()->getSkinName() == 'xbmc' ) {
			$text = preg_replace( '/(<h[1-6]>)(<mw:editsection[^>]*>[^>]*>)(.*?)(<\/h[1-6]>)/', '<div>\2\1\3\4</div>', $text, -1, $count );
			// Count the maximum number of sections (a.k.a. headers) appearing in the article
			// We store the count as self::$headerCount. Don't worry, XBMCTemplate will come
			// looking.
			$matches = array();
			preg_match_all( '/<mw:editsection.*?section="([^"]*)"/', $text, $matches );
			
			foreach ( $matches[1] as $section ) {
				$section = intval( $section );
				if ( $section > self::$headerCount ) {
					self::$headerCount = $section;
				}
			}
		}
		return true;
	}
}
