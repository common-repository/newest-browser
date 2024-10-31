<?php
/*
    Plugin Name: Newest Browser
    Plugin URI: http://www.chriswashington.net/projects/newest-browser-for-wordpress
    Description: Encourage upgrading to the lastest in technology and security by displaying updates for major browsers your visitor is using, or promote a browser your of choice.  Updated are extracted from <a href="http://www.newestbrowser.com/" title="Updates for your browser" target="_blank">NewestBrowser.com</a>.
    Version: 1.0
	Author: CW Web Solutions LLC
    Author URI: http://www.chriswashington.net/
    Licence: Plugin is released under GPL: http://www.opensource.org/licenses/gpl-license.php

	Newest Browser Wordpress Plugin - shows updated version to user's browser or site owner's promoted browser of choice
    Copyright (C) 2010  CW Web Solutions LLC

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
	
    This program comes with ABSOLUTELY NO WARRANTY; for details type `show w'.
    This is free software, and you are welcome to redistribute it
    under certain conditions; type `show c' for details.
*/

// REGISTER FUNCTIONS IF ADMIN
if(is_admin()) {
	add_action('admin_menu', 'NewestBrowser_MenuItem');
}
// REGISTER HEADER FUNCTION FOR CSS STYLING
add_action('wp_head', 'NewestBrowser_head');
// REGISTER WIDGET
register_sidebar_widget('Newest Browser', 'widget_NewestBrowser');
// REGISTER SHORTCODE [NewestBrowser] THAT CAN BE USED IN POST/PAGES TO EXECUTE PLUGIN
add_shortcode('NewestBrowser', 'NewestBrowser');
// REGISTER UNINSTALL FUNCTION
register_uninstall_hook(__FILE__, 'NewestBrowser_uninstall_hook');


// EXECUTE PLUGIN
function NewestBrowser() {
	// FIRST SEE IF YOU HAVE TODAY'S VERION FROM REMOTE XML FILE, IF NOT GO GET IT ONLY ONCE A DAY
	if(get_option('lastupdated') == "" || get_option('lastupdated') != date('m/d/Y')) {
		$strPass = 'HTTP/1.1 200 OK';
		$strVersion = "http://www.newestbrowser.com/versions.xml";
		$arrayResults = get_headers($strVersion);
		$strXML = simplexml_load_file($strVersion);
		if ($arrayResults[0] == $strPass && $strXML != "") {
			// LOOP THROUGH XML TO GET ALL DATA THEN SAVE INTO DATABASE
			foreach($strXML->children() as $child) {
				foreach($child->children() as $grandchild) {
					switch($grandchild->getName()) {
						case "OS":
							$strOS = trim($grandchild);
							break;
						case "Browser":
							$strBrowser = trim($grandchild);
							break;
						case "Version":
							$strVersion = trim($grandchild);
							break;
					};
				}
				// WHATEVER READ, STORE IN DATABASE
				if($strOS == "Windows" && $strBrowser == "Internet Explorer") update_option('win-ie', $strVersion);
				if($strOS == "Windows" && $strBrowser == "Opera") update_option('win-opera', $strVersion);
				if($strOS == "Windows" && $strBrowser == "Safari") update_option('win-safari', $strVersion);
				if($strOS == "Windows" && $strBrowser == "Firefox") update_option('win-ff', $strVersion);
				if($strOS == "Windows" && $strBrowser == "Chrome") update_option('win-chrome', $strVersion);
				if($strOS == "Macintosh" && $strBrowser == "Safari") update_option('mac-safari', $strVersion);
				if($strOS == "Macintosh" && $strBrowser == "Firefox") update_option('mac-ff', $strVersion);
				if($strOS == "Macintosh" && $strBrowser == "Chrome") update_option('mac-chrome', $strVersion);
				if($strOS == "Macintosh" && $strBrowser == "Opera") update_option('mac-opera', $strVersion);
			}
			// SAVE TODAYS DATE IN DATABASE FOR PLUGIN
			update_option('lastupdated', date('m/d/Y'));
		}
	}

	// CHOOSE WHICH OPTION IS SELECTED FROM PLUGIN SETTINGS, THEN DISPLAY THE BROWSER AND VERSION TO UPGRADE TO
	$strUserAgent = $_SERVER['HTTP_USER_AGENT'];
    switch(get_option('promote')) {
		case "Internet Explorer":
			echo "<div id=\"cssNewestBrowser\"><a href=\"http://www.microsoft.com/windows/internet-explorer/worldwide-sites.aspx\" target=\"_blank\" rel=\"nofollow\"><img src=\"" . WP_PLUGIN_URL . "/newestbrowser/images/icon-internet-explorer.png" . "\" /> Upgrade to version " . get_option('win-ie') . "<br /><a href=\"http://www.chriswashington.net/projects/newest-browser-for-wordpress\" title=\"Newest Browser module powered by CW Web Solutions LLC\" target=\"_blank\"><img src=\"" . WP_PLUGIN_URL . "/newestbrowser/images/icon.png" . "\" style=\"float:right;\" /></a></div>";
			break;
		case "Opera":
			echo "<div id=\"cssNewestBrowser\"><a href=\"http://www.opera.com/browser/download/\" target=\"_blank\" rel=\"nofollow\"><img src=\"" . WP_PLUGIN_URL . "/newestbrowser/images/icon-opera.png" . "\" /> Upgrade to version ";
			if(strlen(strstr(strtolower($strUserAgent),"windows")) > 0)
				echo get_option('win-opera') . "<br /><a href=\"http://www.chriswashington.net/projects/newest-browser-for-wordpress\" title=\"Newest Browser module powered by CW Web Solutions LLC\" target=\"_blank\"><img src=\"" . WP_PLUGIN_URL . "/newestbrowser/images/icon.png" . "\" style=\"float:right;\" /></a></div>";
			else
				echo get_option('mac-opera') . "<br /><a href=\"http://www.chriswashington.net/projects/newest-browser-for-wordpress\" title=\"Newest Browser module powered by CW Web Solutions LLC\" target=\"_blank\"><img src=\"" . WP_PLUGIN_URL . "/newestbrowser/images/icon.png" . "\" style=\"float:right;\" /></a></div>";
			break;
		case "Safari":
			echo "<div id=\"cssNewestBrowser\"><a href=\"http://www.apple.com/safari/download/\" target=\"_blank\" rel=\"nofollow\"><img src=\"" . WP_PLUGIN_URL . "/newestbrowser/images/icon-safari.png" . "\" /> Upgrade to version ";
			if(strlen(strstr(strtolower($strUserAgent),"windows")) > 0)
				echo get_option('win-safari') . "<br /><a href=\"http://www.chriswashington.net/projects/newest-browser-for-wordpress\" title=\"Newest Browser module powered by CW Web Solutions LLC\" target=\"_blank\"><img src=\"" . WP_PLUGIN_URL . "/newestbrowser/images/icon.png" . "\" style=\"float:right;\" /></a></div>";
			else
				echo get_option('mac-safari') . "<br /><a href=\"http://www.chriswashington.net/projects/newest-browser-for-wordpress\" title=\"Newest Browser module powered by CW Web Solutions LLC\" target=\"_blank\"><img src=\"" . WP_PLUGIN_URL . "/newestbrowser/images/icon.png" . "\" style=\"float:right;\" /></a></div>";
			break;
		case "Firefox":
			echo "<div id=\"cssNewestBrowser\"><a href=\"http://www.mozilla.com/en-US/firefox/all.html\" target=\"_blank\" rel=\"nofollow\"><img src=\"" . WP_PLUGIN_URL . "/newestbrowser/images/icon-firefox.png" . "\" /> Upgrade to version ";
			if(strlen(strstr(strtolower($strUserAgent),"windows")) > 0)
				echo get_option('win-ff') . "<br /><a href=\"http://www.chriswashington.net/projects/newest-browser-for-wordpress\" title=\"Newest Browser module powered by CW Web Solutions LLC\" target=\"_blank\"><img src=\"" . WP_PLUGIN_URL . "/newestbrowser/images/icon.png" . "\" style=\"float:right;\" /></a></div>";
			else
				echo get_option('mac-ff') . "<br /><a href=\"http://www.chriswashington.net/projects/newest-browser-for-wordpress\" title=\"Newest Browser module powered by CW Web Solutions LLC\" target=\"_blank\"><img src=\"" . WP_PLUGIN_URL . "/newestbrowser/images/icon.png" . "\" style=\"float:right;\" /></a></div>";
			break;
		case "Chrome":
			echo "<div id=\"cssNewestBrowser\"><a href=\"http://www.google.com/chrome\" target=\"_blank\" rel=\"nofollow\"><img src=\"" . WP_PLUGIN_URL . "/newestbrowser/images/icon-chrome.png" . "\" /> Upgrade to version ";
			if(strlen(strstr(strtolower($strUserAgent),"windows")) > 0)
				echo get_option('win-chrome') . "<br /><a href=\"http://www.chriswashington.net/projects/newest-browser-for-wordpress\" title=\"Newest Browser module powered by CW Web Solutions LLC\" target=\"_blank\"><img src=\"" . WP_PLUGIN_URL . "/newestbrowser/images/icon.png" . "\" style=\"float:right;\" /></a></div>";
			else
				echo get_option('mac-chrome') . "<br /><a href=\"http://www.chriswashington.net/projects/newest-browser-for-wordpress\" title=\"Newest Browser module powered by CW Web Solutions LLC\" target=\"_blank\"><img src=\"" . WP_PLUGIN_URL . "/newestbrowser/images/icon.png" . "\" style=\"float:right;\" /></a></div>";
			break;
		default:
			// SHOW LASTEST BROWSER THAT VISITOR IS CURRENTLY USING
			if(strlen(strstr(strtolower($strUserAgent),"firefox")) > 0 && strlen(strstr(strtolower($strUserAgent),"macintosh")) > 0 || strlen(strstr(strtolower($strUserAgent),"firefox")) > 0 && strlen(strstr(strtolower($strUserAgent),"windows")) > 0) {
				// IF USERAGENT CONTAINS FIREFOX
				$strText = "<a href=\"http://www.mozilla.com/en-US/firefox/all.html\" target=\"_blank\" rel=\"nofollow\"><img src=\"" . WP_PLUGIN_URL . "/newestbrowser/images/icon-firefox.png" . "\" /> Upgrade to version ";
				if(strlen(strstr(strtolower($strUserAgent),"macintosh")) > 0)
					$strText = $strText . get_option('mac-ff') . "</a>";
				else
					$strText = $strText . get_option('win-ff') . "</a>";
			} else if(strlen(strstr(strtolower($strUserAgent),"msie")) > 0) {
				// IF USERAGENT CONTAINS INTERNET EXPLORER
				$strText = "<a href=\"http://www.microsoft.com/windows/internet-explorer/worldwide-sites.aspx\" target=\"_blank\" rel=\"nofollow\"><img src=\"" . WP_PLUGIN_URL . "/newestbrowser/images/icon-internet-explorer.png" . "\" /> Upgrade to version " . get_option('win-ie') . "</a>";
			} else if(strlen(strstr(strtolower($strUserAgent),"opera")) > 0 && strlen(strstr(strtolower($strUserAgent),"macintosh")) > 0 || strlen(strstr(strtolower($strUserAgent),"opera")) > 0 && strlen(strstr(strtolower($strUserAgent),"windows")) > 0) {
				// IF USERAGENT CONTAINS OPERA
				$strText = "<a href=\"http://www.opera.com/browser/download/\" target=\"_blank\" rel=\"nofollow\"><img src=\"" . WP_PLUGIN_URL . "/newestbrowser/images/icon-opera.png" . "\" /> Upgrade to version ";
				if(strlen(strstr(strtolower($strUserAgent),"macintosh")) > 0)
					$strText = $strText . get_option('mac-opera') . "</a>";
				else
					$strText = $strText . get_option('win-opera') . "</a>";
			} else if(strlen(strstr(strtolower($strUserAgent),"chrome")) > 0 && strlen(strstr(strtolower($strUserAgent),"macintosh")) > 0 || strlen(strstr(strtolower($strUserAgent),"chrome")) > 0 && strlen(strstr(strtolower($strUserAgent),"windows")) > 0) {
				// IF USERAGENT CONTAINS CHROME
				$strText = "<a href=\"http://www.google.com/chrome\" target=\"_blank\" rel=\"nofollow\"><img src=\"" . WP_PLUGIN_URL . "/newestbrowser/images/icon-chrome.png" . "\" /> Upgrade to version ";
				if(strlen(strstr(strtolower($strUserAgent),"macintosh")) > 0)
					$strText = $strText . get_option('mac-chrome') . "</a>";
				else
					$strText = $strText . get_option('win-chrome') . "</a>";
			} else if(strlen(strstr(strtolower($strUserAgent),"safari")) > 0 && strlen(strstr(strtolower($strUserAgent),"iphone")) < 1 && strlen(strstr(strtolower($strUserAgent),"macintosh")) > 0 || strlen(strstr(strtolower($strUserAgent),"safari")) > 0 && strlen(strstr(strtolower($strUserAgent),"iphone")) < 1 && strlen(strstr(strtolower($strUserAgent),"windows")) > 0) {
				// IF USERAGENT CONTAINS SAFARI, BUT NOT IPHONE
				// IPHONE USERS CANNOT UPGRADE OUTSIDE ITUNES, SO THIS PLUGIN IS POINTLESS FOR THEM
				// SAFARI CONDITION IS CALLED AFTER CROME BECAUE CHROME HAS SAFARI IN USERAGENT STRING
				$strText = "<a href=\"http://www.apple.com/safari/download/\" target=\"_blank\" rel=\"nofollow\"><img src=\"" . WP_PLUGIN_URL . "/newestbrowser/images/icon-safari.png" . "\" /> Upgrade to version ";
				if(strlen(strstr(strtolower($strUserAgent),"macintosh")) > 0)
					$strText = $strText . get_option('mac-safari') . "</a>";
				else
					$strText = $strText . get_option('win-safari') . "</a>";
			} // ELSE MUST BE LINUX OR A MINOR BROWSER
				
			echo "<div id=\"cssNewestBrowser\">" . $strText . "<br /><a href=\"http://www.chriswashington.net/projects/newest-browser-for-wordpress\" title=\"Newest Browser module powered by CW Web Solutions LLC\" target=\"_blank\"><img src=\"" . WP_PLUGIN_URL . "/newestbrowser/images/icon.png" . "\" style=\"float:right;\" /></div>";
			break;
	};
	echo "<div id=\"cssCWWSClear\" />";
}

function NewestBrowser_head() {
?>
<link type="text/css" href="<?php echo WP_PLUGIN_URL . "/newestbrowser/newestbrowser.css" ?>" rel="stylesheet" media="all" />
<?php
}

// WIDGET SETUP
function widget_NewestBrowser($args) {
		extract($args);
		echo $before_widget;
		echo $before_title . 'Newest Browser' . $after_title;
		NewestBrowser();
		echo $after_widget;
}

// SHOW ADMIN MENU FOR PLUGIN
function NewestBrowser_MenuItem() {
    add_menu_page('Newest Browser', 'Newest Browser', 8, __FILE__, 'NewestBrowser_Options', WP_PLUGIN_URL . '/newestbrowser/images/icon.png');
}

// SHOW ADMIN OPTIONS PAGE
function NewestBrowser_Options() {
	// ON SAVE, SAVE DATA
	if ('true' == $_POST['blnSaved'] && is_admin()) {
		update_option('promote', $_POST['promote']);
	}
?>
	<div class="wrap">
		<h3><?php _e("Newest Browser"); ?></h3>
		<p style="text-align:center;"><a href="http://www.chriswashington.net/" alt="CW Web Solutions LLC" title="CW Web Solutions LLC" target="_blank"><img src="<?php echo WP_PLUGIN_URL; ?>/newestbrowser/images/logo-black.jpg" border="0"></a></p>
		<p>Encourage visitors to upgrade to the newest in browser technology/security by displaying updates for major browsers your visitor is using, or promote a browser of your choice.</p>
		<p>Updated numeric versions are extracted remotely from <a href="http://www.newestbrowser.com/" title="Updates for your browser" target="_blank">NewestBrowser.com</a> once a day, then stored in your database to be extracted for optimization.</p>
		<p>This is an early verion, but future versions will give you more control of how promoted content is displayed.</p><p>A <a href="http://www.chriswashington.net/projects/newest-browser-for-drupal" title="Newest Browser Module for Drupal" target="_blank">Drupal module</a> and <a href="http://www.chriswashington.net/projects/newest-browser-for-joomla" title="Newest Browser Extension for Joomla" target="_blank">Joomla extension</a> of Newest Browser are also avalialble for download.</p>
		<p><a href="http://www.chriswashington.net/" title="CW Web Solutions LLC" target="_blank">CW Web Solutions LLC</a></p>
		<br />Steps:<br /><br />1. Choose a browser to promote<br /><br />2. Choose where to add the widget in the sidebar<br /><br />3. Or use the short code <strong>[NewestBrowser]</strong> in post or page content<br /><br />
		<?php if ($_REQUEST['save']) { echo '<div id="message" class="updated fade"><p><strong>Option settings saved.</strong></p></div>'; } ?>
		<form method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>&updated=true">
			<?php wp_nonce_field('update-options'); ?>
			<p>
			<?php _e('Which browser do you want to promote the newest version of: ') ?>
			<select name="promote">
				<option value="default" <?php selected('default', get_option("promote")); ?>><?php _e("Visitor's Browser"); ?></option>
				<option value="Internet Explorer" <?php selected('Internet Explorer', get_option("promote")); ?>><?php _e("Internet Explorer"); ?></option>
				<option value="Opera" <?php selected('Opera', get_option("promote")); ?>><?php _e("Opera"); ?></option>
				<option value="Safari" <?php selected('Safari', get_option("promote")); ?>><?php _e("Safari"); ?></option>
				<option value="Firefox" <?php selected('Firefox', get_option("promote")); ?>><?php _e("Firefox"); ?></option>
				<option value="Chrome" <?php selected('Chrome', get_option("promote")); ?>><?php _e("Chrome"); ?></option>
			</select>
			</p>
			<input type="hidden" name="blnSaved" value="true" />
			<p class="submit"><input type="submit" class="button-primary" value="<?php _e('Save Settings') ?>" /></p>
		</form>
	</div>
<?php
}

// DELETE DATA WHEN PLUGIN IS DELETED
function NewestBrowser_uninstall_hook() {
    delete_option('promote');
	delete_option('lastupdated');
	delete_option('win-ie');
	delete_option('win-opera');
	delete_option('win-safari');
	delete_option('win-ff');
	delete_option('win-chrome');
	delete_option('mac-safari');
	delete_option('mac-ff');
	delete_option('mac-chrome');
	delete_option('mac-opera');
}
?>