<!DOCTYPE html>
<?php
	Ini_Set( 'display_errors', true );
	include '../../init.php';
	include ROOT_DIR . '/assets/php/functions.php';
	

		makeWeatherSidebar();
		echo '<ul class="nav nav-pills nav-stacked">';
		echo '<!-- Online profiles -->';
		// echo '<li class="exoextralight">Online profiles</li>';
		// echo '<li><a href="http://d4rk.co" target="_blank"><i class="icon-fixed-width icon-globe"/></i> Home</a></li>';
		// echo '<li><a href="http://ryanchristensen.net" target="_blank"><i class="icon-fixed-width icon-cloud"></i> Blog</a></li>';
		// echo '<li><a href="http://twitter.com/ryanchristensen" target="_blank"><i class="icon-fixed-width icon-twitter"></i> Twitter</a></li>';
		echo '<li><a href="#contactModal" data-toggle="modal"><i class="icon-fixed-width icon-envelope"></i> Contact</a></li>';
		echo '</ul>';
?>
