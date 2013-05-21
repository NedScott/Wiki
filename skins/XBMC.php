<?php
/**
 * XBMC skin
 * 
 * MediaWiki skins generally consist of two classes. SkinXBMC contains skin
 * indentification and loads the necessary JS and CSS. SkinXBMC::var defines
 * the second class, the main template for the skin.
 * 
 * XBMCTemplate::execute() is where the page content is modified slightly and
 * rendered to the browser.
 * 
 * @file XBMC.php
 * @ingroup Skins
 * @version 1.0.0
 * @author Garrett Brown (garbearucla@gmail.com)
 * @license the "TeamXBMC Rocks" license
 * 
 * Scroll down a bunch to get to the HTML.
 */


if( !defined( 'MEDIAWIKI' ) ) {
	die( -1 );
}

/**
 * SkinTemplate class for XBMC skin.
 */
class SkinXBMC extends SkinTemplate {

	var $skinname = 'xbmc', $stylename = 'xbmc',
		$template = 'XBMCTemplate', $useHeadElement = true;

	/**
	 * Initializes output page and sets up skin-specific parameters.
	 */
	public function initPage( OutputPage $out ) {
		global $wgLocalStylePath, $wgRequest;

		parent::initPage( $out );

		// Append CSS which includes IE only behavior fixes for hover support -
		// this is better than including this in a CSS fille since it doesn't
		// wait for the CSS file to load before fetching the HTC file.
		$min = $wgRequest->getFuzzyBool( 'debug' ) ? '' : '.min';
		$out->addHeadItem( 'csshover',
			'<!--[if lt IE 7]><style type="text/css">body{behavior:url("' .
				htmlspecialchars( $wgLocalStylePath ) .
				"/{$this->stylename}/csshover{$min}.htc\")}</style><![endif]-->"
		);

		$out->addModuleScripts( 'skins.xbmc' );

		$out->addScriptFile( "$wgLocalStylePath/xbmc/js/cufon-yui.js" );
		$out->addScriptFile( "$wgLocalStylePath/xbmc/js/MgOpen_Modata_400.font.js" );
		$out->addScriptFile( "$wgLocalStylePath/xbmc/js/MgOpen_Modata_700.font.js" );
		
		$out->addScript(
"<script>
jQuery(document).ready(function($) {
    Cufon.replace('h1, h2, h3, h4, h5, h6, .big_btn', { fontFamily: 'MgOpen Modata', hover: true });
    Cufon.replace('.btn.big', { fontFamily: 'MgOpen Modata', color: '#fff', hover: true });
    Cufon.replace('#f_sidebar h3', { fontFamily: 'MgOpen Modata', color: '#fff', textShadow: '1px 1px rgba(0, 0, 0, 0.4)' });
    Cufon.replace('.error404, .s3sliderImage b.title');
});
</script>"
		);
	}

	/**
	 * Load skin and user CSS files in the correct order.
	 */
	function setupSkinUserCss( OutputPage $out ){
		parent::setupSkinUserCss( $out );
		$out->addModuleStyles( 'paradise.styles' );
		$out->addModuleStyles( 'paradise.dark' );
		$out->addModuleStyles( 'paradise.custom' );
		$out->addModuleStyles( 'skins.xbmc' );
	}
}

/**
 * Template class for XBMC skin.
 */
class XBMCTemplate extends BaseTemplate {
	/**
	 * @var Skin Cached skin object
	 */
	var $skin;
	
	/**
	 * Outputs the entire contents of the (X)HTML page
	 */
	public function execute() {
		global $wgLocalStylePath;

		$this->skin = $this->data['skin'];
		
		$this->buildNavUrls();
		
		// Reverse horizontally rendered navigation elements
		$this->fixRTL();
		
		// Add the #sp ID to the <body> tag (needed for Paradise CSS files)
		$this->data['headelement'] = str_replace( '<body ', '<body id="sp" ', $this->data['headelement'] );
		
		// Remove TOC if we have less than 3 headers
		if ( XBMCSkinHooks::$headerCount < 3 ) {
			$this->data['bodycontent'] = preg_replace( '/<table[^=]*="toc"[^>]*>/',
					'<table id="toc" class="toc" style="display:none;">', $this->data['bodycontent'] );
		}
		
		// Fix the categories link toolbar
		$this->fixCatlinks();
		
		// Rebuild the sidebar using the 'xbmc-sidebar' wiki message instead of the default
		// 'sidebar' message. This lets us have separate navigation links for Vector and the
		// XBMC skin
		$this->set( 'sidebar', $this->buildSidebar() );
		
		// Output the head element and first <body> tag
		$this->html( 'headelement' );
		
?>
		<!-- content -->
		<div class="container">
			<div id="header">
				<a href="http://xbmc.org" class="logo">
					<img src="<?php echo $wgLocalStylePath ?>/xbmc/images/logo.png" alt="">
				</a>
				<form role="search" method="get" id="searchform" action="http://www.google.com/cse">
					<div>
						<input type="hidden" name="cx" value="003239339731796940873:16ru8erxpls">
						<input type="hidden" name="ie" value="UTF-8">
						<label class="screen-reader-text" for="s">Search for:</label>
						<input type="text" value="" name="q" id="s" <?php /*style="color: rgb(178, 177, 177); "*/?>>
						<input type="submit" name="sa" id="searchsubmit" value="Search">
					</div>
				</form>
				<div class="clear"></div>
			</div>
			<div id="MainNav">
				<a href="http://xbmc.org" class="home_btn">
					<img src="<?php echo $wgLocalStylePath ?>/xbmc/images/icon_home.gif" width="17" height="19" alt="Home">
				</a>
				<div id="menu" class="ddsmoothmenu">
					<ul id="menu-main-menu" class="ddsmoothmenu">
						<li id="menu-item-671" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-671">
							<a href="http://www.xbmc.org/home/">About</a>
						</li>
						<li id="menu-item-732" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-732">
							<a href="http://www.xbmc.org/download/">Download</a>
						</li>
					<!--<li id="menu-item-738" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-738">
							<a href="http://addons.xbmc.org/">Add-Ons</a> 
						</li>-->
						<li id="menu-item-734" class="menu-item menu-item-type-custom menu-item-object-custom current-menu-item page_item page-item-2 current_page_item menu-item-734">
							<a href="<?php echo Title::newMainPage()->getLocalURL(); ?>">Wiki</a>
						</li>
						<li id="menu-item-733" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-733">
							<a href="http://forum.xbmc.org">Forum</a>
						</li>
						<li id="menu-item-811" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-811">
							<a href="http://www.xbmc.org/Contribute/Donate">Donate</a>
						</li>
					</ul>
				</div>
				<div class="clear"></div>
			</div>
			<!-- navigation -->
			<div id="breadcrumbs">
				<div id="mw-panel" class="noprint">
					<?php $this->renderPortals( $this->data['sidebar'] ); ?>
				</div>
				<div id="mw-panel2" class="noprint">
					<?php $this->renderPersonalNavigation(); ?>
				</div>
			</div>
			<!-- /navigation -->
			<a id="top"></a>
			<div class="PageTitle">
				<h1><?php $this->html( 'title' ) ?></h1>
			</div>
			<div id="right-navigation-wrapper">
				<div id="right-navigation">
					<?php $this->renderNavigation( array( 'NAMESPACES', 'VIEWS', 'ACTIONS' ) ); ?>
				</div>
			</div>
			<div id="content_wrapper">
				<div id="content"><div class="box">
					<div id="mw-js-message" style="display:none;"<?php $this->html( 'userlangattributes' ) ?>></div>
					<?php if ( $this->data['subtitle'] ): ?>
					<!-- subtitle -->
					<div id="contentSub"><?php $this->html( 'subtitle' ) ?></div>
					<!-- /subtitle -->
					<?php endif; ?>
					
					<?php if ( $this->data['undelete'] ): ?>
					<!-- undelete -->
					<div id="contentSub2"><?php $this->html( 'undelete' ) ?></div>
					<!-- /undelete -->
					<?php endif; ?>
					
					<?php if( $this->data['newtalk'] ): ?>
					<!-- newtalk -->
					<div class="usermessage"><?php $this->html( 'newtalk' )  ?></div>
					<!-- /newtalk -->
					<?php endif; ?>
					
					<!-- bodycontent -->
					<div id="mw-content-article">
						<?php $this->html( 'bodycontent' ) ?>
					</div>
					<!-- /bodycontent -->
					
					<?php if ( $this->data['catlinks'] ): ?>
					<!-- catlinks -->
					<?php $this->html( 'catlinks' ); ?>
					<!-- /catlinks -->
					<?php endif; ?>
					
					<?php if ( $this->data['dataAfterContent'] ): ?>
					<!-- dataAfterContent -->
					<?php $this->html( 'dataAfterContent' ); ?>
					<!-- /dataAfterContent -->
					<?php endif; ?>
					
					<div class="visualClear"></div>
					<!-- debughtml -->
					<?php $this->html( 'debughtml' ); ?>
					<!-- /debughtml -->
					
					<!-- footer -->
					<div id="mw-footer"<?php $this->html( 'userlangattributes' ) ?>>
						<?php foreach( $this->getFooterLinks() as $category => $links ): ?>
							<ul id="mw-footer-<?php echo $category ?>">
								<?php foreach( $links as $link ): ?>
									<li id="mw-footer-<?php echo $category ?>-<?php echo $link ?>"><?php $this->html( $link ) ?></li>
								<?php endforeach; ?>
							</ul>
						<?php endforeach; ?>
						<div style="clear:both"></div>
					</div>
					<!-- /footer -->
				</div></div>
				<div class="clear"></div>
			</div>
			<!-- Start Footer Sidebar -->
			<div id="f_sidebar">
				<div class="sb_wrapper">
					<!-- Start First Column -->
					<div class="widget-container widget_text">
						<h3>About XBMC</h3>
						<p>XBMC is a free and open source media player application developed by the XBMC Foundation, a non-profit technology consortium. XBMC is available for multiple operating-systems and hardware platforms, featuring a 10-foot user interface for use with televisions and remote controls. It allows users to play and view most videos, music, podcasts, and other digital media files from local and network storage media and the internet.</p>
					</div>
					<!-- Start First Column -->
					<div class="widget-container">
						<h3>Internal Links</h3>
						<ul>
							<li><a href="#" title="Contribute">Contribute</a></li>
							<li><a href="#" title="Corporate Enquiries">Corporate</a></li>
							<li><a href="#" title="XBMC Foundation">XBMC Foundation</a></li>
							<li><a href="#" title="XBMC Software">XBMC Software</a></li>
							<li><a href="#" title="XBMC Team">XBMC Team</a></li>
						</ul>
					</div>
					<!-- Start Second Column -->
					<div class="widget-container">
					<h3>Feeds</h3>
						<ul>
							<li><a href="http://addons.xbmc.org/rssupdated.php" title="At-Visons">Latest Add-Ons</a></li>
							<li><a href="http://www.xbmc.org/comments/feed/" title="Comments RSS">Latest Comments</a></li>
							<li><a href="http://www.xbmc.org/feed/" title="News RSS">Latest News</a></li>
							<li><a href="http://addons.xbmc.org/rssnewest.php" title="Newest RSS">Newest Add-Ons</a></li>
						</ul>
					</div>
					
					<!-- Start Third Column -->
					<div class="widget-container">
					<h3>Sponsors</h3>
						<ul>
							<li><a href="http://www.at-visions.com" title="At-Visons">at-Visons</a></li>
							<li><a href="http://www.ouya.tv/" title="Ouya">Ouya</a></li>
							<li><a href="http://www.pivosgroup.com/" title="PivosConvar">Pivos</a></li>
							<li><a href="http://www.vidon.me/" title="VidOn.Me">VidOn.Me</a></li>
							<li><a href="http://www.webhostingbuzz.com/" title="WebHostingBuzz">WebHostingBuzz</a></li>
							<li><a href="http://www.wunderground.com/" title="wunderground">wunderground</a></li>
						</ul>
					</div>
					<!-- Start 4th Collumn -->
					<div class="widget-container footer-widget-last">
						<h3>External Links</h3>
						<ul>
							
							<li><a href="http://fanart.tv" title="Fanart.tv">Fanart.TV</a></li>
							<li><a href="http://openelec.tv/" title="OpenELEC">OpenELEC</a></li>
							<li><a href="www.TheAudioDB.com" title="April 2010">TheAudioDB.com</a></li>
							<li><a href="www.TheGamesDB.net" title="TheGamesDB">TheGamesDB.net</a></li>
							<li><a href="www.TheMovieDB.org" title="TheMovieDB">TheMovieDB.org</a></li>
							<li><a href="www.TheTVDB.com" title="TheTVDB">TheTVDB.com</a></li>
							<li><a href="www.xbmlogs.net" title="XBMCLogs">XBMCLogs.com</a></li>
						</ul>
					</div>
				</div>
				<div class="clear"></div>
			</div>
			<!-- End Footer Sidebar -->
		</div>
		<!-- /content -->
		<!-- fixalpha -->
		<script type="<?php $this->text( 'jsmimetype' ) ?>"> if ( window.isMSIE55 ) fixalpha(); </script>
		<!-- /fixalpha -->
		<?php $this->printTrail(); ?>
	</body>
</html>
<?php
	}
	
	/*
	 * End of easily-modifiable HTML
	 */

	/**
	 * Build additional attributes for navigation urls
	 */
	private function buildNavUrls() {
		global $wgVectorUseIconWatch;
		//$nav = $this->skin->buildNavigationUrls();
		$nav = $this->data['content_navigation'];
		
		if ( $wgVectorUseIconWatch ) {
			$mode = $this->skin->getTitle()->userIsWatching() ? 'unwatch' : 'watch';
			if ( isset( $nav['actions'][$mode] ) ) {
				$nav['views'][$mode] = $nav['actions'][$mode];
				$nav['views'][$mode]['class'] = rtrim( 'icon ' . $nav['views'][$mode]['class'], ' ' );
				$nav['views'][$mode]['primary'] = true;
				unset( $nav['actions'][$mode] );
			}
		}
		
		$xmlID = '';
		foreach ( $nav as $section => $links ) {
			foreach ( $links as $key => $link ) {
				if ( $section == 'views' && !( isset( $link['primary'] ) && $link['primary'] ) ) {
					$link['class'] = rtrim( 'collapsible ' . $link['class'], ' ' );
				}
		
				$xmlID = isset( $link['id'] ) ? $link['id'] : 'ca-' . $xmlID;
				$nav[$section][$key]['attributes'] =
				' id="' . Sanitizer::escapeId( $xmlID ) . '"';
				if ( $link['class'] ) {
					$nav[$section][$key]['attributes'] .=
					' class="' . htmlspecialchars( $link['class'] ) . '"';
					unset( $nav[$section][$key]['class'] );
				}
				if ( isset( $link['tooltiponly'] ) && $link['tooltiponly'] ) {
					$nav[$section][$key]['key'] =
					Linker::tooltip( $xmlID );
				} else {
					$nav[$section][$key]['key'] =
					Xml::expandAttributes( Linker::tooltipAndAccesskeyAttribs( $xmlID ) );
				}
			}
		}
		$this->data['namespace_urls'] = $nav['namespaces'];
		$this->data['view_urls'] = $nav['views'];
		$this->data['action_urls'] = $nav['actions'];
		$this->data['variant_urls'] = $nav['variants'];
	}

	/**
	 * Redo the category links. Currently, the "categories" hyperlink is an <a>
	 * tag outside of the categories <ul> list. This code inserts the
	 * categories hyperlink as a <li> item, and if the page has at least 1
	 * header, adds an "Add category" button.
	 * 
	 * The "Add category" button simply links to the edit page, with the last
	 * header as an edit target (this is where category links go). If there
	 * is no header, we skip the Add button because A) there's no target to
	 * jump to and B) if there's no header, it might not be a page that should
	 * use categories.
	 */
	private function fixCatlinks() {
		
		$match = array();

		$html = $this->data['catlinks'];
		
		$canEdit = $this->getSkin()->getTitle()->userCan( 'edit', false );
		$hasHeaders = (XBMCSkinHooks::$headerCount > 0);
		
		if ( $html != '' ) {
			// Link to Special:Categories
			$catLink = '<li class="categories-link"><a href="' . Skin::makeSpecialUrl( 'Categories' ) .
			'" title="Special:Categories">' . wfMsg( 'categories' ) . '</a></li>';
			
			// Get categories
			$matches = array();
			$count = preg_match_all( '/<li>.*?<\/li>/', $html, $matches );
			if ( $count ) {
				$categories = $matches[0];
			} else {
				// If there's no categories, don't show the categories bar unless we can edit and there are headers
				if ( $canEdit  && $hasHeaders ) {
					$categories = array( '<li class="categories-none"><span>' . wfMsg( 'qbsettings-none' ) . '</span></li>' );
				} else {
					return;
				}
			}
			
			// Create Add New (+) link
			$addNew = '';
			if ( $canEdit ) {
				// Make an Add button to easily add a category. This links to the edit page, automatically pulling
				// up the last heading found in the table of contents.
				if ( $hasHeaders ) {
					$url = $this->skin->getTitle()->getLocalURL( 'action=edit&section=' . XBMCSkinHooks::$headerCount );
					$addNew = '<li class="add-category"><a href="' . $url . '" title="Add a category">+</a></li></ul>';
				}
			}
			
			// Merge the results
			$this->data['catlinks'] = '<div id="xbmc-catlinks" class="xbmc-catlinks"><div id="catlinks-wrapper"><ul>' .
				implode( array_merge( array( $catLink ),  $categories, array( $addNew ) ) ) . '</ul></div></div>';
		}
	}
	
	/**
	 * Build an array that represents the sidebar(s), the navigation bar among them
	 *
	 * @return array
	 */
	function buildSidebar() {
		global $parserMemc, $wgEnableSidebarCache, $wgSidebarCacheExpiry;
		
		$key = wfMemcKey( 'xbmc-sidebar', $this->skin->getLang()->getCode() );
		
		if ( $wgEnableSidebarCache ) {
			$cachedsidebar = $parserMemc->get( $key );
			if ( $cachedsidebar ) {
				wfProfileOut( __METHOD__ );
				return $cachedsidebar;
			}
		}
		
		$bar = array();
		$this->skin->addToSidebar( $bar, 'xbmc-sidebar' );
		
		if ( $wgEnableSidebarCache ) {
			$parserMemc->set( $key, $bar, $wgSidebarCacheExpiry );
		}
		
		return $bar;
	}
	
	/**
	 * Render a series of portals
	 *
	 * @param $portals array
	 */
	private function renderPortals( $portals ) {
		// Render portals
		foreach ( $portals as $name => $content ) {
			if ( $content === false )
				continue;
			$this->renderPortal( $name, $content );
		}
	}

	private function renderPortal( $name, $content, $msg = null, $hook = null ) {
		if ( !isset( $msg ) ) {
			$msg = $name;
		}
		if ( $name == 'navigation' ) {
			$this->renderNavigationPortal( $content );
			return;
		}
		
		?>
	<div class="portal" id='<?php echo Sanitizer::escapeId( "p-$name" ) ?>'<?php echo Linker::tooltip( 'p-' . $name ) ?>>
		<div class="body">
	<?php
			if ( is_array( $content ) ): ?>
			<ul>
	<?php
				foreach( $content as $key => $val ): ?>
				<?php echo $this->makeListItem( $key, $val ); ?>
	
	<?php
				endforeach;
				if ( isset( $hook ) ) {
					wfRunHooks( $hook, array( &$this, true ) );
				}
				?>
			</ul>
	<?php
			else: ?>
			<?php echo $content; /* Allow raw HTML block to be defined by extensions */ ?>
	<?php
			endif; ?>
		</div>
	</div>
	<?php
		}

	private function renderNavigationPortal( $content ) {
		global $wgUser;
		?>
	<?php
		if ( true ): ?>
		<table id="<?php echo Sanitizer::escapeId( "p-navigation" ) ?>"><tr>
<?php
			$i = 0;
			foreach( $content as $key => $val ): ?>
			<?php
				$item = $this->makeListItem( $key, $val );
				if ( $i == 0 )
					$item = preg_replace('/^<li[^>]*>/', '<td class="breadcrumb-nav-first">', $item, 1);
				else if ( $i + 1 == count( $content ) && $wgUser->isLoggedIn() ) {
					// Only add .breadcrumb-nav-last if we're logged in
					$item = preg_replace('/^<li[^>]*>/', '<td class="breadcrumb-nav-last">', $item, 1);
				}
					$item = preg_replace('/^<li[^>]*>/', '<td>', $item, 1);
				$item = preg_replace('/<\/li>$/', '</td>', $item, 1);
				$first = false;
				echo $item;
				$i++;
			?>
<?php
			endforeach;
			
			$returnto = $this->getReturnToURL();
			if ($wgUser->isLoggedIn()) {
				$isPrefs = $this->skin->getTitle()->isSpecial( 'Preferences' );
				$logoutURL = Skin::makeSpecialUrl( 'Userlogout', $isPrefs ? 'noreturnto' : $returnto);
				echo '<td id="nav-control-panel"><a href="#" title="User control panel">' .
				     '<span class="control-button-down">Control panel</span></a></td>';
				echo '<td id="nav-logout"><a href="' . $logoutURL . '">' . wfMsg( 'userlogout' ) . '</a></td>';
			} else {
				$loginURL = $this->skin->makeSpecialUrl( 'Userlogin', $returnto );
				$createURL = $this->skin->makeSpecialUrl( 'Userlogin', "$returnto&type=signup" );
				
				global $wgServer, $wgSecureLogin;
				if( substr( $wgServer, 0, 5 ) === 'http:' && $wgSecureLogin ) {
					$title = SpecialPage::getTitleFor( 'Userlogin' );
					$loginURL = preg_replace( '/^http:/', 'https:', $title->getFullURL() );
					$createURL = preg_replace( '/^http:/', 'https:', $title->getFullURL("type=signup") );
				}
				
				echo '<td id="nav-login"><a href="' . $loginURL . '">'. wfMsg( 'login' ) . '</a></td>';
				if ( $wgUser->isAllowed( 'createaccount' ) ) {
					echo '<td id="nav-createaccount"><a href="' . $createURL . '">'. wfMsg( 'createaccount' ) . '</a></td>';
				}
			}
			?>
		</tr></table>
<?php
		endif; ?>
	<?php
	}
	
	private function getReturnToURL() {
		global $wgRequest;
		$page = Title::newFromURL( $wgRequest->getVal( 'title', '' ) );
		$page = $wgRequest->getVal( 'returnto', $page );
		
		$query = array();
		if ( !$wgRequest->wasPosted() ) {
			$query = $wgRequest->getValues();
			unset( $query['title'] );
			unset( $query['returnto'] );
			unset( $query['returntoquery'] );
		}
		$a = array();
		if ( strval( $page ) !== '' ) {
			$a['returnto'] = $page;
			$query = $wgRequest->getVal( 'returntoquery', wfArrayToCGI( $query ) );
			if( $query != '' ) {
				$a['returntoquery'] = $query;
			}
		}
		$returnto = wfArrayToCGI( $a );
		return $returnto;
	}
	
	/**
	 * Render one or more navigations elements by name, automatically reveresed
	 * when UI is in RTL mode
	 */
	private function renderNavigation( $elements ) {
		global $wgVectorUseSimpleSearch, $wgVectorShowVariantName, $wgUser, $wgLang;

		// If only one element was given, wrap it in an array, allowing more
		// flexible arguments
		if ( !is_array( $elements ) ) {
			$elements = array( $elements );
		// If there's a series of elements, reverse them when in RTL mode
		} elseif ( $wgLang->isRTL() ) {
			$elements = array_reverse( $elements );
		}
		// Render elements
		foreach ( $elements as $name => $element ) {
			echo "\n<!-- {$name} -->\n";
			switch ( $element ) {
				case 'NAMESPACES':
					$hasFriends = (count( $this->data['view_urls'] ) != 0 || count( $this->data['action_urls'] ) != 0);
?>
<div id="p-namespaces" class="vectorTabs<?php if ( $hasFriends ) echo ' p-namespaces-has-friends'; ?>">
	<h5><?php $this->msg( 'namespaces' ) ?></h5>
	<ul<?php $this->html( 'userlangattributes' ) ?>>
		<?php foreach ( $this->data['namespace_urls'] as $link ): ?>
			<li <?php echo $link['attributes'] ?>><span><a href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?>><?php echo htmlspecialchars( $link['text'] ) ?></a></span></li>
		<?php endforeach; ?>
	</ul>
</div>
<?php
				break;
				case 'VARIANTS':
?>
<div id="p-variants" class="vectorMenu<?php if ( count( $this->data['variant_urls'] ) == 0 ) echo ' emptyPortlet'; ?>">
	<?php if ( $wgVectorShowVariantName ): ?>
		<h4>
		<?php foreach ( $this->data['variant_urls'] as $link ): ?>
			<?php if ( stripos( $link['attributes'], 'selected' ) !== false ): ?>
				<?php echo htmlspecialchars( $link['text'] ) ?>
			<?php endif; ?>
		<?php endforeach; ?>
		</h4>
	<?php endif; ?>
	<h5><span><?php $this->msg( 'variants' ) ?></span><a href="#"></a></h5>
	<div class="menu">
		<ul<?php $this->html( 'userlangattributes' ) ?>>
			<?php foreach ( $this->data['variant_urls'] as $link ): ?>
				<li<?php echo $link['attributes'] ?>><a href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?>><?php echo htmlspecialchars( $link['text'] ) ?></a></li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
<?php
				break;
				case 'VIEWS':
					if ( count( $this->data['view_urls'] ) ) {
?>
<div id="p-views" class="vectorTabs<?php if ( count( $this->data['view_urls'] ) == 0 ) { echo ' emptyPortlet'; } ?>">
	<h5><?php $this->msg('views') ?></h5>
	<ul<?php $this->html('userlangattributes') ?>>
		<?php foreach ( $this->data['view_urls'] as $link ): ?>
			<li<?php echo $link['attributes'] ?>><span><a href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?>><?php
				// $link['text'] can be undefined - bug 27764
				if ( array_key_exists( 'text', $link ) ) {
					echo array_key_exists( 'img', $link ) ?  '<img src="' . $link['img'] . '" alt="' . $link['text'] . '" />' : htmlspecialchars( $link['text'] );
				}
				?></a></span></li>
		<?php endforeach; ?>
	</ul>
</div>
<?php
					}
				break;
				case 'ACTIONS':
					if ( count( $this->data['action_urls'] ) ) {
?>
<div id="p-cactions" class="vectorMenu<?php if ( count( $this->data['action_urls'] ) == 0 ) echo ' emptyPortlet'; ?>">
	<h5><span><?php $this->msg( 'actions' ) ?></span><a href="#"></a></h5>
	<div class="menu">
		<ul<?php $this->html( 'userlangattributes' ) ?>>
			<?php foreach ( $this->data['action_urls'] as $link ): ?>
				<li<?php echo $link['attributes'] ?>><a href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?>><?php echo htmlspecialchars( $link['text'] ) ?></a></li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>
<?php
					}
				break;
				case 'PERSONAL':
?>
<div id="p-personal" class="<?php if ( count( $this->data['personal_urls'] ) == 0 ) echo ' emptyPortlet'; ?>">
	<h5><?php $this->msg( 'personaltools' ) ?></h5>
	<ul<?php $this->html( 'userlangattributes' ) ?>>
<?php			foreach( $this->getPersonalTools() as $key => $item ) { ?>
		<?php echo $this->makeListItem( $key, $item ); ?>

<?php			} ?>
	</ul>
</div>
<?php
				break;
				case 'SEARCH':
?>
<div id="p-search">
	<h5<?php $this->html( 'userlangattributes' ) ?>><label for="searchInput"><?php $this->msg( 'search' ) ?></label></h5>
	<form action="<?php $this->text( 'wgScript' ) ?>" id="searchform">
		<input type='hidden' name="title" value="<?php $this->text( 'searchtitle' ) ?>"/>
		<?php if ( $wgVectorUseSimpleSearch && $wgUser->getOption( 'vector-simplesearch' ) ): ?>
		<div id="simpleSearch">
			<?php if ( $this->data['rtl'] ): ?>
			<?php echo $this->makeSearchButton( 'image', array( 'id' => 'searchButton', 'src' => $this->skin->getSkinStylePath( 'images/search-rtl.png' ) ) ); ?>
			<?php endif; ?>
			<?php echo $this->makeSearchInput( array( 'id' => 'searchInput', 'type' => 'text' ) ); ?>
			<?php if ( !$this->data['rtl'] ): ?>
			<?php echo $this->makeSearchButton( 'image', array( 'id' => 'searchButton', 'src' => $this->skin->getSkinStylePath( 'images/search-ltr.png' ) ) ); ?>
			<?php endif; ?>
		</div>
		<?php else: ?>
		<?php echo $this->makeSearchInput( array( 'id' => 'searchInput' ) ); ?>
		<?php echo $this->makeSearchButton( 'go', array( 'id' => 'searchGoButton', 'class' => 'searchButton' ) ); ?>
		<?php echo $this->makeSearchButton( 'fulltext', array( 'id' => 'mw-searchButton', 'class' => 'searchButton' ) ); ?>
		<?php endif; ?>
	</form>
</div>
<?php

				break;
			}
			echo "\n<!-- /{$name} -->\n";
		}
	}
	

	/**
	 * Render one or more navigations elements by name, automatically reveresed
	 * when UI is in RTL mode
	 */
	private function renderPersonalNavigation() {
		global $wgVectorUseSimpleSearch, $wgVectorShowVariantName, $wgUser, $wgLang;
		
		$personals = $this->getPersonalTools();
		$i = 0;
?>
<table id="p-personal">
	<tr>
<?php	foreach( $personals as $key => $item ) {
			$listItem =  $this->makeListItem( $key, $item ); 
			
			if ( $i == 0 ) {
			$listItem = preg_replace('/^<li[^>]*>/', '<td class="breadcrumb-personal-first">', $listItem, 1);
			} else if ( $i + 1 == count( $personals ) ) {
				$listItem = preg_replace('/^<li[^>]*>/', '<td class="breadcrumb-personal-last">', $listItem, 1);
			} else {
				$listItem = preg_replace('/^<li[^>]*>/', '<td>', $listItem, 1);
			}
			$listItem = preg_replace('/<\/li>$/', '</td>', $listItem, 1);
			
			echo $listItem;
			
			$i++;
		} ?>
	</tr>
</table>
<?php
	}
	
	/**
	 * Reverse horizontally rendered navigation elements.
	 */
	private function fixRTL() {
		global $wgLang;
		if ( $wgLang->isRTL() ) {
			$this->data['view_urls'] =
			array_reverse( $this->data['view_urls'] );
			$this->data['namespace_urls'] =
			array_reverse( $this->data['namespace_urls'] );
			$this->data['personal_urls'] =
			array_reverse( $this->data['personal_urls'] );
		}
	}
}
