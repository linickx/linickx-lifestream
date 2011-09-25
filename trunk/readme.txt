=== Plugin Name ===
Contributors: linickx
Donate link: http://www.linickx.com/donate
Tags: lifestream, feed
Requires at least: 2.8.4
Tested up to: 3.2.1
Stable tag: 0.2

 A WordPress Plug-in which allows you to lifestream any Feed!

== Description ==

LINICKX LifeStream is a plugin which allows you to lifestream any feed. Simply load up your RSS feeds into the plugin and posts will be created in wordpress.

A new post is created for each lifestream entry you can then use standard WordPress themes & plug-ins to customize these posts anyway you like.

== Installation ==

1. Unzip `linickx-lifestream.zip` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. In the WordPress dashboard, Settings -> Lifestream fill in the feed you want to stream, e.g.  http://twitter.com/statuses/user_timeline/5902742.rss
1. Wait :-) ....posts will be created in WordPress when the feed is updated.

== Frequently Asked Questions ==

= How often are feeds checked ? =
By default every 5 minutes, but you can change that.

= Some users experience slowness on my website! =
By default at the end of the 5minute period the next user to visit your site kicks off a "feed check" for that user it can take some time for your site to load as they have to wait for this to complete. Don't worry tho', by enabling *cron mode* you can fix this.

= What is Cron Mode ? =
You can configure Unix Cron to update your feeds, in the  WordPress dashboard, Settings -> Lifestream, enable Cron updating, then configure cron to download http://you.com/WordPress/wp-content/linickx-lifestream/run.php

= How do I configure Cron to update my Feeds ? =
I've got a file called *linickx-lifestream* in /etc/cron.d with this in it...

`*/5 * * * * nick wget -q --spider http://www.linickx.com/wp-content/plugins/linickx-lifestream/run.php`

your mileage may vary tho.

= What is the "Database Size / Expiry" option ? =
LINICKX LifeStream downloads your feeds and saves them to a database, you get to decide how much is stored

= Where is the Feed Database ? =
By default it goes into WordPress, but if you have lots of big feeds this could slow things down. To speed things up, change to a *file* database, **make sure wp-content is writable** and you'll see your feeds saved in `lnx_lifestream_feeddb.txt` get created.  

= What does "To Use Cron Updating you must create config.php, see FAQ or Config.Sample.php for more details" Mean? = 
If you want to run linickx-lifestream from cron then the plugin needs to know how to find WordPress. To find WordPress the plugin looks for config.php in ~/wp-content/linickx-lifestream , with the package there is an example config.sample.php. Rename config.sample.php to config.php and change the variable $WPDIR to where your wordpress is instlled, there are a couple of examples in there.

= What is the Post Fail-Safe ? =
Version 0.1.x had a frustrating double-post bug where by some feed items would not be saved in the feed DB thus be posted to WP multiple times. I cannot work out if this is a Bug in my plugin, wordpress, simplepie or even PHP so I cam up with this fail-safe mechanism. As of 0.2 Lifestream posts will be created with some meta_data in a key called `lnx_lifestream_id` if a feed ID matches this key then a new post will not be created.

= Can the Post Fail-Safe be overridden? =
Just like in your fav action movie there is a manual override where by you can force these skipped posts to be created. You will need to be using Cron mode and web-broswe to domain.com/wp-content/plugins/linickx-lifestream/run.php?fsoverride=1 ... remember to see what cron mode is doing enable the `Verbose` option in the dashboard.

= Can I post the Full Content, not just links? =
What you need is [FeedWordPress](http://wordpress.org/extend/plugins/feedwordpress/ "FeedWordPress by Charles Johnson") ;-)

== Screenshots ==

1. The Admin interface, where you set up the magic !

== Changelog ==

= 0.2.1 =
* Admin Pg Updates, including Context Help.
* Fail without Bail for RSS Feeds.
* Post Format Support

= 0.2 =
* Troubleshooting Feature-Pack
 * Factory Reset
 * Datadump - URL/Feeds
 * Datadump - Lifestream DB
 * Verbose Cron Mode
* Double-Post Fail-Safe

= 0.1.3 =
* Fix WordPres AutoMagic Updates broken by 0.1.2
* Try to fix execution issues with max time and is_running check
* Introduce config.php to resolve upgrade issues

= 0.1.2 =
* Patch to fix cron error in dashboard

= 0.1.1 =
* Patch to fix error - include_once error

= 0.1 =
* Initial Release

== Upgrade Notice ==

= 0.2.1 =
Optional Upgrade, worth it for TwentyEleven users.
