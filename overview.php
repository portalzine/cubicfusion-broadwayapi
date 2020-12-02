<?php

include("class.BroadwayAPI.php");

$Broadway = new BroadwayAPI();

$listings 			= $Broadway->getChannelListing();
$config 			= $Broadway->config;
$rename 			= $Broadway->rename;

$stream_profiles 	= $Broadway->getStreamProfiles();

$streamAvailable 	= $Broadway->isStreamAvailable();
$broadwayAvailable 	= $Broadway->checkForBroadway();

if(!empty($_POST['action'])){
	
	switch($_POST['action']){
	case "save_rename":
		header("content-type:application/json");
		$ini = "<?php\n #Rename channels\n\n return array(\n\n";
		
		foreach($_POST['rename'] as $key => $val){
				if(!empty($val)) $ini .=	"'".utf8_decode($key)."' => '".$val."',\n";
		
		}
		$ini .= "\n\n);";
		file_put_contents("resources/rename.php", $ini);
		$return['done'] = 1;
		$return["json"] = json_encode($return);
		echo json_encode($return);
		exit;
	break;
	case "save_config":
		header("content-type:application/json");
		
		$ini = "<?php\n #BroadwayAPI configuration\n\n return array(\n\n";
		
		foreach($_POST['config'] as $key => $val){
				 $ini .=	"'".utf8_decode($key)."' => '".$val."',\n";
		
		}
		$ini .= "\n\n);";
		
		file_put_contents("resources/config.php", $ini);
		
		$return['done'] = 1;
		$return["json"] = json_encode($return);
		echo json_encode($return);
		exit;
	break;
	case "build":
		header("content-type:application/json");
		
		// Save playlist to file
		$Broadway->exportPlaylist("broadway.m3u");
	
		// Save EPG data to file
		$Broadway->exportEPG("broadway_epg.xml");
		
		$return['done'] = 1;
		$return["json"] = json_encode($return);
		echo json_encode($return);
		exit;
	break;
	case "check_stream":
		header("content-type:application/json");		
		$return['streamAvailable'] = $Broadway->isStreamAvailable();
		$return["json"] = json_encode($return);
		echo json_encode($return);
		exit;
	
	break;
	
	}
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>BroadwayAPI (PHP)</title>
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<link rel="stylesheet" href="/resources/css/bootstrap.min.css">
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
<link rel="stylesheet" href="/resources/css/main.css">
<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
 <script src="//code.jquery.com/ui/1.11.1/jquery-ui.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script src="/resources/js/main.js"></script>
</head>
<body><br>
<div class="container"> <img src="/resources/images/logo.png" width="400">
  <div class="navbar navbar-default" role="navigation">
    <div class="container-fluid">
   
      <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse"> <span class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span class="icon-bar"></span> <span class="icon-bar"></span> </button>
       </div>
      <div class="navbar-collapse collapse">
        <ul class="nav navbar-nav">
      <li class="dropdown">
    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
      Broadway <span class="caret"></span>
    </a>
    <ul class="dropdown-menu" role="menu">
    <li><a href="http://<?php echo $Broadway->stream_ip; ?>" target="_blank">LiveTV</a></li>
          <li ><a href="http://<?php echo $Broadway->stream_ip; ?>/TVC.1343/ui/broadway/Admin.html" target="_blank">Admin</a></li>
          <li ><a href="http://<?php echo $Broadway->stream_ip; ?>/TVC.1343/ui/Settings.html" target="_blank">Settings</a></li>
    </ul>
  </li>
          
          <li data-toggle="tooltip" data-placement="bottom" title="Update Playlist & EPG files"><a href="#" class="updateLocalFiles">Update Files</a></li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
          <li ><a href="https://bitbucket.org/portalzine/cubicfusion-broadwayapi" target="_blank">Repository</a></li>
        </ul>
      </div>
    </div>
  </div>
  <div class="spinner">
    <div class="rect1"></div>
    <div class="rect2"></div>
    <div class="rect3"></div>
    <div class="rect4"></div>
    <div class="rect5"></div>
  </div>
  <ul class="nav nav-tabs" role="tablist">
    <li class="active" ><a href="#home" role="tab" data-toggle="tab">Home</a></li>
    <li><a href="#setting" role="tab" data-toggle="tab">Settings</a></li>
    <li><a href="#list" role="tab" data-toggle="tab">Channel Lists</a></li>
    <li><a href="#logos" role="tab" data-toggle="tab">Channels & Logos</a></li>
    <li><a href="#add" role="tab" data-toggle="tab">Add Streams</a></li>
  </ul>
  <div class="tab-content">
  <div class="tab-pane " id="add">
      <div class="jumbotron">
     <h2>Add Stream</h2>
     <small>Add radio and video streams to your playlist.</small><br><br>
      <form role="form" method="POST" action="">
  <div class="form-group">
    <label for="name">Name</label>
    <input type="text" class="form-control" id="name" name="add[name]" placeholder="">
  </div>
   <div class="form-group">
    <label for="name">Stream</label>
    <input type="text" class="form-control" id="stream" name="add[stream]" placeholder="">
  </div>
   <div class="form-group">
    <label for="name">Logo</label>
    <input type="text" class="form-control" id="logo" name="add[logo]" placeholder="">
  </div>
  <div class="checkbox-inline">
    <label>
      <input type="radio" name="add[type]" value="video"> Video 
    </label>
  </div>
   <div class="checkbox-inline" >
    <label>
      <input type="radio" name="add[type]" value="radio"> Radio
    </label>
  </div>
  <div class="clearfix"></div><br><br>
  <button type="submit" class="btn btn-default btn-block">Submit</button>
</form>
<br><hr ><br>
 <h2>Video</h2>
        <table class="table table-striped sortable" >
         <tr>
              <th>Name</th>
              <th>Stream</th>
              <th>Logo</th>
            </tr>
           
        </table><br>
        <h2>Radio</h2>
        <table class="table table-striped sortable">
         <tr>
              <th>Name</th>
              <th>Stream</th>
              <th>Logo</th>
            </tr>
          
        </table>
      </div>
    </div>
    <div class="tab-pane active" id="home">
      <div class="jumbotron">
        <h2>Status</h2>
        <div class="btn-group"> <button id="networkStatus" type="button" <?php if($broadwayAvailable){echo 'class="btn btn-default"><span class="glyphicon glyphicon glyphicon-ok"></span> Broadway live';}else{ echo 'class="btn-danger"><span class="glyphicon glyphicon glyphicon-remove"></span>Broadway not live' ;} ?>
          </button>
          <button type="button" id="streamStatus" <?php if($streamAvailable){echo 'class="btn btn-default"><span class="glyphicon glyphicon glyphicon-ok"></span> Stream available';}else{echo 'class="btn btn-danger"><span class="glyphicon glyphicon glyphicon-remove"></span> Stream in use' ;}  ?> 
          </button>
        </div>
        <br><br>
         <h2>Exports</h2>
        <table class="table table-striped">
         <tr>
              <td>Playlist (m3u)<br>
                <span class="label label-primary"><?php echo date("d.m.Y H:i:s.", filectime("broadway.m3u")); ?></span></td>
              <td><input class='form-control' type='text' value='http://<?php echo $_SERVER["SERVER_NAME"]; ?>/broadway.m3u'></td>
            </tr>
            <tr>
              <td>XMLTV<br>
                <span class="label label-primary"> <?php echo date("d.m.Y H:i:s.", filectime("broadway_epg.xml")); ?></span></td>
              <td><input class='form-control' type='text' value='http://<?php echo $_SERVER["SERVER_NAME"]; ?>/broadway_epg.xml'></td>
            </tr>
        </table>
         <br><br>
         <h2>Stream-Profiles m2ts</h2>
         <table class="table table-striped">
          <thead>
          <tr>
              <th>ID</th>
               <th>Video Bitrate</th>
               <th>Audio Bitrate</th>
              <th>Dimension</th>
            </tr>
            </thead>
		 <?php foreach($stream_profiles['m2ts'] as $m2ts){?>
         <tr>
              <td><?php echo $m2ts->Id; ?></td>
               <td><?php echo $m2ts->{'Video.Bitrate'}; ?></td>
               <td><?php echo $m2ts->{'Audio.Bitrate'}; ?></td>
              <td><?php echo $m2ts->{'Width'}; ?>x<?php echo $m2ts->{'Height'}; ?></td>
            </tr>
           <?php }?>
        </table>
        <br><br>
         <h2>Stream-Profiles flv</h2>
         <table class="table table-striped">
          <thead>
          <tr>
              <th>ID</th>
               <th>Video Bitrate</th>
               <th>Audio Bitrate</th>
              <th>Dimension</th>
            </tr>
            </thead>
		 <?php foreach($stream_profiles['flv'] as $m2ts){?>
         <tr>
              <td><?php echo $m2ts->Id; ?></td>
               <td><?php echo $m2ts->{'Video.Bitrate'}; ?></td>
               <td><?php echo $m2ts->{'Audio.Bitrate'}; ?></td>
              <td><?php echo $m2ts->{'Width'}; ?>x<?php echo $m2ts->{'Height'}; ?></td>
            </tr>
           <?php }?>
        </table>
      </div>
    </div>
    <div class="tab-pane" id="setting">
      <div class="jumbotron">
        <h2>Settings</h2>
        <form role="form" id="configForm">
          <table class="table table-striped">
            <?php
	
		
	  foreach($config as $key => $var){
		  if($key == "user_pin"){
			  echo "<tr>
		<td>TV-PIN <br>
<small>default: 0000</small></td>
		<td><input class='form-control' type='password' value='0000' name='config[".$key."]'></td>		
		</tr>";
			  }elseif($key =="channel_list"){
				 
		
		echo "<tr>
		<td>".$key."</td><td><select class='form-control' name='config[".$key."]'><option value=''>Choose</option>";
		 foreach($listings as $list){
			 echo "<option value='".$list->Id."'";
			 if($var ==$list->Id) echo " selected";
			 echo">".$list->DisplayName."</option>";
		}
		echo"</select></td>		
		</tr>";
		
				  }
				  elseif($key =="stream_profile"){
				 
		
		echo "<tr>
		<td>".$key."</td><td><select class='form-control' name='config[".$key."]'><option value=''>Choose</option>";
		foreach($stream_profiles['m2ts'] as $m2ts){
			 echo "<option value='".$m2ts->Id."'";
			 if($var ==$m2ts->Id) echo " selected";
			 echo">".$m2ts->Id." / ".$m2ts->{'Video.Bitrate'}."</option>";
		}
		
		echo"</select></td>		
		</tr>";
		
				  }
			  
			  else{
echo "<tr>
		<td>".$key."</td>
		<td><input class='form-control' type='text' value='".$var."' name='config[".$key."]'></td>		
		</tr>";
			  }
	  }
		?>
           
          </table>
        </form>
      </div>
    </div>
    <div class="tab-pane" id="list">
      <div class="jumbotron">
        <h2>Channellists</h2>
        <table class="table table-striped">
          <?php
echo "<thead><tr>

		<th>ID</td>
		<th>NAME</td>
		<th>CHANNELS</td>
		
		</tr></thead><tbody>";
		
foreach($listings as $list){
		echo "<tr class='success'>
		<td>".$list->Id."</td>
		<td>".$list->DisplayName."</td>
		<td>".$list->Count."</td>
		</tr>";
		
		
		echo "<tr>
		
		<td colspan='3'>";
		foreach($list->Items->Channels as $channel){
			echo '<span class="label label-primary">'.$channel->DisplayName.'</span> ';
		}
		echo "</td>
		
		</tr></tbody>";
}
?>
        </table>
      </div>
    </div>
    <div class="tab-pane" id="logos">
      <div class="jumbotron">
        <h2>Channel Logos</h2> <form id="logoForm">
        <table class="table table-striped">
           
          
          <?php
echo "<thead><tr >

		<th>ID</td>
		<th>NAME</td>
		<th>CHANNELS</td>
		
		</tr></thead><tbody>";
		
foreach($listings as $list){
		echo "<tr class='success'>
		<td>".$list->Id."</td>
		<td>".$list->DisplayName."</td>
		<td>".$list->Count."</td>
		</tr>";
		
		
		
		foreach($list->Items->Channels as $channel){echo "<tr>
		
		<td colspan='2'>";
			echo '<h4>'.utf8_decode($channel->DisplayName).'</h4>
<small>[ID: '.$channel->Id.' / '.$Broadway->cleanString($channel->DisplayName) .'.png]</small><br>
<input class="form-control" type="text" name="rename['.$channel->DisplayName.']" value="'.$rename[$channel->DisplayName].'" placeholder="Rename"></td>';
			if(file_exists($config['channel_logos'].$Broadway->cleanString($channel->DisplayName) .".png")){
			echo 	"<td class='active'><center><img width='80' src='/".$config['channel_logos'].$Broadway->cleanString($channel->DisplayName) .".png'></center></td>";
			}else{
				echo 	"<td class='danger'>Missing: ".$Broadway->cleanString($channel->DisplayName) .".png</td>";
				}
		echo "</td></tr>";
		}
		
	
		
		echo "</tbody>";
}
?>
        </table>
        </form>
      </div>
    </div>
  </div>
  <center><img src="/resources/images/tai.jpg">
    <small>&copy; Copyright 2014 <a href="http://www.portalzine.de" target="_blank">portalZINE NMN</a> / Alexander Graef. All rights reserved.</small>
  </center>
  <br>
  <br>
</div>

</body>
</html>
