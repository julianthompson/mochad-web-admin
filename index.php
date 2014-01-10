<?php
	include "./classes/class.mochad_client.php";
	include "./classes/class.JSObject.php";
	include "./config.php";

	$channels = array('All'=>'');
	
	$mochadclient = new mochad_client();
	$mochadclient->dummyoutput = MOCHAD_DUMMY_OUTPUT;
  $responseobj = $mochadclient->getstatus();
  $mochadclient->close();
  
  $networkname = isset($networkname)?$networkname:'X10';
  
  $title = $networkname . ' Appliance Control';

?>

<html>

	<head>
		<title><?php print $title;?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1"/>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.2/jquery-ui.min.js"></script>
		<script src="http://netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
		<script src="./script/bootstrap-switch/build/js/bootstrap-switch.js"></script>
		<script src="./script/slider/js/bootstrap-slider.js"></script>
		<script src="./script/jquery.base.js"></script>

		<link rel="stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css">
		<link rel="stylesheet" href="http://netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap-theme.min.css">
		<link rel="stylesheet" href="./script/bootstrap-switch/build/css/bootstrap3/bootstrap-switch.css">
		<link rel="stylesheet" href="./style/base.css"/>
		<link rel="stylesheet" href="./script/slider/css/bootstrap-slider.css">
		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css">
	</head>
	
	<body>

		<h1>X10 Appliance Control</h1>
		
		<?php //print_r($devices);?>
	
		<?php foreach ($devices as $housecode => &$device) : ?>
	
		<?php $channels[(strtoupper($device->housecode))] = $device->housecode; ?>
		
		<?php $devicestate = (isset($responseobj->status[$housecode])) ? $responseobj->status[$housecode] : new stdClass; ?>
		<?php if (!isset($devicestate->status)) $devicestate->status = 0;?>
		
		<?php //print_r($devicestate);?>
	
		<div class="node device <?php print ($devicestate->status?'status-on':'status-off'); ?>" data-id="<?php print $housecode;?>">
		
			<?php if (MOCHAD_CLIENT_DEBUG) : ?>
			<pre><?php print_r($device);?></pre>
			<?php endif; ?>
			
			<h2><?php print $device->title;?> <span class="housecode"><?php print strtoupper($housecode);?></span> <?php if ($device->dimmable) : ?><span class="brightnesslevel" id="node-<?php print $housecode;?>-brightness"><?php print ($devicestate->level);?>%</span><?php endif;?></h2>
			<div class="location">
				<?php
					$zonenames = array_intersect_key($zones,array_flip($device->zones));
					print implode(', ',$zonenames);
				?>
			</div>
			<div class="controls">
				<div class="checkbox-wrapper make-switch onoffswitch" data-target="<?php print $housecode;?>">
			  <input type="checkbox" value="1" <?php print ($devicestate->status)?"checked":"";?> />	
			  </div>		
			  
				<?php if ($device->dimmable) : ?>
				<div class="dimmer-group">
				  <i class="fa fa-moon-o dimbulb"></i>
					<div class="dimmer-wrapper">
						<input class="dimmer" id="dimmer-<?php print $housecode;?>" data-slider-id='slider-<?php print $housecode;?>' type="text" data-slider-min="0" data-slider-max="100" data-slider-enabled="<?php print ($devicestate->status)?'true':'false';?>" data-slider-step="5" data-slider-value="<?php print ($devicestate->level);?>"/>
					</div>
				  <i class="fa fa-sun-o brightbulb"></i>
				</div>
				<?php endif;?>
				
			</div>
		
		</div>
		
		<?php endforeach; ?>
	
		<?php if (FALSE) : ?>
		<?php foreach ($zones as $zonename) : ?>
		<div class="channel">
			<h2><?php print $zonename;?></h2>
			<div class="controls">
				<a href="./command.php?t=<?php print $zonename;?>&c=all_units_on" class="button">All on</a>
				<a href="./command.php?t=<?php print $zonename;?>&c=all_units_off" class="button">All off</a>
				<a href="./command.php?t=<?php print $zonename;?>&c=all_lights_on" class="button">Lights on</a>
				<a href="./command.php?t=<?php print $zonename;?>&c=all_lights_off" class="button">Lights off</a>
			</div>
		</div>
		
		<?php endforeach; ?>
		<?php endif; ?>	
	
		<div class="command">
			<pre id="responsedata"><?php print json_encode($responseobj);?></pre>
		</div>

		
	</body>

</html>
<?php
//exec( 'sudo shutdown -r now', $output, $return_val );

//print_r( $output );
//echo "\n";
//echo 'Error: '. $return_val ."\n";
