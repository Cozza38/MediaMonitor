<?php
ini_set('display_errors', true);
include '../../init.php';
include 'functions.php';

$plex_session_xml       = getSession();
$plex_status_file_1     = ROOT_DIR . '/assets/caches/plexstatusfile1.txt';
$plex_status_file_2     = ROOT_DIR . '/assets/caches/plexstatusfile2.txt';
$plex_status_file_1_md5 = md5_file($plex_status_file_1);
$plex_status_file_2_md5 = md5_file($plex_status_file_2);
$plex_progress_file     = ROOT_DIR . '/assets/caches/plexprogress.txt';

// See if Plex Media Server is online and how many people are watching.
if ( !$plex_session_xml ) {
    // If Plex Media Server is offline.
    $plex_status = 'offline';
} else {
    // If Plex Media Server is online.
	$plex_status = 'online';
    // Let's see if someone Is watching something
	$i = 0;
    if ( !count($plex_session_xml->Video) == 0 ) {
        // Someone is watching, get their progress
        foreach($plex_session_xml->Video as $plex_session) {
            $i++; // Increment i every pass through the array
            $duration = $plex_session[$i - 1]['duration'];
            $viewOffset = $plex_session[$i - 1]['viewOffset'];
            $progress = sprintf('%.0f', ($viewOffset / $duration) * 100);
            $progress_array[] = $progress;
        }
    }
}

// Build status array
$status_array = [
	'status'  => $plex_status,
];

// Write the data out to the first Plex check file
if (!file_exists($plex_status_file_1 || $plex_status_file_2)) {
    touch($plex_status_file_1);
    touch($plex_status_file_2);
}
file_put_contents($plex_status_file_1, $status_array, LOCK_EX);
// Check to see if it's the same as the second Plex check file
if ( $plex_status_file_1_md5 == $plex_status_file_2_md5 ) {
	// if they are the same do nothing
} else {
	// If they are different, update plexcheckfile2
	file_put_contents($plex_status_file_2, $status_array, LOCK_EX);
}

// Write out our progress array
if (!file_exists($plex_progress_file)) {
    touch($plex_progress_file);
}
file_put_contents($plex_progress_file, implode(',',$progress_array), LOCK_EX);
