﻿=== Author Avatars List/Block ===
Contributors: pbearne
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=MZTZ5S8MGF75C&lc=CA&item_name=Wordpress%20Development%20%2f%20Paul%20Bearne&item_number=AuthorAvatarsList%20Plugin&currency_code=CAD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted
Tags: block, Avatar, Author, Image, Profile
Requires at least: 3.0
Tested up to: 6.8
Stable tag: 2.1.25
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Display lists of user avatars using widgets or shortcodes. With Gutenberg support.

== Description ==

This plugin makes it easy to *display lists of user avatars*, grouped by user roles, on your (multiuser) site. It also allows you to *insert single avatars* for blog users or any email address into a post or page - great for displaying an image of someone you're talking about.

It makes use of built-in WordPress (core) functions to retrieve user information and get avatars.

Integrates with: Gutenberg, BuddyPress, xprofile, Multisite, Wpmu, BBPress, co-authors.

Avatar lists can be inserted into your sidebar by adding a widget or into posts/pages by using a [shortcode](http://authoravatars.wordpress.com/documentation/authoravatars-shortcode/). The plugin comes with a tinymce editor plugin which makes inserting shortcodes very easy.

This also provides Gutenberg Block for use with the Gutenberg editor.
Please help with the plugin Translations at https://translate.wordpress.org/projects/wp-plugins/author-avatars.

Both the shortcode and widget and Gutenberg Block can be configured to:

*   Show a custom title (widget only)
*   Only show specific user groups and/or hide certain users
*   Limit the number of users shown
*   Change the sort order of users or show in random order
*   Adjust the size of user avatars
*   Optionally show a user's name or biography
*   Show users from the current blog, all blogs or a selection of blogs (on WPMU/Multisite)
*   Group users by their blog (when showing from multiple blogs), and show the blog name above each grouping.
*   Support users from Co-Author Plus, Ultimate Member, BBpress and BuddyPress (xprofile)
*   Limit the number of avatars per page for large sets by adding a page_size to the shortcode e.g. "page_size=30" (shortcode only)

Additionally, single user avatars can be inserted using the [show_avatar shortcode](http://authoravatars.wordpress.com/documentation/show_avatar-shortcode/) and configured to:

*   Adjust the size of the user avatar.
*   Align the avatar left, centered or right.
The Gutenberg Block support both single user avatars and role based selections of avatars

Please report bugs and provide feedback in the [wordpress support forum](http://wordpress.org/tags/author-avatars?forum_id=10#postform).

**Plugin support:** In 2011, Ben stepped down as maintainer of the Plugin, handing over ownership to co-author Paul Bearne, who continues to provide support and drive the development of new features.

== Installation ==

1. Upload the `author-avatars` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Enable and configure the widget as usual on the Design / Widgets page.

[Look at this page](https://authoravatars.wordpress.com/documentation/authoravatars-shortcode/) to find out how to use the [authoravatars] shortcode.

You can find information for developers [on this page](http://authoravatars.wordpress.com/documentation/developers-guide/).

== Upgrade Notice ==

<strong>Breaking change in 1.8.0 </strong> in CSS *.multiwidget_author_avatars* is now *.widget_author_avatars*. This is caused by a library change  in-order to support the jetpack visibility option.<br />
If you have added CSS to your theme you may have to update it for this upgrade (do a find and replace).

== Screenshots ==

1. Very simple set up Gutenberg block in a empty blog.
2. Gutenberg block.
3. Examples of what the <code>[authoravatars]</code> shortcode can do.
4. Shortcode helper available from the WYSIWYG editor on the edit post page.
5. List of users with name and biography
6. The Widget configuration panel.
7. Advance Gutenberg blocks options

== Changelog ==
2.1.25
Added lazy loading tag to avatars
Can be overriden with filter aa_user_avatar_lazy_load set to false
Bumped WP VERSION

2.1.24
Addjusted where the late escaping happened for the background-color
Fix issue with the border in the block
Added radius control for avatar in the block
2.1.23
Addjusted where the late escaping happened for the avatar name

2.1.22
Added late escaping to avatar name

2.1.21
Fix an enqueuing issue @props @ryanwelcher

2.1.20
fix to display attr in Blocks @props @joergliwa

2.1.19
2.1.18
Fixed an additional input
reported by Ngô Thiên An (ancorn_)

2.1.17
Fixed lack of escaping on the shortcode
reported by Ngô Thiên An (ancorn_)

2.1.16
Fixed duplicate query when sorting by posts

2.1.15
"wp-editor" script should not be enqueued notice fixed

2.1.14
php 8.1 fix

2.1.13
wp version bump

2.1.11
added missing files

2.1.10
Fixed color picker cashing in editor
Added border options

2.1.8
Fix for PHP 8 thanks to @editpostok for reporting

2.1.5
Stopped not needed css from loading

2.1.4
Removed .live in js

2.1.3
Added filter for last post link text

2.1.2
fix object type for Gutenberg block

2.1.1
added preview and block.json

2.0.8
fixed the detection for bbPress plugin

2.0.7
Fixed block not setting the user_id dropdown on page reload

2.0.6
Fixed trim function

2.0.5
added more lang for TinyMCE plugin

2.0.4
trim $atts so , don't break short code
refactoring code (start)

2.0.3
removed PHP 7 return type

2.0.2
Fixed padding in Gutenberg sidebar

2.0.1
Added missing files

2.0.0
Gutenberg support added :-)

= 1.18 =
PHP 7.3 errors
https://wordpress.org/support/topic/php-7-3-17/

= 1.17 =
bumped WP version

= 1.16 =
fixed name not loading in ajax paging calls

= 1.15 =
Added filter aa_user_final_css to filter the classes on the user div
added name-{first_letter} class
Added span wrape around post count brackets

= 1.14 =
Added nickname what could be show
refactored [show_avatar] to use display


= 1.13 =
added implode to xprofile content if array

= 1.12 =
Fixed the JS in the TinyMCE popup

= 1.11 =
added nonce to check_admin_referer

= 1.10 =
fixed PHP7 Deprecated constructors
Added does class exists for BP xprofile code

= 1.9.9 =
fixed typo
fixed filter name
fixed advance option not showing in widget form

= 1.9.8 =
Added Filter aa_user_raw_list

= 1.9.7 =
Added filter aa_user_show_last_post_query
Added the ability to use any URL in the profile contact section as a link
Added the ability for user_link to accept a comma-separated list as fall through if a URL is not found in first selection
Added contact_links to the short-code

= 1.9.6 =
readme update
removed php 4 constructors

= 1.9.5 =
Added sorting by white list values

= 1.9.4 =
Added white list for users

= 1.9.3 =
renamed function causing redeclare error

= 1.9.2 =
Added aa_user_show_last_post_type filter to allow setting of post type for last post link
added defaulted to author page if no last post is returned

= 1.9 =
fixed problem with WP 4.4 and widgets not saving
Add help translate link

= 1.8.8 =
set the page count to start at 1 not 0
Added support for UM profiles links


= 1.8.7 =
replaced parent::WP_Widget()  with parent::__construct to remove php 4 constructors
remove extract( $args, EXTR_SKIP and replaced with direct extracts
Added user id to CSS
Fixed Co_Author Plus listings

= 1.8.6.6 =
Added Hungarian Translation (by Otto Radics: Webmenedzser.hu - http://www.webmenedzser.hu)

= 1.8.6.5 =
Added filter (aa_user_level_for_editor) to allow control of who can see the tinyMCE editor button
Added last_post_filter option to link options
Fix the truncating of bio in single avatars
Changed AA in filter names to aa

= 1.8.6.4 =
Fixed a problem with upgrading if you had bios

= 1.8.6.3 =
Set the bios to maintain line breaks

= 1.8.6.2 =
Fixed the random order if not logged in

= 1.8.6.1 =
deploy script failed to add new file

= 1.8.6.0 =
* added the ability to truncate bio
* added support for BuddyPress xprofile
* add filter to post count number @props Andrew Minion


= 1.8.4.2 =
* Whitespace reformat
* removed trailing php closing from files

= 1.8.4.1 =
* Fix mixed limmint cache ID


= 1.8.4 =
* Added Ukrainian Translation (by Michael Yunat:  Get Voip - http://getvoip.com)
* Fixes around the cache id
* Replaced Deprecated : is_site_admin() with  is_super_admin()
* Fixed path to tinyMCE js files reomved hardcoded path
* fix static call when starting class

= 1.8.3 =
* added a tile to the span arond the image.
* disabled / delete the transit cache for logged in users to help clear them.
= 1.8.2 =
* change the caching to use transits for SQL calls with a 1 hour refesh
= 1.8.1 =
* Added support for tinyMCE version 4 ready for WordPress 3.9
= 1.8.0 =
* Replaced the pre 2.6 wordpres widget code with the current widget API calls to enable visablity setting
* CSS changed .multiwidget_author_avatars changed to .widget_author_avatars. This was caused by the widget API update
* Added expemently support for Co-Author Pluss Plugin - the post count does not work for linked account - will take a patch that fixs it :-)
* Moved the display option to the right column to make more room for roles
* Split 'Recent Activity' and 'BudyPress last activity' (only shows when buddypress running) to septerate options in the advance ordering option
* Split / removed 'Recent Activity' into sitewide (pages / custom page types / posts) and just posts (any old shortcode will call just posts)

= 1.7.1 =
* bubfix removed an extra ' in a SQL select in get_user_last_activity() function. Thanks to "basaja" for the bug report.

= 1.7.0 =
* Added Local User select to Single Avatar Shortcode creator
* Replaced wp_specialchars() with esc_html()
* Added BBPRESS_post_count as shortcode dispaly and sort options
* Added show_email to shortcode display option
* Added some translation updates
* Fixed issue with TniyMCE breaking when using HTTPS
* And a few other tidy ups
* Added SQL fliter to only fetch the users for the rolls being requested rather than all users
* Added caching to the main get_users function which will use an object cache if turned on

= 1.6.3 =
* Wraped ordering code in "remove_accents" functions to to replace Uni-code accents with non unicode versions so sort works as expected.
* Increased height of TinyMCE popup so content shows with scroll bars.
* Replaced text donate links with image link.

= 1.6.2 =
* Added display options for single Avatar options
* Added donation link

= 1.6.1 =
* Fixed a bug that stoped the loading of default CSS sheet for the plugin that I added a bug in in 1.6

= 1.6.0 =
* Added the option to link to BBpress profile in the link to the shortcode and generator  user_link=bbpress_memberpage
* Fixed bug - the the shortcode generator was shown up in the tinyMCE edit if it was loaded on a page (BBpress forum posts) the popup was 404'ing so add a $pagenow != 'index.php' to make sure we are in the addmin section
* Fixed bug causing the RTL layout to break

= 1.5.1 =
*  Added  Hindi language (by Love Chandel:  Outshine Solutions - http://outshinesolutions.com)

= 1.5 =
*  Added Paging to the short code
*  Added  Romanian language (by Alexander Ovsov:  Web Hosting Geeks - http://webhostinggeeks.com)

= 1.4 =
*   Fix a bug in the js code for the short-code generator in the tinyMCE editor.
*   It wasn't possible to set the show name / post count / biography options.

= 1.2 =
*   Added Italian translation (by Nata Strazda)

= 1.1 =
*   Added fix for buddypress which was using thumb instead of full versions of images.
*   Added support for network admin area (new in WP 3.1)
*   Added dutch translation by René (wpwebshop)
*   Fixed bug with min_post_count in shortcode

= 1.0 =
*   Fixed a number of styling issues
*   Fixed bug with capabilities (Wordpress 3 multisite)
*   Removed deprecated functions

= 0.9 =
*   Fixed compatibility with WordPress 3.0 (and its new multisite feature)
*   Fixed BuddyPress integration
*   Added feature to show avatars of commentators
*   Added feature to sort by firstname or lastname

= 0.8 =
*   Added feature to show a user's biography next to the avatar
*   Added feature to limit shown users by a minimum number of posts
*   Added feature to show a user's number of posts
*   Added Italian translation (by Gianni Diurno)

= 0.7.4 =
*   Fixed javascript issues with widget settings page and shortcode wizard in WordPress 2.8
*   Fixed support for translations
*   Added German translation
*   Added feature to sort by recent user activity (requires Buddypress)

= 0.7.3 =
*   Added filters to allow modification of userlist templates
*   Added "BuddyPress? Member Page" to the list of pages which a user can be linked to
*   Changed get_avatar() call so that it works with buddypress

= 0.7.2 =
*   Fixed a spelling mistake which prevented the plugin from loading

= 0.7.1 =
*   Improved inline function documentation
*   Fixed bug which caused a faulty name attribute for checkbox lists with only one choice. Now the "show name" option is working as exptected again.
*   Removed by-reference variable which causes PHP 4 parse errors

= 0.7 =
*   Removed invalid characters from uninstall.php (fixes uninstall behaviour).
*   New feature to link users to their website or blog (wpmu).
*   Added new feature to allow specification of a sort direction for sorted user lists.
*   Changed string-based sorting to case-insensitive (strcmp -> strcasecmp).
*   Added feature to sort users by date of registration.
*   Optimised UserList filtering.
*   Fixed numeric sorting issues (user_id and post count)
*   Added "order by number of posts" feature
*   Removed user role from avatar title.

= 0.6.2 =
*   Fixed bug which caused the plugin to crash in PHP 4.
*   Added uninstall.php to remove plugin related data when the plugin is deleted.

= 0.6.1 =
*   Fixed bug which caused other tinymce plugins to stop working.
*   Improved way of detecting a wpmu install.

= 0.6 =
*   Implementation of tinymce plugin.
*   Removed personalised jquery ui script and added just the packed ui.resizable.
*   Changed script and stylesheet handling (using register&enqueue functions with proper dependencies).
*   Refactored the "resizable avatar preview" script code into separate file.
*   FormHelper: Added option to generate textareas.
*   FormHelper: Added option to show expanded choice fields "inline".
*   Added improved function for cleaning up a value to be used as html id attribute.
*   AuthorAvatarsForm: added methods to ease generation of tabs and two-column panes.
*   AuthorAvatarsForm: added new renderField methods for shortcode type, email and alignment (used in show_avatar wizard).
*   Various Documentation updates and cleanups.
*   Refactored widget form field generation into new AuthorAvatarsForm.class.php to ease devevlopment of shortcode wizard.
*   Refactored form field generation code into new FormHelper.class.php.
*   Fixed size and position of blog selection box on sitewide admin page. Changed the name of is_wpmu() function to safer name AA_is_wpmu().
*   Removed "Group by blogs" checkbox for users without the blog selection filter.

= 0.5.1 =
*   Fixed method chaining error that caused a critical syntax error on PHP 4

= 0.5 =
*    Added "show_avatar" shortcode
*    Small MultiWidget fix by [Dan Cole](http://blog.firetree.net/2008/11/30/wordpress-multi-widget/#comment-24976)
*    Refactored [show_avatar] shortcode into new file ShowAvatarShortcode.class.php to keep it all nice and tidy.
*    Added basic blog filtering feature.
*    Added classes for settings and sitewide admin
*    Added sitewide setting for the blog filter
*    Updated update mechanism in AuthorAvatars.class.php
*    Added "Group by blog" feature

= 0.4 =
*    Added new [shortcode](http://authoravatars.wordpress.com/documentation/authoravatars-shortcode/) feature.
*    Fixed small bug in update procedure (version 0.1 to 0.2)

= 0.3 =
*    Fixed error that broke some javascript on "edit post" pages in wordpress 2.7

= 0.2 =
*    Widget: added avatar preview image to the control panel
*    Widget: added option to link the user/avatar to their respective "author page"
*    Widget: hiddenusers also allows user ids now (e.g. 1 for "admin")
*    Refactored the plugin to use [Alex Tingle's "MultiWidget" class](http://blog.firetree.net/2008/11/30/wordpress-multi-widget/)

== Frequently asked questions ==

= Shortcode, huh? =

A shortcode is a tag like <code>[authoravatars]</code> which you can insert into a page or post to display a list of users on that post/page. You can read more about shortcodes in general in the wordpress codex, for example [here](http://codex.wordpress.org/Using_the_gallery_shortcode) or [here](http://codex.wordpress.org/Shortcode_API).

= How do I use the author avatar shortcode? =

As of version 0.6 the plugin comes with a tinymce plugin which makes it very easy to insert shortcode(s).

If you'd like to do it manually it's still simple: just add <code>[authoravatars]</code> into your post and hit save! There's a large number of [parameters](http://authoravatars.wordpress.com/documentation/authoravatars-shortcode/) available.

The plugin comes with two shortcodes: <code>[authoravatars]</code> for lists of avatars and <code>[show_avatar]</code> for single avatars.

= I can't get my widget to show users from multiple blogs! =

Make sure you have enabled the "blog filter" in Site Admin / Author Avatars for the blog on which you are trying to use this feature on. By default this is only enabled for the root blog (blog id = 1).

And you are running [Wordpress MU](http://mu.wordpress.org/) (or respectively WordPress 3 in multi-site mode), right?

= Can I upload custom pictures for users? =

No, the Author Avatars List plugin only provides ways of <strong>displaying</strong> user avatars.

The plugin uses the Wordpress Core Template function <code>get_avatar()</code> to retrieve the actual avatar images. In order to display custom images you need to look for plugins which use/override WordPress' avatar features and provide respective upload features...

Have a look at the [User Photo](http://wordpress.org/extend/plugins/user-photo/) Plugin (turn on option "Override Avatar with User Photo") or the [Add Local Avatar](http://wordpress.org/extend/plugins/add-local-avatar/) Plugin.

= I get a "404 Page not found" error when I click on the avatar of a user! =

This can happens when you've choosen to link users to their "author page" and the user has not written any posts on a blog. There are two things that you should do in this situation:

1. To prevent the 404 page from showing up install the [Show authors without posts](http://wordpress.org/extend/plugins/show-authors-without-posts/) Plugin. This forces WordPress to always show the user page if the user exists.

2. If not already there add a custom user/author template to your theme. Otherwise if a user has no posts their user page is going to be quite empty by default...
You can find a [tutorial](http://codex.wordpress.org/Author_Templates) on Author Templates as well as a [Sample Template File](http://codex.wordpress.org/Author_Templates#Sample_Template_File) in the WordPress Codex.

= Can I use html in user biographies? =

Wordpress Core unfortunately strips all html from the user biography field when entered. Install the plugin [Weasel's HTML Bios](http://wordpress.org/extend/plugins/weasels-html-bios/) if you want to use html...

= How can I change the styling of the avatar lists? =

The styling of the widget is controlled by the styles defined in [css/widgets.css](http://plugins.trac.wordpress.org/browser/author-avatars/trunk/css/widget.css), avatars on posts/pages (using the shortcode) are styled by code in [css/shortcode.css](http://plugins.trac.wordpress.org/browser/author-avatars/trunk/css/shortcode.css).

You can override the styles in that file by copying a style block to your theme's `style.css` and adjusting respectively. For example add the following to remove the padding from avatars displayed in a widget:

`html .widget_author_avatars .author-list .user {
  padding: 0;
}`

demo edit