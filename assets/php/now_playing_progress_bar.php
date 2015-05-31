<?php
ini_set('display_errors', true);
include '../../init.php';
include 'functions.php';

$plexSessionXML = SessionCache();

$plexSessionID = $_GET['id'];

$duration   = $plexSessionXML->Video[$plexSessionID]['duration'];
$viewOffset = $plexSessionXML->Video[$plexSessionID]['viewOffset'];
$progress   = sprintf('%.0f', ( $viewOffset / $duration ) * 100);

return $progress;
