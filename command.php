<?php
	include "./classes/class.mochad_client.php";
	include "./classes/class.JSObject.php";
	include "./config.php";
	
	$mochadclient = new mochad_client();
	$command = 'st';
	$response = new StdClass;
	$target = isset($_GET['t'])?trim($_GET['t']):FALSE;
	//die($target);
	switch($_GET['c']) {

		case 'on' :
			$response = $mochadclient->setstate($target,TRUE);
	    break;

		case 'off' :
		  //$response = 'OFF';
			$response = $mochadclient->setstate($target,FALSE);
	    break;
	    
		case 'xdim' :
			$response = $mochadclient->setstate($target,FALSE,$_GET['l']);
	    break;

		case 'dim' :
			$command = "pl {$target} dim 10";
			$response = $mochadclient->sendcommand($command);	
	    break;
	    
		case 'bright' :
			$command = "pl {$target} bright 10";
			$response = $mochadclient->sendcommand($command);	
	    break;
	    
	}	
	
  //$response->status = $mochadclient->getstatus();
  print json_encode($response);
  //print json_encode($_GET);
  $mochadclient->close();
?>
