<?php

include("class.BroadwayAPI.php");

$Broadway = new BroadwayAPI();
// Your Broadway IP
$Broadway->stream_ip 		= "192.168.1.46";

// See class for available streaming profiles. If empty the raw stream will be used.
$Broadway->stream_profile 	= "";

// Channel list to use 
$Broadway->channel_list 	= 1;

// Save playlist to file
$Broadway->exportPlaylist("broadway.m3u");

// Save EPG data to file
$Broadway->exportEPG("broadway_epg.xml");