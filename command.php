<?php
	include "./classes/class.mochad_client.php";
	include "./classes/class.JSObject.php";
	include "./config.php";
	
	$mochadclient = new mochad_client();
	$mochadclient->dummyoutput = MOCHAD_DUMMY_OUTPUT;
	$command = 'st';
	$response = new StdClass;
	$target = isset($_GET['t'])?trim($_GET['t']):FALSE;
	
	//die($target);
	switch($_GET['c']) {

		case 'on' :
			$response = $mochadclient->setstate($target,TRUE);
	    break;

		case 'off' :
			$response = $mochadclient->setstate($target,FALSE);
	    break;
	    
		case 'xdim' :
			$response = $mochadclient->setstate($target,FALSE,$_GET['l']);
	    break;
	    
	  case 'zonecommand' :
			$response = $mochadclient->setstate($target,FALSE,$_GET['l']);	  	
	  	break;  
	}	
	
  print json_encode($response);

  $mochadclient->close();
?>
