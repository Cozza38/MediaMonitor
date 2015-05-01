# MediaMonitor  
Simple homepage designed to monitor a local Plex & Usenet Server 

## Installation
##### Set your config.ini path.
Find the functions.php file in assets/php/functions.php. Open the file and edit the section at the top to specify a path to your config.ini. **Your config.ini file should not be located in a web accessible area**
``` 
$config_path = ROOT_DIR . '../private/config.ini';
```
##### Install twig with composer.
Jump onto your local server and run install.phar with php. You may need to supply the full path to you php installation depending on OS.
``` 
php composer.phar install 
```
##### Modify the config.ini file to suite your needs.
The config.ini file is well documented, unless you have a more complicated setup it should be all you need.

Everything at this point in the config.ini file is optional at this point. To get any functionality you will need to modify the fields to fit your setup. None of the fields are set out of the box and to get a working installation you'll need to tell PlexMonitor where all of your tools are located, your Plex name and password, IP address, etc.

When you've modified everything you need, navigate to the page in a browser and test. If it works, great! If it doesn't feel free to post a github issue.

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
