<?php
	include "./classes/class.mochad_client.php";
	include "./classes/class.JSObject.php";
	include "./config.php";

	$channels = array('All'=>'');
	
	$mochadclient = new mochad_client();
	$mochadclient->dummyoutput = MOCHAD_DUMMY_OUTPUT;
  $responseobj = $mochadclient->getstatus();
  $mochadclient->close();
  foreach ($devices as &$device) {
    $housecode = strtoupper($device->housecode);
    $code = $device->code;
    if (isset($responseobj->status[$housecode][$code]->status)) {
  		$device->status = $responseobj->status[$housecode][$code]->status==1?TRUE:FALSE;
  	} else {
  		$device->status = FALSE;
  	}
  }

?>

<html>

	<head>
		<title>X10 Appliance Control</title>
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
	
		<?php foreach ($devices as $housecode => &$device) : ?>
	
		<?php $channels[(strtoupper($device->housecode))] = $device->housecode; ?>
	
		<div class="node device <?php print ($device->status?'status-on':'status-off'); ?>" data-id="<?php print $housecode;?>">
		
			<?php if (MOCHAD_CLIENT_DEBUG) : ?>
			<pre><?php print_r($device);?></pre>
			<?php endif; ?>
			
			<h2><?php print $device->title;?> <span class="housecode"><?php print strtoupper($housecode);?></span> <?php if ($device->dimmable) : ?><span class="brightnesslevel"><?php print ($device->status)?100:0;?>%</span><?php endif;?></h2>
			<div class="location"><?php print $device->location;?></div>
			<div class="controls">
				<div class="checkbox-wrapper make-switch onoffswitch" data-target="<?php print $housecode;?>">
			  <input type="checkbox" value="1" <?php print ($device->status)?"checked":"";?> />	
			  </div>			
				<?php if ($device->dimmable) : ?>
				<div class="dimmer-group">
				  <i class="fa fa-moon-o dimbulb"></i>
				  <!--
					<a href="./command.php?t=<?php print $housecode;?>&c=dim" data-command="dim" class="button button-dim">-</a> 
					-->
					<div class="dimmer-wrapper">
						<input class="dimmer" id="dimmer-<?php print $housecode;?>" data-slider-id='slider-<?php print $housecode;?>' type="text" data-slider-min="0" data-slider-max="100" data-slider-enabled="<?php print ($device->status)?'true':'false';?>" data-slider-step="5" data-slider-value="<?php print ($device->status)?100:0;?>"/>
					</div>
				  <i class="fa fa-sun-o brightbulb"></i>
					<!--
					<a href="./command.php?t=<?php print $housecode;?>&c=bright" data-command="bright" class="button button-bri">+</a> 
					-->
				</div>
				<?php endif;?>
				<?php if (FALSE)  : ?>
				<span>Pretend these don't exist ---></span>
				<a href="./command.php?t=<?php print $housecode;?>&c=on" data-command="on" class="button button-on">On</a> 
				<a href="./command.php?t=<?php print $housecode;?>&c=off" data-command="off" class="button button-off">Off</a> 
				<?php endif; ?>
			</div>
		
		</div>
		
		<?php endforeach; ?>
	
	
		<?php foreach ($channels as $channelname=>$channel) : ?>
	
		<div class="channel">
			<h2><?php print $channelname;?></h2>
			<div class="controls">
				<a href="./command.php?t=<?php print $channel;?>&c=all_units_on" class="button">All on</a>
				<a href="./command.php?t=<?php print $channel;?>&c=all_units_off" class="button">All off</a>
				<a href="./command.php?t=<?php print $channel;?>&c=all_lights_on" class="button">All lights on</a>
				<a href="./command.php?t=<?php print $channel;?>&c=all_lights_off" class="button">All lights off</a>
				<a href="./command.php?t=<?php print $channel;?>&c=on" class="button">On</a>
				<a href="./command.php?t=<?php print $channel;?>&c=off" class="button">Off</a>
				<a href="./command.php?t=<?php print $channel;?>&c=dim" class="button">-</a>
				<a href="./command.php?t=<?php print $channel;?>&c=bright" class="button">+</a>
			</div>
		</div>
		
		<?php endforeach; ?>
	
	
		<div class="command">
			<h2>Response</h2>
			<hr/>
			<pre id="responsedata"><?php print_r($responseobj);?></pre>
		</div>
		
	</body>

</html>
<?php
//exec( 'sudo shutdown -r now', $output, $return_val );

//print_r( $output );
//echo "\n";
//echo 'Error: '. $return_val ."\n";
