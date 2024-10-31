=== Outdated Plugin Notifier ===
Contributors: carlgross
Donate link: https://everlooksolutions.com/
Tags: plugins
Requires at least: 4.9.0
Tested up to: 6.7
Stable tag: trunk
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

To the plugins admin table, adds a new column indicating the last time the plugin developer released an update.

== Description ==

WordPress plugins frequently become abandoned by their developers, and in-turn, develop security vulnerabilities.  If one of these plugins is installed on your site, it will put your site at risk, without any warning or indication.  Outdated Plugin Notifier is a lightweight and unobtrusive plugin that helps you stay aware of these situations.  Outdated Plugin Notifier monitors your plugins and tells you exactly when each plugin was last updated by its developer.  If a plugin has been expelled from the WordPress plugin repository, Outdated Plugin Notifier will display that information as well.

To use, simply install and activate the plugin.  There is no additional configuration required.  After, open the plugin admin page from your WordPress dashboard and you will see a new column displayed in the table:  Last Dev Update.  This column will indicate the last date the plugin was updated by its developer.  If it has been several months--or years--since the developer last updated the plugin, you may want to contact the developer to confirm the plugin is still maintained.  If the plugin is no longer in the WordPress plugin repository, Outdated Plugin Notifier will indicate so on the plugins admin page, and suggest you remove and/or replace the plugin.

== Installation ==
1. Upload the plugin files to the `/wp-content/plugins/outdated-plugin-notifier` directory, or install the plugin directly through the WordPress plugins page.

2. Activate the plugin through the 'Plugins' screen in WordPress

== Frequently Asked Questions ==

= Are there any settings to configure? =

There are currently no settings to configure with this plugin.  As soon as you activate the plugin, it will display a new column on the plugin admin page. 
 
== Screenshots ==

1. This screen shot description corresponds to screenshot-1.jpg. After installing and activating the plugin, your plugin admin table should now display a new column, "Last Dev Update."  This column indicates the last time the plugin developer released an update.

== Changelog ==

= 1.0.6 =
* Adds (to the WordPress plugins page) a more descriptive message when a plugin is not found in the WordPress plugin repo.
* Adds minor front-end error handling.

= 1.0.5 =
* Adds more descriptive messages when plugin fails minimum WordPress or PHP check.

= 1.0.4 =
* Fixes a bug which was causing dates to display in the browser's default locale, rather than the WordPress user's locale.

= 1.0.3 =
* Fixes a bug which was causing plugin to display a blank string for some plugins (instead of a date or an error message).

= 1.0.2 =
* Fixes a performance bug which was causing admin plugins page to load very slowly.  

= 1.0.1 =
* Fixes a bug which would have prevented translations from being added to the plugin. Feel free to contribute to the project and translate the plugin into a language other than EN-US.

= 1.0.0 =
* First publicly available version. 

== Upgrade Notice ==

= 1.0.0 =
Upgrade notices describe the reason a user should upgrade.  Stay tuned for more info here.
