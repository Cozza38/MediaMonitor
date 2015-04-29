<!DOCTYPE html>
<?php
ini_set('display_errors', false);
include '../../init.php';
include ROOT_DIR . '/assets/php/functions.php';
?>
<html lang="en">
<script>
	// Enable bootstrap tooltips
	$(function () {
		$("[rel=tooltip]").tooltip();
	});
</script>
<!-- Ups Status -->
<?php
echo '<div class="exolight">';
echo 'Line Voltage: ' . findUpsValue('LINEV') . ' volts';
echo '<br><br>';
echo 'Time Left: ' . findUpsValue('TIMELEFT') . ' minutes';
echo '<br><br>';
makeUpsBars();
?>