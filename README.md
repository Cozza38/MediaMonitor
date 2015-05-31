# MediaMonitor  
Simple homepage designed to monitor a local Plex & Usenet Server

## Features
* Responsive web design viewable on desktop, tablet and mobile web browsers.
* Designed using Bootstrap 3.
* Uses jQuery to provide near real time feedback.
* Optimized for Apple devices Tested on OS X 10.9/10.10 and iOS 7/8.

##### Displays the following:
* Currently playing items from Plex Media Server.
* Recently added items from Plex Media Server.
* Active transcoding sessions.
* Online / Offline Status for:
    * Plex Media Server
    * SABnzbd
    * Sonarr
    * CouchPotato
    *  (Planned) SickBeard/SickRage
* Minute by minute weather forecast from forecast.io.
* Recently viewed items via trakt.tv (If Plex is offline).
* Server load.
* Total disk space for all hard drives.
* Now Playing section adjusts scrollable height on the fly depending on browser window height.

## Screenshots

##### Recently Added View
![RecentlyAdded](http://i.imgur.com/hPdJwaB.png)
##### Service & Basic SABnzbd Status
![ServiceStatis](http://i.imgur.com/OLiwfni.png) ![SabStatus](http://i.imgur.com/JjQoHv9.png)
##### Trakt.tv 'Recently Viewed' (Shows when/if Plex is offline)
![RecentlyViewed](http://i.imgur.com/w91LQCc.png)
##### Now Playing
![NowPlaying](http://i.imgur.com/dofaO5Q.png)

## Installation
#### Protect your private directories
Protect your private directories from prying eyes or get them out of a web accessible area.
##### Apache
```
<Directory "${US_ROOTF}/private">
   Order deny,allow
   Deny from all
   Allow from localhost
</Directory>
```
##### Nginx
Add something like this to your .conf
```
location ^~ /private/ {
    deny all;
}
```
##### Install twig with composer.
Jump onto your local server and run install.phar with php. You may need to supply the full path to you php installation depending on OS.
```
php composer.phar install
```
##### Modify the config.ini file to suite your needs.
Make a copy of config_default.ini, anme it config.ini and place it where you want it to go (It defaults to /private/ but It can be updated in functions.php)

The config.ini file is well documented, unless you have a more complicated setup it should be all you need.

To get any functionality you will need to modify the fields to fit your setup. None of the fields are set out of the box and to get a working installation you'll need to tell MediaMonitor where all of your tools are located, your Plex name and password, IP address, etc.

When you've modified everything you need, navigate to the page in a browser and test. If it works, great! If it doesn't feel free to post a github issue.

##### Thanks
* D4rk22 for his original network status page - https://github.com/d4rk22/Network-Status-Page
* Vastinator for porting to PlexMonitor - https://github.com/vanstinator/PlexMonitor
