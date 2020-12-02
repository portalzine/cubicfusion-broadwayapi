<?php 

	include("class.BroadwayAPI.php");

	$Broadway = new BroadwayAPI();

	$listings 			= $Broadway->getChannelListing();
	$config 			= $Broadway->config;
	$rename 			= $Broadway->rename;

	$Broadway->exportPlaylist("broadway.m3u");
	echo date("d.m.Y H:i")." Done - Playlist\n";
	
	// Save EPG data to file
	$Broadway->exportEPG("broadway_epg.xml");
	
	echo date("d.m.Y H:i")." Done - EPG";