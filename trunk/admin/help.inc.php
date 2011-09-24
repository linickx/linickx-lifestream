<p><strong>LINICKX LifeStream</strong> This plugin allows you to lifestream any feed. Simply load up your RSS feeds into the plugin and posts will be created in wordpress.
</p>
<p>This software is provided free of charge, support is provided by the WordPress comminity <a href="http://wordpress.org/tags/linickx-lifestream">in this forum</a> and you can <a href="http://wordpress.org/tags/linickx-lifestream#postform">use this form to ask questions and get support</a>. <br />
If you are a bit of a wizard, you can use the WordPress trac for feature request,  <a href="http://plugins.trac.wordpress.org/newticket?component=linickx-lifestream&owner=linickx"><strong>Open a ticket here for patch sumission and bug reports.</strong></a>. <br />
Remember to show your love to WordPress plugin developers by <a href="http://www.linickx.com/donate">dontaing to the author</a>.
</p>
<p>
<strong>Basic Usage:</strong><br />
Usage is quite straight forward, fill in some RSS/ATOM feed URLs and click save URLS, I am not so good with the javascript so you will have to enter each url one at a time to get a new box. <br />
The other options, category, tags, post format customize the post that is created. If you do not use post formats select standard, if you use twenteleven then link works quite well!
</p>
<p>
<strong>General Options:</strong><br />
The following settings can be changed to tweak the behaviour of the plugin:
</p>
<ul>
	<li>
	Update Method: <br />
	WordPress automatic is the default, basically every 5 mins (by default) during a request to your WordPress website a feed fetch will happen in the background. <br />
	Cron is a way of manually controlling the updates, basically you configure a cron job to make a specail HTTP request which triggers a feed fetch, more infomaton can be found in the <a href="http://wordpress.org/extend/plugins/linickx-lifestream/faq/">faq</a>.
	</li>
	<li>
	Update Interval: <br />
	If you are using automatic updates, this defines how often (In Minutes) a feed fetch happens.
	</li>
	<li>
	Verbose: <br />
	If you are using cron updates, this defines if the page (run.php) should output anything, it is useful to have this on during debugging put probabbly wants to be switched off.
	</li>
	<li>
	Database Size/Expirey & Location <br />
The database is a cache of the feeds to determine if a post should be created or not; You can choose whether this cache should be on the disk (<em>as a file</em>) or in the wordpress database. The size/Expirey define how new an entry must be for a post to be created, if you set this to 10days then feed items created 11 or more days ago will be ignored. These features may be depeceated in future version as a meta tag is also used to define if a post exists or not.
	</li>
</ul>
<p>
<strong>Debug:</strong><br />
If you get into trouble there are some things you can do.
</p>
<ul>
	<li>
	Show the feed database: <br />
	Click the link to show a page which dumps the local database of feeds, this is useful for understanding what is being downloaded from a given RSS/ATOM feed.
	</li>
	<li>
	Show the feed/URLs: <br />
	Click the link to show a page which dumps the configuration of each feed so that you can confirm you settings are being saved.
	</li>
	<li>
	Factory Reset <br />
	Use this to remove the lifestream settings from WordPress, this <u>does not delete</u> any posts but will remove the feed database and the general options.
	</li>
</ul>