<?php
# BroadwayAPI configuration

return array(
		
	//Broadway IP
	"stream_ip" 		=> "192.168.1.46",
	
	// Broadway TV-PIN
	"user_pin"			=> "0000",
	
	// Channellist to use. see overview.php
	"channel_list" 		=> 1,
	
	// Channel Logos
	// The actual channel logo location is defined in the IPTV Simple Client (example: http://192.168.1.118/resources/logos/)	
	"channel_logos" 	=> "resources/logos/",

	// Broadway stream profiles  / stream quality (default is empty = raw stream)
	// see overview.php
	"stream_profile" 	=>"",
	
	"stream_caching"	=> 800
);	
