<!DOCTYPE html>
<?php
ini_set('display_errors', true);
include '../../init.php';
include ROOT_DIR . '/assets/php/functions.php';
include( "service.class.php" );
include( "servicePlex.class.php" );
include( "serviceSAB.class.php" );
?>
<html lang="en">
<script>
	// Enable bootstrap tooltips
	$(function () {
		$("[rel=tooltip]").tooltip();
	});
</script>
<?php
$sabnzbdXML = simplexml_load_file('https://' . $sab_ip . ':' . $sab_port . '/api?mode=qstatus&output=xml&apikey=' . $sabnzbd_api);

if ( ( $sabnzbdXML->state ) == 'Downloading' ):
	$timeleft = $sabnzbdXML->timeleft;
	$sabTitle = 'SABnzbd (' . $timeleft . ')';
else:
	$sabTitle = 'SABnzbd';
endif;

$services = array(
	new servicePlex("Plex", $plex_port, ( 'http://' . $wan_domain . ':' . $plex_port . '/web' ), $plex_server_ip,
		$plexToken),
	new serviceSAB("SABnzbd", $sab_port, ( 'https://' . $domain_name . '/sabnzbd' ), $sab_ip),
	//	new service("SickBeard", 8081, "http://d4rk.co:8081", "10.0.1.3"),
	//	new serviceCouch("CouchPotato", $couch_port, ('https://' . $domain_name . '/movies') , $couch_ip),
	//  new service("Starbound Server", 21025, "http://playstarbound.com"),
);
?>
<table class="center">
	<?php foreach ($services as $service)
	{ ?>
		<tr>
			<td style="text-align: right; padding-right:5px;" class="exoextralight"><?php echo $service->name; ?></td>
			<td style="text-align: left;"><?php echo $service->makeButton(); ?></td>
		</tr>
	<?php } ?>
</table>
