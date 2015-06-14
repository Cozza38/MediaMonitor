<h4 class="panel-title exoextralight"
	<?php
	ini_set('display_errors', true);
	include '../../init.php';
	include 'functions.php';

	$transcode_sessions = getTranscodeSessions();

	if ( $transcode_sessions > 0 )
	{
		echo ' style="margin-left:23px"';
		var_dump($transcode_sessions);
	};
	?>
	>
	<?php
	if ( $transcode_sessions > 0 )
	{
		echo '<span id="transcodeSessions" class="badge pull-right" rel="tooltip" data-toggle="tooltip" data-placement="bottom" title="Transcode Sessions" style="width:23px">' . $transcodeSessions . '</span>';
	};
	?>
	Load
</h4>
