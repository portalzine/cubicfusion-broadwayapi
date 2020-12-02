<?php
class BroadwayAPI{
		# Broadway network IP
		public $stream_ip		= "192.168.1.46";
		public $user 			= 'User';
		public $user_pin 		= '0000';
		
		# http://+IP+/TVC/user/data/profiles/m2ts
		# http://+IP+/TVC/user/data/profiles/flv
				     
		public $stream_profile 	= "";
		public $stream_caching	= 800;
		
		#Broadway channellist to use
		public $channel_list	= 1;
				
		static $channels;
		public $playlist;
		public $epg;
		static $channelEPG = array();	
		
		public $rename;
		public $config;			
		
		function __construct() {
			
			if(file_exists("resources/config.php")){
				
				$config = include("resources/config.php");
				$this->config = $config;
				
				$this->stream_ip 		= $config["stream_ip"];
				$this->stream_profile 	= $config["stream_profile"];
				$this->stream_caching 	= $config["stream_caching"];
				$this->channel_list 	= $config["channel_list"];	
				$this->user_pin 		= $config["user_pin"];		
			}
			
			if(file_exists("resources/rename.php")){
				$this->rename = include("resources/rename.php");
			}
		}
		
		/*
			Load Config
		*/
		function getConfig(){
			return $this->getData("http://".$this->user.":".md5($this->user_pin)."@".$this->stream_ip."/TVC/user/data/config");
		}
		function getTuners(){			
			return $this->getData("http://".$this->user.":".md5($this->user_pin)."@".$this->stream_ip."/TVC/free/data/tuners/1");	
		}
		/*
			Load Broadway profiles
		*/
		function getStreamProfiles(){
			
			$m2ts 	=  $this->getData("http://".$this->user.":".md5($this->user_pin)."@".$this->stream_ip."/TVC/user/data/profiles/m2ts");
			$flv 	=  $this->getData("http://".$this->user.":".md5($this->user_pin)."@".$this->stream_ip."/TVC/user/data/profiles/flv");
			
			return array('m2ts' => $m2ts,
						 'flv'  => $flv);	
		}
		
		/*
			Load Broadway channel listing / JSON
		*/
		function getChannelListing(){
			$listing =  $this->getData("http://".$this->user.":".md5($this->user_pin)."@".$this->stream_ip."/TVC/user/data/tv/channellists/");	
			
			foreach($listing as $list){
			
				$list->Items =  $this->getData("http://".$this->user.":".md5($this->user_pin)."@".$this->stream_ip."/TVC/user/data/tv/channellists/".$list->Id);	
				$data[] = $list;	
			}
			return $data;
		}
		
		/*
			Load Broadway Channellist / JSON
		*/
		function getChannelList(){
			return $this->getData("http://".$this->user.":".md5($this->user_pin)."@".$this->stream_ip."/TVC/user/data/tv/channellists/".$this->channel_list);	
		}
		
		/*
			Load Broadway Channel EPG / JSON
		*/

		function getChannelEPG($id){
			if ($this->channelEPG[$id] !== NULL  )
        		return $this->channelEPG[$id];
			
			$data = $this->getData("http://".$this->user.":".md5($this->user_pin)."@".$this->stream_ip."/TVC/user/data/epg/?ids=" .$id.  "&extended=1");
			
			$this->channelEPG[$id] = $data;
			
			return $data;	
		}
		
		/*
			Cleanup and combine channel list / epg
		*/
		function getChannels($addEPG = true){
			
			if ($this->channels !== NULL && $addEPG != true  )
        		return $this->channels;
		
			$data = array();
			
			$list = $this->getChannelList();
			
			foreach((array)$list->Channels as $channel){
				if($addEPG === true){
					$prep 			= $this->getChannelEPG($channel->Id);
	 				$entries		= $prep[0]->Entries;
	 				$channel->EPG 	= $entries;
				}
				
				$data[] 		= $channel;
			}
			
			$this->channels =  $data;
			return 	 $data;
		}

		/*
			Build Playlist
			- Images use the name of the channel with empty spaces or slashes replaced width "-"
			- In XBMC the image folder is defined in the IPTV Simple PVR Addon
		*/
		function buildPlaylist(){
			
			 $data = $this->getChannels(false);
			 
			 $playlist = "#EXTM3U\n";

                foreach($data as $d ){
                   
                        $playlist .="#EXTINF:-1 tvg-logo=\"".$this->cleanString($d->DisplayName) .".png\", ".$this->renameChannel($d->DisplayName) ."\n";
                        $playlist .=  "http://".$this->user.":".md5($this->user_pin)."@".$this->stream_ip."/basicauth/TVC/Preview?channel=".$d->Id . "&profile=".$this->stream_profile."&caching=".$this->stream_caching."\n";
				}
				
			// Add radio in the future
			/*	$playlist .="#EXTINF:-1 radio=\"true\", NDR 2\n";
				$playlist .="http://ndr-ndr2-nds-mp3.akacast.akamaistream.net/7/400/252763/v1/gnl.akacast.akamaistream.net/ndr_ndr2_nds_mp3";
			*/
			$this->playlist = $playlist;
		}

		/*
			Export Playlist
		*/
		function exportPlaylist($filename = "broadway.m3u"){
			
			$this->buildPlaylist();
			
			file_put_contents($filename, $this->playlist);	
		}
		
		/*
			Build EPG
			- Improvising on the category, as the Broadway EPG does not provide any further information			
		*/
		function buildEPG(){
			
			$data = $this->getChannels(true);
			
			$epg ="<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
            $epg .="<tv>\n";

               foreach($data as $d ){
                    $epg .="<channel id=\"".$d->Id . "\">\n";
                    $epg .=" <display-name>".$this->renameChannel($d->DisplayName) ."</display-name>\n";				
                    $epg .="</channel>\n";
			   }
			
			   foreach($data as $d ){
					
					 foreach($d->EPG as $prog ){
			            if(preg_match("/,/", $prog->ShortDescription, $matches)){
							$split = explode(",",  $prog->ShortDescription);
							if(count($split) == 3){
									$subtitle = $split[0]." / ".$split[2];
									$cat = $split[1];
							}else{
									$subtitle = $split[1];
									$cat = $split[0];
							}
							
							if(empty($cat)){
								$cat = "News";	
							}
							
							$epg .="<programme start=\"".date("YmdHis",($prog->StartTime/1000))." +0200\" stop=\"".date("YmdHis",($prog->EndTime/1000))." +0200\" channel=\"".$d->Id."\">\n";                           
							$epg .=" <title>".$this->xml_entities($prog->Title)." (".$this->xml_entities($subtitle).")</title>\n";
							$epg .=" <desc>".$this->xml_entities($prog->LongDescription)."</desc>\n";
							$epg .=" <category>".$this->xml_entities($cat) ."</category>\n";
							$epg .="</programme>\n";	
						}else{ 
					   		$epg .="<programme start=\"".date("YmdHis",($prog->StartTime/1000))." +0200\" stop=\"".date("YmdHis",($prog->EndTime/1000))." +0200\" channel=\"".$d->Id."\">\n";                           
					   		$epg .=" <title>".$this->xml_entities($prog->Title)."</title>\n";
					   		$epg .=" <desc>".$this->xml_entities($prog->LongDescriptio)."</desc>\n";
					   		$epg .=" <category>".$this->xml_entities($prog->ShortDescription)."</category>\n";
					   		$epg .="</programme>\n";
					 	}
					 }
				}
 			$epg .="</tv>\n";
			
 			$this->epg =  $epg;
		}
		
		function renameChannel($channel){
			if(!empty($this->rename[$channel])){
				return 	trim($this->rename[$channel]);
			}	
			return $channel;
		}
		/*
			Export EPG
		*/
		function exportEPG($filename = "broadway_epg.xml"){
			
			$this->buildEPG();
			
			file_put_contents($filename, $this->epg);	
		}
		
		function checkForBroadway(){		
			
			$url =  "http://".$this->user.":".md5($this->user_pin)."@".$this->stream_ip."/TVC/user/data/tv/channellists"; 
						
			switch($this->getResponseCode($url)){
				case 200:
					return true;
				break;
				
			}	
			return false;
			
		}
		/* Check, if stream is occupied */
		
		function isStreamAvailable(){
			
			$tuner = $this->getTuners();
			if($tuner->Status->TuningStatus != "Tuned") return true;
			return false;
		}
		
		function getResponseCode($url) {
    		$headers = get_headers($url);
    		return substr($headers[0], 9, 3);
		}
		
		function getRedirectsToUri($uri)
		{
			$redirects = array();
			$http = stream_context_create();
			stream_context_set_params(
				$http,
				array(
					"notification" => function() use (&$redirects)
					{
						if (func_get_arg(0) === STREAM_NOTIFY_REDIRECTED) {
							$redirects[] = func_get_arg(2);
						}
					}
				)
			);
			file_get_contents($uri, false, $http);
			return $redirects;
		}
		
		function cleanString($string){
  			 $upas = Array("ä" => "ae", "ü" => "ue", "ö" => "oe", "Ä" => "Ae", "Ü" => "Ue", "Ö" => "Oe", "/"=> "-"," "=> "-"); 
 			 return  strtr($string, $upas);
  }
		
		/*
			Fetch data
		*/
		function getData($url){
			
			$ch = curl_init();

			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			
			curl_setopt($ch, CURLOPT_URL, $url);
			
			$result=curl_exec($ch);
		
			curl_close($ch);		
			
			return json_decode($result);
		}
		
		function xml_entities($string) {
		  return strtr(
			  $string, 
			  array(
				  "<" => "&lt;",
				  ">" => "&gt;",
				  '"' => "&quot;",
				  "'" => "&apos;",
				  "&" => "&amp;",
			  )
		  );
	}
}