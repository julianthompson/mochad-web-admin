<?php
	define('MOCHAD_CLIENT_DEBUG',FALSE);

	include "./classes/class.mochad_client.php";
	include "./classes/class.JSObject.php";
	include "./config.php";

	$channels = array('All'=>'');
	
	$mochadclient = new mochad_client();
  $responseobj = $mochadclient->getstatus();
  $mochadclient->close();
  foreach ($devices as &$device) {
    $housecode = strtoupper($device->housecode);
    $code = $device->code;
    if (isset($responseobj->response[$housecode][$code])) {
  		$device->status = $responseobj->response[$housecode][$code]==1?TRUE:FALSE;
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
		<script src="http://olance.github.io/jQuery-switchButton/jquery.switchButton.js"></script>
		<script src="./script/jquery.base.js"></script>
		<link rel="stylesheet" href="http://olance.github.io/jQuery-switchButton/jquery.switchButton.css"/>
		<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css"/>
		<link rel="stylesheet" href="./style/base.css"/>
	</head>
	
	<body>

		<h1>X10 Appliance Control</h1>
	
		<?php foreach ($devices as $housecode => &$device) : ?>
	
		<?php $channels[(strtoupper($device->housecode))] = $device->housecode; ?>
	
		<div class="node device <?php print ($device->status?'status-on':'status-off'); ?>" data-id="<?php print $housecode;?>">
		
			<?php if (MOCHAD_CLIENT_DEBUG) : ?>
			<pre><?php print_r($device);?></pre>
			<?php endif; ?>
			
			<h2><?php print $device->title;?> <span class="housecode"><?php print strtoupper($housecode);?></span></h2>
			<div class="location"><?php print $device->location;?></div>
			<div class="controls">
				<div class="checkbox-wrapper">
			  <input type="checkbox" value="1" <?php print ($device->status)?"checked":"";?> />	
			  </div>			
				<?php if ($device->dimmable) : ?>
				<div class="dimmer-group">
					<a href="./command.php?t=<?php print $housecode;?>&c=dim" data-command="dim" class="button button-dim">-</a> 
					<div class="dimmer-wrapper">
						<div class="dimmer" id="dimmer-<?php print $housecode;?>" data-value="<?php print ($device->status)?100:0;?>"></div>
					</div>
					<a href="./command.php?t=<?php print $housecode;?>&c=bright" data-command="bri" class="button button-bri">+</a> 
					<div class="brightness"><?php print ($device->status)?100:0;?>% (?)</div>
				</div>
				<?php endif;?>
				<span>Pretend these don't exist ---></span>
				<a href="./command.php?t=<?php print $housecode;?>&c=on" data-command="on" class="button button-on">On</a> 
				<a href="./command.php?t=<?php print $housecode;?>&c=off" data-command="off" class="button button-off">Off</a> 
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