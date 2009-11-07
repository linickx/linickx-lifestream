=== Plugin Name ===
Contributors: linickx
Donate link: http://www.linickx.com/index.php?content=donate
Tags: lifestream, feed
Requires at least: 2.8.4
Tested up to: 2.8.5
Stable tag: 0.1.2

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

= What does "Can't find Wordpress, edit $WPDIR in run.php" Mean? = 
If you want to run linickx-lifestream from cron then the plugin needs to know how to find WordPress. By Default the plugin guesses that your plugin is installed in /wp/ but your blog might not be installed there so you'll have to edit run.php. If your blog is in /blog/ for example change like so..
Old run.php...

`$WPDIR = "/wp";`

New run.php...

`$WPDIR = "/blog";`

and that error message should go away.

== Screenshots ==

1. The Admin interface, where you set up the magic !

== Changelog ==

= 0.1 =
* Initial Release

= 0.1.1 =
* Patch to fix error - include_once error

= 0.1.2 =
* Patch to fix cron error in dashboard
