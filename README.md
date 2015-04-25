PlexMonitor - 0.1.0alpha
===================

Designed to monitor a local server and network with forecast.io, Plex, and pfSense integration.

Supports MacOSX, Linux, and Windows.

[Live site][ls]

[Plex forum thread][pft]

[ls]: http://d4rk.co/
[pft]: http://forums.plexapp.com/index.php/topic/84856-network-status-page/

###Installation
---------------
Setting up PlexMonitor for your first time isn't hard. There are a few moving parts that need to get configured first but none of them are hard and within a few minutes you can have a basic website working. To start with setup a PHP environment in a webserver of your choice. As these options vary depending on what OS you're on I won't cover them here. After your PHP installation is complete and you've verified it works download the latest zip of the PlexMonitor-Master. This will always be the most up-to-date stable branch of the codebase.

There are a few steps we're now going to walkthrough to get your instance running properly.
* Specify a path to your config.ini file.
* Modify the config.ini file to suite your needs.

##### Set your config.ini path
Find the functions.php file in the folder structure you just unzipped in assets/php/functions.php. Open the file and edit the section at the top to specify a path to your config.ini.

```
<?php
$config_path = '../../config.ini'; //path to config file, recommend you place it outside of web root
Ini_Set('display_errors', false);
...
```

This is the setup that will work out of the box with your install of PlexMonitor. The issue is the current path happens to be something that is being served by your web server and will be accessible to the public outside. You should change your path to point to somewhere outside of this folder. I have mine pointing to C:\PlexMonitor\config.ini. After you choose where to put your path you need to move the config.ini file to this new location.

##### Set up the config.ini file.

Everything at this point in the config.ini file is optional at this point. To get any functionality you will need to modify the fields to fit your setup. None of the fields are set out of the box and to get a working installation you'll need to tell PlexMonitor where all of your tools are located, your Plex name and password, IP address, etc.

When you've modified everything you need navigate to the page in a browser and test. If it works, great! If it doesn't come to the forums for help.

###Features
---------------
* Responsive web design viewable on desktop, tablet and mobile web browsers 

* Designed using [Bootstrap 3][bs]

* Uses jQuery to provide near real time feedback

* Optimized for Apple devices  `Tested on OS X 10.9/10.10 and iOS 7/8`

* Displays the following:
	* currently playing items from Plex Media Server
	* current network bandwidth from pfSense
	* current ping to ip of your choosing, e.g. Google DNS
	* online / offline status for custom services
	* minute by minute weather forecast from forecast.io
	* server load
	* total disk space for all hard drives

* Now Playing section adjusts scrollable height on the fly depending on browser window height


[bs]: http://getbootstrap.com


###Screenshots
---------------
![alt tag](http://i.imgur.com/94GVDPM.png)

![alt tag](http://d.pr/i/1eTEu+)


###Requirements
---------------
* [Plex Media Server][pms] (v0.9.8+) and a [myPlex][pp] account `These are both free.`
* The weather sidebar requires a [forecast.io API key][fcAPI] `Free up to 1000 calls/day.`
* Web server that supports php (apache, nginx, XAMPP, WampServer, EasyPHP, lighttpd, etc)
* PHP 5.4

**Note:** While this project is written with OS X in mind, it can very easily be adapted to run on linux or windows by rewriting the functions that don't work on those platforms.

[pms]: https://plex.tv
[pp]: https://plex.tv/subscription/about
[fcAPI]: https://developer.forecast.io


###Optional
---------------
* A few functions are written to be used with the following software but they are optional:
	* [SABnzbd+][sab]
	* [pfSense][pfs]

[sab]: http://sabnzbd.org
[pfs]: http://www.pfsense.org
