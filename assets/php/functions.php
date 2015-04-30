<?php
include(ROOT_DIR . '/init.php');
Ini_Set('display_errors', false);

$config_path = ROOT_DIR . '../private/config.ini'; //path to config file, recommend you place it outside of web root
$config = parse_ini_file($config_path, true);
$network = $config['network'];
$credentials = $config['credentials'];
$api_keys = $config['api_keys'];
$sabnzbd = $config['sabnzbd'];
$misc = $config['misc'];
$weather = $config['weather'];
$disks = $config['disks'];

// Import variables from config file
// Network
$wan_ip = $network['wan_ip'];
$domain_name = $network['domain_name'];
$plex_server_ip = $network['plex_server_ip'];
$plex_port = $network['plex_port'];

// Credentials
$plex_username = $credentials['plex_username'];
$plex_password = $credentials['plex_password'];
$trakt_username = $credentials['trakt_username'];

// API Keys
$forecast_api = $api_keys['forecast_api'];
$sabnzbd_api = $api_keys['sabnzbd_api'];
$trakt_api = $api_keys['trakt_api'];

// SABnzbd+
$sab_ip = $sabnzbd['sab_ip'];
$sab_port = $sabnzbd['sab_port'];
$ping_throttle = $sabnzbd['ping_throttle'];
$sabSpeedLimitMax = $sabnzbd['sabSpeedLimitMax'];
$sabSpeedLimitMin = $sabnzbd['sabSpeedLimitMin'];

// Misc
$cpu_cores = $misc['cpu_cores'];

// Weather
$weather_lat = $weather['weather_lat'];
$weather_long = $weather['weather_long'];
$weather_name = $weather['weather_name'];
$weather_units = $weather['weather_units'];
$weather_timezone = $weather['weather_timezone'];

// Timezone
if ($weather_timezone != ""){
date_default_timezone_set($weather_timezone);
}

// Disks
$disk = $disks;

// Set the path for the Plex Token
$plexTokenCache = ROOT_DIR . '/assets/caches/plex_token.txt';
// Check to see if the plex token exists and is younger than one week
// if not grab it and write it to our caches folder

include ROOT_DIR . '/assets/php/plex.php';
if (file_exists($plexTokenCache) && (filemtime($plexTokenCache) > (time() - 60 * 60 * 24 * 7))) {
    $plexToken = file_get_contents(ROOT_DIR . '/assets/caches/plex_token.txt');
} else {
    file_put_contents($plexTokenCache, getPlexToken());
    $plexToken = file_get_contents(ROOT_DIR . '/assets/caches/plex_token.txt');
}

$serverMac = 'MAC';
$serverWin = 'WIN';
$serverNix = 'LIN';
$serverOs = strtoupper(substr(php_uname('s'), 0, 3));

// Calculate server load
if (isMac() || isNix()) {
    $loads = sys_getloadavg();
} else if (isWin()) {
    $loads = (win_load_avg());
} else {
    $loads = Array(0.55, 0.7, 1);
}

function win_load_avg()
{
    //CPU
    $dump = shell_exec("wmic cpu get loadpercentage");
    $dump = (preg_split('/\n/', $dump));
    $load[0] = $dump[1];

    //RAM
    $dump = shell_exec("wmic OS get FreePhysicalMemory");
    $dump = (preg_split('/\n/', $dump));
    $load[1] = $dump[1];
    return $load;
}


function isWin()
{
    global $serverOs;
    global $serverWin;
    if ($serverOs == $serverWin) {
        return true;
    }
    return false;
}

function isMac()
{
    global $serverOs;
    global $serverMac;
    if ($serverOs == $serverMac) {
        return true;
    }
    return false;
}

function isNix()
{
    global $serverOs;
    global $serverNix;
    if ($serverOs == $serverNix) {
        return true;
    }
    return false;
}

function isLocal($ipAddress)
{
    if ($ipAddress == 'localhost') {
        return true;
    } else if ($ipAddress == '127.0.0.1') {
        return true;
    } else if ($ipAddress == $_SERVER['SERVER_ADDR']) {
        return true;
    } else {
        return false;
    }
}

function showDiv($div)
{
    switch ($div) {
        case 'services':
            break;
    }
}

function makeTotalDiskSpace()
{
    global $disks;

    $du = 0;
    $dts = 0;

    foreach ($disks as $disk) {
        $disk = preg_split('/,/', $disk);
        $du += disk_total_space($disk[0]) - disk_free_space($disk[0]);
        $dts += disk_total_space($disk[0]);
    }
    $dfree = $dts - $du;
    printTotalDiskBar(sprintf('%.0f', ($du / $dts) * 100), "Total Capacity", $dfree, $dts);
}

function byteFormat($bytes, $unit = "", $decimals = 2)
{
    $units = array('B' => 0, 'KB' => 1, 'MB' => 2, 'GB' => 3, 'TB' => 4,
        'PB' => 5, 'EB' => 6, 'ZB' => 7, 'YB' => 8);

    $value = 0;
    if ($bytes > 0) {
        // Generate automatic prefix by bytes
        // If wrong prefix given
        if (!array_key_exists($unit, $units)) {
            $pow = floor(log($bytes) / log(1000));
            $unit = array_search($pow, $units);
        }

        // Calculate byte value by prefix
        $value = ($bytes / pow(1024, floor($units[$unit])));
    }

    // If decimals is not numeric or decimals is less than 0
    // then set default value
    if (!is_numeric($decimals) || $decimals < 0) {
        $decimals = 2;
    }

    // Format output
    return sprintf('%.' . $decimals . 'f ' . $unit, $value);
}

function autoByteFormat($bytes)
{
    // If we are working with more than 0 and less than 1000GB (Apple filesystem).]
    if (($bytes >= 0) && ($bytes < 1000000000000)) {
        $unit = 'GB';
        $decimals = 0;
        // 1TB to 999TB
    } elseif (($bytes >= 1000000000000) && ($bytes < 1.1259e15)) {
        $unit = 'TB';
        $decimals = 2;
    }
    return array($bytes, $unit, $decimals);
}

function makeDiskBars()
{
    global $disks;

    foreach ($disks as $disk) {
        $disk = preg_split('/,/', $disk);
        printDiskBar(getDiskspace($disk[0]), $disk[1], disk_free_space($disk[0]), disk_total_space($disk[0]));
    }
}

function makeLoadBars()
{
    if (isWin()) {
        printBar(getLoad(0), "CPU");
        printBar(100 - (getLoad(1) / 102400), "RAM");
    } else {
        printBar(getLoad(0), "1 min");
        printBar(getLoad(1), "5 min");
        printBar(getLoad(2), "15 min");
    }
}

function getDiskspace($dir)
{
    $df = disk_free_space($dir);
    $dt = disk_total_space($dir);
    $du = $dt - $df;
    return sprintf('%.0f', ($du / $dt) * 100);
}

function getLoad($id)
{
    global $cpu_cores;

    if (isWin()) {
        return $GLOBALS['loads'][$id];
    } else {
        return 100 * ($GLOBALS['loads'][$id] / $cpu_cores);
    }
}

function printBar($value, $name = "")
{
    if ($name != "") echo '<!-- ' . $name . ' -->';
    echo '<div class="exolight">';
    if ($name != "")
        echo $name . ": ";
    echo number_format($value, 0) . "%";
    echo '<div class="progress">';
    echo '<div class="progress-bar" style="width: ' . trim($value) . '%"></div>';
    echo '</div>';
    echo '</div>';
}

function printRamBar($percent, $name = "", $used, $total)
{
    if ($percent < 90) {
        $progress = "progress-bar";
    } else if (($percent >= 90) && ($percent < 95)) {
        $progress = "progress-bar progress-bar-warning";
    } else {
        $progress = "progress-bar progress-bar-danger";
    }

    if ($name != "") echo '<!-- ' . $name . ' -->';
    echo '<div class="exolight">';
    if ($name != "")
        echo $name . ": ";
    echo number_format($percent, 0) . "%";
    echo '<div rel="tooltip" data-toggle="tooltip" data-placement="bottom" title="' . number_format($used, 2) . ' GB / ' . number_format($total, 0) . ' GB" class="progress">';
    echo '<div class="' . $progress . '" style="width: ' . $percent . '%"></div>';
    echo '</div>';
    echo '</div>';
}

function printDiskBar($dup, $name = "", $dsu, $dts)
{
    // Using autoByteFormat() the amount of space will be formatted as GB or TB as needed.
    if ($dup < 90) {
        $progress = "progress-bar";
    } else if (($dup >= 90) && ($dup < 95)) {
        $progress = "progress-bar progress-bar-warning";
    } else {
        $progress = "progress-bar progress-bar-danger";
    }

    if ($name != "") echo '<!-- ' . $name . ' -->';
    echo '<div class="exolight">';
    if ($name != "")
        echo $name . ": ";
    echo number_format($dup, 0) . "%";
    echo '<div rel="tooltip" data-toggle="tooltip" data-placement="bottom" title="' . byteFormat(autoByteFormat($dsu)[0], autoByteFormat($dsu)[1], autoByteFormat($dsu)[2]) . ' free out of ' . byteFormat(autoByteFormat($dts)[0], autoByteFormat($dts)[1], autoByteFormat($dts)[2]) . '" class="progress">';
    echo '<div class="' . $progress . '" style="width: ' . $dup . '%"></div>';
    echo '</div>';
    echo '</div>';
}

function printTotalDiskBar($dup, $name = "", $dsu, $dts)
{
    // Using autoByteFormat() the amount of space will be formatted as GB or TB as needed.
    if ($dup < 95) {
        $progress = "progress-bar";
    } else if (($dup >= 95) && ($dup < 99)) {
        $progress = "progress-bar progress-bar-warning";
    } else {
        $progress = "progress-bar progress-bar-danger";
    }

    if ($name != "") echo '<!-- ' . $name . ' -->';
    echo '<div class="exolight">';
    if ($name != "")
        echo $name . ": ";
    echo number_format($dup, 0) . "%";
    echo '<div rel="tooltip" data-toggle="tooltip" data-placement="bottom" title="' . byteFormat(autoByteFormat($dsu)[0], autoByteFormat($dsu)[1], autoByteFormat($dsu)[2]) . ' free out of ' . byteFormat(autoByteFormat($dts)[0], autoByteFormat($dts)[1], autoByteFormat($dts)[2]) . '" class="progress">';
    echo '<div class="' . $progress . '" style="width: ' . $dup . '%"></div>';
    echo '</div>';
    echo '</div>';
}

function getNetwork()
{
    // It should be noted that this function is designed specifically for getting the local / wan name for Plex.
    global $wan_ip;
    global $plex_server_ip;

    $clientIP = get_client_ip();
    if ($clientIP == $plex_server_ip):
        $network = 'http://' . $plex_server_ip;
    else:
        $network = 'http://' . $wan_ip;
    endif;
    return $network;
}

function get_client_ip()
{
    if (isset($_SERVER["REMOTE_ADDR"])) {
        $ipaddress = $_SERVER["REMOTE_ADDR"];
    } else if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
        $ipaddress = $_SERVER["HTTP_X_FORWARDED_FOR"];
    } else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
        $ipaddress = $_SERVER["HTTP_CLIENT_IP"];
    }
    return $ipaddress;
}

function getTraktHistory($traktUsername, $type)
{
    global $trakt_api;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,
        "https://api-v2launch.trakt.tv/users/{$traktUsername}/history/{$type}?extended=images");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Content-Type: application/json",
        "trakt-api-version: 2",
        "trakt-api-key: " . $trakt_api . ""
    ));

    $response = curl_exec($ch);

    if ( $response )
    {
        curl_close($ch);

        return json_decode($response);
    }
    else
    {
        $error = curl_error($ch);
        curl_close($ch);

        return $error;
    }
}

function makeRecentlyViewed()
{
    global $trakt_username;

    // $traktMovieHistory = getTraktHistory($trakt_username, 'movies');
    $traktEpisodeHistory = getTraktHistory($trakt_username, 'episodes');
    echo '<div class="col-md-10 col-sm-offset-1">';
    echo '<div id="carousel-example-generic" class=" carousel slide">';
    echo '<div class="thumbnail">';
    echo '<!-- Wrapper for slides -->';
    echo '<div class="carousel-inner">';
    echo '<div class="item active">';
    $i = 0;
    for (; ;) {
        if ($i == 10) break;
        $coverArt = $traktEpisodeHistory[$i]->show->images->poster->full;
        $showTitle = $traktEpisodeHistory[$i]->show->title;
        $seasonNumber = $traktEpisodeHistory[$i]->episode->season;
        $episodeNumber = $traktEpisodeHistory[$i]->episode->number;
        if ($i != 0 ) {
        echo '<div class="item">';
        }
        echo '<img src="' . $coverArt . '">';
        echo '<h3 class="exoextralight" style="margin-top:5px;">' . $showTitle . '</h3>';
        echo '<h4 class="exoextralight" style="margin-top:5px;">Season ' . $seasonNumber . ' - Episode ' . $episodeNumber . '</h4>';
        echo '<a href="http://trakt.tv/user/' . $trakt_username . '">trakt.tv</a>';
        echo '</div>';
        $i++;
    }
    echo '</div>'; // Close carousel-inner div
    echo '</div>'; // Close thumbnail div
    echo '<!-- Controls -->';
    echo '<a class="left carousel-control" href="#carousel-example-generic" data-slide="prev">';
    echo '</a>';
    echo '<a class="right carousel-control" href="#carousel-example-generic" data-slide="next">';
    echo '</a>';
    echo '</div>'; // Close carousel slide div
    echo '</div>'; // Close column div
}

function makeRecentlyAdded()
{
    global $plex_port;
    global $plexToken;
    $network = getNetwork();
    $clientIP = get_client_ip();
    $plexNewestXML = simplexml_load_file($network . ':' . $plex_port . '/library/recentlyAdded/?X-Plex-Token=' . $plexToken);
    $plexNewestXML->registerXPathNamespace("a", XMLNS_SOAPENV);
    echo '<div class="col-md-10 col-sm-offset-1">';
    echo '<div id="carousel-example-generic" class=" carousel slide">';
    echo '<div class="thumbnail">';
    echo '<!-- Wrapper for slides -->';
    echo '<div class="carousel-inner">';
    echo '<div class="item active">';
    // Determine if this is a Movie or TV Show and display the correct result
    $i = 0;
    foreach($plexNewestXML as $key => $media) {
        if ( $media['type'] == 'season' ) {
            // It's a TV Show!
            $episodeParentKey = $media['parentKey'];
            $episodeKey = $media['key'];
            $seasonNumber = $media['title'];
            $mediaXML = simplexml_load_file($network . ':' . $plex_port . $episodeParentKey . '/?X-Plex-Token=' . $plexToken);
            $coverArt = $mediaXML->Directory['thumb'];
            $showTitle = $mediaXML->Directory['title'];
            $episodeXML = simplexml_load_file($network . ':' . $plex_port . $episodeKey . '/?X-Plex-Token=' . $plexToken);
            $episodeXML->registerXPathNamespace("a", XMLNS_SOAPENV);
            $lastEP = $episodeXML->Video[count($episodeXML->Video)-1];
            $episodeNumber = $lastEP['index'];
            // Truncated Summary
            if (countWords($lastEP['summary']) < 51) {
                $showSummary = $lastEP['summary'];
            } else {
                $showSummary = limitWords($lastEP['summary'], 50); // Limit to 50 words
                $showSummary .= "...";
            }
            // Only open this div if it's not the first item
            if ($i != 0 ) {
                echo '<div class="item">';
            }
            // Display coverArt if we have it, otherwise use a placeholder
            if ($coverArt != null) {
                echo '<img src="' . ($network . ':' . $plex_port . $coverArt . '/?X-Plex-Token=' . $plexToken) . '" alt="' . $showTitle . '">';
            } else {
                echo '<img src="assets/img/placeholder.jpg">';
            }
            // Display the show title with season info and summary
            echo '<h3 class="exoextralight" style="margin-top:5px;">' . $showTitle . '</h3>';
            echo '<h4 class="exoextralight" style="margin-top:5px;">' . $seasonNumber . ' - Episode ' . $episodeNumber . '</h4>';
            echo '<p>' . $showSummary . '</p>';
            echo '</div>';
        } elseif ($media['type'] == 'movie' ) {
            // It's a Movie!
            $movieKey = $media['key'];
            $mediaXML = simplexml_load_file($network . ':' . $plex_port . $movieKey . '/?X-Plex-Token=' . $plexToken);
            $coverArt = $mediaXML->Video['thumb'];
            $movieTitle = $mediaXML->Video['title'];
            $movieYear = $mediaXML->Video['year'];
            // Truncated Summary
            if (countWords($mediaXML->Video['summary']) < 51) {
                $movieSummary = $mediaXML->Video['summary'];
            } else {
                $movieSummary = limitWords($mediaXML->Video['summary'], 50); // Limit to 50 words
                $movieSummary .= "...";
            }
            // Only open this div if it's not the first item
            if ($i != 0 ) {
                echo '<div class="item">';
            }
            // Display coverArt if we have it, otherwise use a placeholder
            if ($coverArt != null) {
                echo '<img src="' . ($network . ':' . $plex_port . $coverArt . '/?X-Plex-Token=' . $plexToken) . '" alt="' . $movieTitle . '">';
            } else {
                echo '<img src="assets/img/placeholder.jpg">';
            }
            // // Display the movie title with year and summary
            echo '<h3 class="exoextralight" style="margin-top:5px;">' . $movieTitle . ' (' . $movieYear . ')</h3>';
            echo '<p>' . $movieSummary . '</p>';
            echo '</div>';
        }
        $i++;
    }
    echo '</div>'; // Close carousel-inner div
    echo '</div>'; // Close thumbnail div
    echo '<!-- Controls -->';
    echo '<a class="left carousel-control" href="#carousel-example-generic" data-slide="prev">';
    echo '</a>';
    echo '<a class="right carousel-control" href="#carousel-example-generic" data-slide="next">';
    echo '</a>';
    echo '</div>'; // Close carousel slide div
    echo '</div>'; // Close column div
}

function makeNowPlaying()
{
    global $plex_port;
    global $plexToken;
    $network = getNetwork();
    $plexSessionXML = simplexml_load_file($network . ':' . $plex_port . '/status/sessions?X-Plex-Token=' . $plexToken);

    if (!$plexSessionXML):
        makeRecentlyViewed();
    elseif (count($plexSessionXML->Video) == 0):
        // makeRecentlyViewed();
        makeRecentlyAdded();
    else:
        $i = 0; // Initiate and assign a value to i & t
        $t = 0; // T is the total amount of sessions
        echo '<div class="col-md-10 col-sm-offset-1">';
        //echo '<div class="col-md-12">';
        foreach ($plexSessionXML->Video as $sessionInfo):
            $t++;
        endforeach;
        foreach ($plexSessionXML->Video as $sessionInfo):
            $mediaKey = $sessionInfo['key'];
            $playerTitle = $sessionInfo->Player['title'];
            $mediaXML = simplexml_load_file($network . ':' . $plex_port . $mediaKey . '?X-Plex-Token=' . $plexToken);
            $type = $mediaXML->Video['type'];
            echo '<div class="thumbnail">';
            $i++; // Increment i every pass through the array
            if ($type == "movie"):
                // Build information for a movie
                $movieArt = $mediaXML->Video['thumb'];
                $movieTitle = $mediaXML->Video['title'];
                $duration = $plexSessionXML->Video[$i - 1]['duration'];
                $viewOffset = $plexSessionXML->Video[$i - 1]['viewOffset'];
                $progress = sprintf('%.0f', ($viewOffset / $duration) * 100);
                $user = $plexSessionXML->Video[$i - 1]->User['title'];
                $device = $plexSessionXML->Video[$i - 1]->Player['title'];
                $state = $plexSessionXML->Video[$i - 1]->Player['state'];
                // Truncate movie summary if it's more than 50 words
                if (countWords($mediaXML->Video['summary']) < 51):
                    $movieSummary = $mediaXML->Video['summary'];
                else:
                    $movieSummary = limitWords($mediaXML->Video['summary'], 50); // Limit to 50 words
                    $movieSummary .= "..."; // Add ellipsis
                endif;
                echo '<img src="' . ($network . ':' . $plex_port . $movieArt . '?X-Plex-Token=' . $plexToken) . '" alt="' . $movieTitle . '">';
                // Make now playing progress bar
                //echo 'div id="now-playing-progress-bar">';
                echo '<div class="progress now-playing-progress-bar">';
                echo '<div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="' . $progress . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . $progress . '%">';
                echo '</div>';
                echo '</div>';
                echo '<div class="caption">';
                //echo '<h2 class="exoextralight">'.$movieTitle.'</h2>';
                echo '<p class="exolight" style="margin-top:5px;">' . $movieSummary . '</p>';
                if ($state == "playing"):
                    // Show the playing icon
                    echo '<span class="glyphicon glyphicon-play"></span>';
                else:
                    echo '<span class="glyphicon glyphicon-pause"></span>';
                endif;
                if ($user == ""):
                    echo '<p class="exolight">' . $device . '</p>';
                else:
                    echo '<p class="exolight">' . $user . '</p>';
                endif;
            else:
                // Build information for a tv show
                $tvArt = $mediaXML->Video['grandparentThumb'];
                $showTitle = $mediaXML->Video['grandparentTitle'];
                $episodeTitle = $mediaXML->Video['title'];
                $episodeSummary = $mediaXML->Video['summary'];
                $episodeSeason = $mediaXML->Video['parentIndex'];
                $episodeNumber = $mediaXML->Video['index'];
                $duration = $plexSessionXML->Video[$i - 1]['duration'];
                $viewOffset = $plexSessionXML->Video[$i - 1]['viewOffset'];
                $progress = sprintf('%.0f', ($viewOffset / $duration) * 100);
                $user = $plexSessionXML->Video[$i - 1]->User['title'];
                $device = $plexSessionXML->Video[$i - 1]->Player['title'];
                $state = $plexSessionXML->Video[$i - 1]->Player['state'];
                //echo '<div class="img-overlay">';
                echo '<img src="' . ($network . ':' . $plex_port . $tvArt . '?X-Plex-Token=' . $plexToken) . '" alt="' . $showTitle . '">';
                // Make now playing progress bar
                //echo 'div id="now-playing-progress-bar">';
                echo '<div class="progress now-playing-progress-bar">';
                echo '<div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="' . $progress . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . $progress . '%">';
                echo '</div>';
                echo '</div>';
                //echo '</div>';
                // Make description below thumbnail
                echo '<div class="caption">';
                //echo '<h2 class="exoextralight">'.$showTitle.'</h2>';
                echo '<h3 class="exoextralight" style="margin-top:5px;">Season ' . $episodeSeason . '</h3>';
                echo '<h4 class="exoextralight" style="margin-top:5px;">E' . $episodeNumber . ' - ' . $episodeTitle . '</h4>';
                // Truncate episode summary if it's more than 50 words
                if (countWords($mediaXML->Video['summary']) < 51):
                    $episodeSummary = $mediaXML->Video['summary'];
                else:
                    $episodeSummary = limitWords($mediaXML->Video['summary'], 50); // Limit to 50 words
                    $episodeSummary .= "..."; // Add ellipsis
                endif;
                echo '<p class="exolight">' . $episodeSummary . '</p>';
                if ($state == "playing"):
                    // Show the playing icon
                    echo '<span class="glyphicon glyphicon-play"></span>';
                else:
                    echo '<span class="glyphicon glyphicon-pause"></span>';
                endif;
                if ($user == ""):
                    echo '<p class="exolight">' . $device . '</p>';
                else:
                    echo '<p class="exolight">' . $user . '</p>';
                endif;
            endif;
            // Action buttons if we ever want to do something with them.
            //echo '<p><a href="#" class="btn btn-primary">Action</a> <a href="#" class="btn btn-default">Action</a></p>';
            echo "</div>";
            echo "</div>";
            // Should we make <hr>? Only if there is more than one video and it's not the last thumbnail created.
            if (($i > 0) && ($i < $t)):
                echo '<hr>';
            else:
                // Do nothing
            endif;
        endforeach;
        echo '</div>';
    endif;
}

function getTranscodeSessions()
{
    global $plex_port;
    global $plexToken;
    $network = getNetwork();
    $plexSessionXML = simplexml_load_file($network . ':' . $plex_port . '/status/sessions/?X-Plex-Token=' . $plexToken);

    if (count($plexSessionXML->Video) > 0):
        $i = 0; // i is the variable that gets iterated each pass through the array
        $t = 0; // t is the total amount of sessions
        $transcodeSessions = 0; // this is the number of active transcodes
        foreach ($plexSessionXML->Video as $sessionInfo):
            $t++;
        endforeach;
        foreach ($plexSessionXML->Video as $sessionInfo):
            if ($sessionInfo->TranscodeSession['videoDecision'] == 'transcode') {
                $transcodeSessions++;
            };
            $i++; // Increment i every pass through the array
        endforeach;
        return $transcodeSessions;
    endif;
}

//function getPlexToken()
//{
//    global $plex_username;
//    global $plex_password;
//    $myPlex = shell_exec('curl -H "Content-Length: 0" -H "X-Plex-Client-Identifier: my-app" -u "' . $plex_username . '"":""' . $plex_password . '" -X POST https://my.plexapp.com/users/sign_in.xml 2> /dev/null');
//    $myPlex_xml = simplexml_load_string($myPlex);
//    $token = $myPlex_xml['authenticationToken'];
//    return $token;
//}

function countWords($string)
{
    $words = explode(" ", $string);
    return count($words);
}

function limitWords($string, $word_limit)
{
    $words = explode(" ", $string);
    return implode(" ", array_splice($words, 0, $word_limit));
}

function getDir($b)
{
    $dirs = array('N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW', 'N');
    return $dirs[round($b / 45)];
}

function makeWeatherSidebar()
{
    global $forecast_api;
    global $weather_lat;
    global $weather_long;
    global $weather_units;

    $forecastExcludes = '?exclude=flags'; // Take a look at https://developer.forecast.io/docs/v2 to configure your weather information.
    $currentForecast = json_decode(file_get_contents('https://api.forecast.io/forecast/' . $forecast_api . '/' . $weather_lat . ',' . $weather_long . $forecastExcludes . '&units=' . $weather_units));

    $currentSummary = $currentForecast->currently->summary;
    $currentSummaryIcon = $currentForecast->currently->icon;
    $currentTemp = round($currentForecast->currently->temperature);
    $currentWindSpeed = round($currentForecast->currently->windSpeed);
    if ($currentWindSpeed > 0) {
        $currentWindBearing = $currentForecast->currently->windBearing;
    }
    $minutelySummary = $currentForecast->minutely->summary;
    $hourlySummary = $currentForecast->hourly->summary;

    $sunriseTime = $currentForecast->daily->data[0]->sunriseTime;
    $sunsetTime = $currentForecast->daily->data[0]->sunsetTime;

    if ($sunriseTime > time()) {
        $rises = 'Rises';
    } else {
        $rises = 'Rose';
    }

    if ($sunsetTime > time()) {
        $sets = 'Sets';
    } else {
        $sets = 'Set';
    }

    // If there are alerts, make the alerts variables
    if (isset($currentForecast->alerts)) {
        $alertTitle = $currentForecast->alerts[0]->title;
        $alertExpires = $currentForecast->alerts[0]->expires;
        $alertDescription = $currentForecast->alerts[0]->description;
        $alertUri = $currentForecast->alerts[0]->uri;
    }
    // Make the array for weather icons
    $weatherIcons = [
        'clear-day' => 'B',
        'clear-night' => 'C',
        'rain' => 'R',
        'snow' => 'W',
        'sleet' => 'X',
        'wind' => 'F',
        'fog' => 'L',
        'cloudy' => 'N',
        'partly-cloudy-day' => 'H',
        'partly-cloudy-night' => 'I',
    ];
    $weatherIcon = $weatherIcons[$currentSummaryIcon];
    // If there is a severe weather warning, display it
    //if (isset($currentForecast->alerts)) {
    //	echo '<div class="alert alert-warning alert-dismissable">';
    //	echo '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
    //	echo '<strong><a href="'.$alertUri.'" class="alert-link">'.$alertTitle.'</a></strong>';
    //	echo '</div>';
    //}
    echo '<ul class="list-inline" style="margin-bottom:-20px">';
    echo '<li><h1 data-icon="' . $weatherIcon . '" style="font-size:500%;margin:0px -10px 20px -5px"></h1></li>';
    echo '<li><ul class="list-unstyled">';
    echo '<li><h1 class="exoregular" style="margin:0px">' . $currentTemp . '°</h1></li>';
    echo '<li><h4 class="exoregular" style="margin:0px;padding-right:10px;width:80px">' . $currentSummary . '</h4></li>';
    echo '</ul></li>';
    echo '</ul>';
    if ($currentWindSpeed > 0) {
        $direction = getDir($currentWindBearing);
        if ($weather_units == si) {
            echo '<h4 class="exoextralight" style="margin-top:0px">Wind: ' . $currentWindSpeed . ' m/s from the ' . $direction . '</h4>';
        } else if ($weather_units != us and $weather_units != uk) {
            echo '<h4 class="exoextralight" style="margin-top:0px">Wind: ' . $currentWindSpeed . ' km/h from the ' . $direction . '</h4>';
        } else {
            echo '<h4 class="exoextralight" style="margin-top:0px">Wind: ' . $currentWindSpeed . ' mph from the ' . $direction . '</h4>';
        }
    } else {
        echo '<h4 class="exoextralight" style="margin-top:0px">Wind: Calm</h4>';
    }
    echo '<h4 class="exoregular">Next Hour</h4>';
    echo '<h5 class="exoextralight" style="margin-top:10px">' . $minutelySummary . '</h5>';
    echo '<h4 class="exoregular">Next 24 Hours</h4>';
    echo '<h5 class="exoextralight" style="margin-top:10px">' . $hourlySummary . '</h5>';
    echo '<h4 class="exoregular">The Sun</h4>';
    echo '<h5 class="exoextralight" style="margin-top:10px">' . $rises . ' at ' . date('g:i A', $sunriseTime) . '</h5>';
    echo '<h5 class="exoextralight" style="margin-top:10px">' . $sets . ' at ' . date('g:i A', $sunsetTime) . '</h5>';
    // echo '<p class="text-right no-link-color" style="margin-bottom:-10px"><small><a href="http://forecast.io/#/f/' . $weather_lat . ',' . $weather_long . '">Forecast.io</a></small></p> ';
}

?>
