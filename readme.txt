=== UCB Recommend ===
Contributors: 123teru321
Tags: ucb, recommend, recommendation, multi-armed bandit
Requires at least: 3.9.14
Tested up to: 4.6.1
Stable tag: 1.1.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Recommendation plugin using ucb algorithm

== Description ==

You can get related post based on user behavior.  
This plugin uses UCB algorithm.  
https://en.wikipedia.org/wiki/Multi-armed_bandit  
http://techlife.cookpad.com/entry/2014/10/29/102036  
https://support.google.com/analytics/answer/2844870?hl=ja  
At least PHP5.4 is needed.  
[日本語の説明](https://technote.space/ucb-recommend "Documentation in Japanese")

This plugin needs PHP5.4 or higher.

== Installation ==

1. Upload the `ucb-recommend` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Open the setting menu, it is possible to set the execution condition.

== Screenshots ==

1. Settings.
2. Recommendation.

== Changelog ==

= 1.1.3 =
* 2016-10-09
* Modified translation
* Changed default value
* Small changes to dashboard

= 1.1.2 =
* 2016-10-02
* Add history tab
* Show php version when PHP version < 5.4

= 1.1.1 =
* 2016-10-02  Translation

= 1.1.0 =
* 2016-10-02  Performance

= 1.0.9 =
* 2016-10-02  Small bug fix

= 1.0.8 =
* 2016-10-02
* Add test
* Show message if PHP version < 5.4
* Add mode not to consider cache page
* Add changed option filter
* Add mode to check develop update
* Change algorithm to decide widget post
* Small bug fix

= 1.0.7 =
* 2016-09-28  SERVER_NAME => HTTP_HOST

= 1.0.6 =
* 2016-09-28  Change default front_admin_ajax setting

= 1.0.5 =
* 2016-09-28  Add filter to decide whether to check ajax referer

= 1.0.4 =
* 2016-09-27
* Ajax access without using admin-ajax.php
* PHP7
* Small bug fix

= 1.0.3 =
* 2016-09-26  Small bug fix

= 1.0.2 =
* 2016-09-26
* Add ucb constant setting 
* Small bug fix

= 1.0.1 =
* 2016-09-22  Small bug fix

= 1.0.0 =
* 2016-09-21  Registered wordpress plugin directory

= 0.2.9 =
* 2016-09-21  Small bug fix

= 0.2.8 =
* 2016-09-21  Small bug fix

= 0.2.7 =
* 2016-09-21  Changed logo

= 0.2.6 =
* 2016-09-21  Changed custom post type name

= 0.2.5 =
* 2016-09-21  Bug fix

= 0.2.4 =
* 2016-09-20  Bug fix

= 0.2.3 =
* 2016-09-20  Small design changes

= 0.2.2 =
* 2016-09-20  Do shortcode of widget text 

= 0.2.1 =
* 2016-09-20  Small bug fix

= 0.2.0 =
* 2016-09-20
* Modified design preview behavior
* Small bug fix

= 0.1.9 =
* 2016-09-20  Small bug fix

= 0.1.8 =
* 2016-09-20  Small bug fix

= 0.1.7 =
* 2016-09-20
* Support multiple redirect urls
* Small bug fix

= 0.1.6 =
* 2016-09-20  Small bug fix

= 0.1.5 =
* 2016-09-20
* Changed post type condition set
* Add redirect url condition set
* Add filter to custom post type settings

= 0.1.4 =
* 2016-09-20
* Add custom post type for redirect
* Add new redirect behavior
* Small bug fix

= 0.1.3 =
* 2016-09-19  Modified design setting behavior

= 0.1.2 =
* 2016-09-19
* Add design preview
* Small bug fix

= 0.1.1 =
* 2016-09-18
* Support no context mode
* Small bug fix

= 0.1.0 =
* 2016-09-17  Small bug fix

= 0.0.9 =
* 2016-09-17  Small change to admin page’s design

= 0.0.8 =
* 2016-09-17
* Small bug fix
* Small change to admin menu

= 0.0.7 =
* 2016-09-17  Small bug fix

= 0.0.6 =
* 2016-09-17  Small bug fix

= 0.0.5 =
* 2016-09-17  Preview bug fix

= 0.0.4 =
* 2016-09-17  Translation

= 0.0.3 =
* 2016-09-17

= 0.0.2 =
* 2016-09-17

= 0.0.1 =
* 2016-09-14  First release
