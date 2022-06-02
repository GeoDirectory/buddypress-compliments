=== BuddyPress Compliments ===
Contributors: viruthagiri, stiofansisland, paoltaia
Donate link: http://wpgeodirectory.com/
Tags: buddypress, buddypress compliments, WordPress yelp compliments, buddypress integration, business directory plugin, directory, directory plugin, geodirectory, geodirectory buddypress, geodirectory buddypress integration, social network, yelp clone, yelp compliments
Requires at least: 3.1
Tested up to: 5.2
Stable tag: 1.0.9
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Inspired by Yelp compliments system BuddyPress Compliments is a BuddyPress addon that allows members to send each other compliments or eGifts. 

== Description ==

BuddyPress compliments adds a smart way for BuddyPress members to interact with each other via compliments.

* Install the plugin
* Create unlimited number of Compliment types (eg: Thank you - Good Writer - Cute Pic - Like your Profile - etc.)
* A Compliment button and Compliments tab will appear in each member's profile.
* When you click the Compliments button, a popup will show up with compliment types
* Submitted compliments will be visible in user's compliments section.
* Compliments can be set as private and visible only to the member receiving them.
* A notification is optionally sent to the compliment receiving member.
* Compliments can optionally be tracked in BuddyPress Activity
* Members can delete compliments received (option to prevent this available).
* Compliments are now paginated
* Compliments can be renamed to anything Ex: "Gifts"

We built this plugin especially for Whoop! our [WordPress Directory Theme](https://wpgeodirectory.com/themes/wordpress-directory-theme-whoop/ "Whoop! is the latest Social Directory theme for GeoDirectory") powered by [GeoDirectory](https://wordpress.org/plugins/geodirectory/ "Whoop! is a GeoDirectory theme and it will be released soon"). 

The plugins has been tested with the following themes before release:

Twenty Thirteen, Twenty Fourteen, Twenty Fifteen, GeoDIrectory Framework and Whoop! of course.

Should you find any bug, please report it in the support forum and we will fix it asap!

BuddyPress Compliments is 100% translatable.

== Installation ==

= Minimum Requirements =

* WordPress 3.1 or greater
* BuddyPress plugin
* PHP version 5.2.4 or greater
* MySQL version 5.0 or greater

= Automatic installation =

Automatic installation is the easiest option. To do an automatic install of BuddyPress Compliments, log in to your WordPress dashboard, navigate to the Plugins menu and click Add New.

In the search field type BuddyPress Compliments and click Search Plugins. Once you've found our plugin you install it by simply clicking Install Now. [BuddyPress Compliments installation](https://docs.wpgeodirectory.com/buddypress-compliments-overview/)

= Manual installation =

The manual installation method involves downloading BuddyPress Compliments and uploading it to your webserver via your favourite FTP application. The WordPress codex will tell you more [here](http://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation). 

= Updating =

Automatic updates should seamlessly work. We always suggest you backup up your website before performing any automated update to avoid unforeseen problems.

== Frequently Asked Questions ==

No questions so far, but don't hesitate to ask!

== Screenshots ==

1. The compliments page where you can add compliment types.
2. A Compliment button and Compliments tab are added to each profile page.
3. Click the Compliments button and a popup will be displayed with compliment types for submission.
4. Submitted compliments will be visible in user's compliments section.

== Changelog ==
= 1.0.9 =
Fix issue for plugin installation via WP-CLI - FIXED
Compliments can be deleted by the sender - ADDED
Installation redirects only if user has BP - FIXED
Add a close button at the top of lightboxes - ADDED
Add multiple compliment notification issue - ADDED
Fix invalid html issue for send compliment button - FIXED

= 1.0.7 =
Send compliment button can be displayed in /members page - ADDED
PHP undefined notices in wordpress admin - FIXED
Option added to remove plugin data on plugin delete - ADDED
Email headers changed from string to array() and MIME-Version removed - CHANGED
Compliment not displaying content in activity page - FIXED
Filter added to prevent duplicate compliments - ADDED
Compliment can be redirected to individual compliment page after send - ADDED
Compliment can be redirected to individual compliment from notifications - ADDED

= 1.0.6 =
Notification incorrect from email - FIXED

= 1.0.5 =
Email notifications not working - FIXED
Compliment line break and clickable support removed. Use filter to override - CHANGED

= 1.0.4 =
Compliment user settings page - ADDED
Linebreaks are removed on compliment messages - FIXED
Compliments message links are clickable - ADDED

= 1.0.1 =
Admin can delete compliments - ADDED
Use singular name instead of slug name - FIXED
404 page when deleting compliments - FIXED

= 1.0.0 =
Compliment message uses slug name instead of singular name - FIXED
Send modal form not showing properly in mobile - FIXED
Plugin out of BETA

= 0.0.9 =
Added support for member only compliment display - ADDED
Added filters and actions for whoop theme compatibility - ADDED
Changed textdomain from defined constant to a string - CHANGED
Notification not showing properly - FIXED


= 0.0.8 =
Option added to enable/disable activity component - ADDED
Option added to enable/disable notifications component - ADDED
Added placeholder text to message textarea - ADDED
Changed textdomain from defined constant to a string - CHANGED
Compliments in activity dropdown filter is ambiguous - FIXED
Undefined property ID notice - FIXED

= 0.0.7 =
Compliments can be renamed to anything Ex: Gifts - ADDED

= 0.0.5 =
404 error while sending compliments - FIXED

= 0.0.4 =
Members can see other members compliment page? setting - ADDED

= 0.0.3 =
Docblocks added to all functions, filters and actions - ADDED
Members can delete compliments received? setting - ADDED
Number of Compliments to display per page setting - ADDED
Custom CSS styles setting - ADDED
Validation added when adding compliment, icon required - FIXED
Some strings are not translatable - FIXED

= 0.0.2 =
* Support for bp activity component - ADDED
* Compliments can be deleted by the receiver - ADDED
* Supports for bp notification component - ADDED
* Translation support - ADDED
* Compliment Icon upload form uses latest media uploader - CHANGED
* Send compliment modal form z-index bug - FIXED

= 0.0.1 =
* First release.

== Upgrade Notice ==

TBA