=== Blogger Title Fix ===
Contributors: poco
Donate link: http://notions.okuda.ca
Tags: blogger, title
Requires at least: 2.8
Tested up to: 4.3.1
Stable tag: trunk

This plugin replaces the random title given to posts that were imported from Blogger with an excerpt.

== Description ==

If you have ever imported a Blogger blog into WordPress you probably have some posts that were originally missing a title. During the import, a title (and post slug) was created for the post that looks like a large numerical value. I believe this is the original Blogger post ID, I guess the importer needed something to use.

What this plugin does is replace those ugly numerical titles with the first few words of your post, like Blogger did. By simply enabling the plugin, any post that matches the “ugly title” filter will have the title replaced by the first 48 characters of the post.

== Installation ==

1. Upload all the files to the `/wp-content/plugins/blogger-title-fix` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Configure the plugin in the Options admin tab. As of version 2.0 it is now in the Plugins tab.

== Frequently Asked Questions ==

== Changelog ==

= 0.1 =
* Initial release

= 0.2 =
* Added option to change the number of characters used for the title from the default 48. Added option to break the title at a word boundary.

= 2.0 =
* Upgraded to work with WordPress 2.5. Fixed a bug with disabling the word boundary setting. Made it more efficient by removing the extra DB query unless it was really necessary.

= 2.1 =
* Added single_post_title filter at the request of Alejandro Carravedo

= 2.2 =
Removed usage of second parameter for the_title filter, it was inconsistent - relies on global "post" to generate the new title.
