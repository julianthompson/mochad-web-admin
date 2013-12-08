<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

define('MOCHAD_CLIENT_NEWLINE', "\n");

class mochad_client {

	private $host = "127.0.0.1";
	private $port = "1099";
	private $socket = NULL;
	private $status = array();
	private $selected = array();
	
	
	function __construct($host="127.0.0.1",$port="1099") {
		$this->host = $host;
		$this->port = $port;
		$this->connect();	
	}
	
	
	function connect() {
		if (gettype($this->socket)!='resource') {
			$url = "tcp://{$this->host}:{$this->port}";
			$this->socket = stream_socket_client($url, $errno, $errstr, 0);   
		}	
	}


	function close() {
		fclose($this->socket); 
		$this->socket = NULL;
	} 
	
	
	function readresponse($length, $end) {
		
		// get file pointer
		$current = ftell($this->socket);
		
		$str = fread($this->socket, $length);
		
		$responses = preg_split('/\n|\r/', $str, -1, PREG_SPLIT_NO_EMPTY);

		foreach($responses as $key=>$response) {
		  $pattern = '@^([0-9]{2}\\/[0-9]{2}\\s[0-9]{2}:[0-9]{2}:[0-9]{2})\\s+(.*)$@i';
			$result = preg_match( $pattern, $response , $matches );
			$responseobj = new StdClass;
			$responseobj->timestamp = strtotime($matches[1]);
			$responseobj->data = trim($matches[2]);
			$responses[$key] = $responseobj;
			//if (preg_match($responseobj->response,))
		}
		return $responses;
		/*
		$i = strpos($str, $end);
		if ($i === FALSE) {
		  return $str;   
		} else {
		  fseek($fp, $current + $i + strlen($end));
		  return substr($str, 0, $i);
		}
		*/
	}


	function sendcommand($command) {
	  //echo 'sendcommand'.$command;
	  $this->connect();
	  if (!is_array($command)) $command = array($command);
	  $commandimploded = implode(MOCHAD_CLIENT_NEWLINE, $command) . MOCHAD_CLIENT_NEWLINE;
		fwrite($this->socket, $commandimploded);
    stream_set_timeout($this->socket, 1);
    usleep(800);
    $responses = $this->readresponse(1000000, "End status");
    //usleep(200);
    //$this->close(); 
    return $responses;
	}


	function getstatus() {
		$command = 'st';
		$responses = $this->sendcommand($command);
	  $responseobj = new StdClass();
  	$responseobj->command = $command;
		$responseobj->response = $this->process_response_status($responses);
		
		return $responseobj;		
	}

	
	function setstate($targets,$state,$level=FALSE) {
	  if (!is_array($targets)) {
	  	$targets = array($targets);
	  }
	  $commands = array();
	  $zones = array();
	  foreach($targets as $devicecode) {
	  	if (preg_match('@^([A-Za-z])(\d+)$@',$devicecode,$matches)) {
	  		$commands[] = "pl {$devicecode}";
	  		$zones[($matches[1])]=TRUE;
	  	}
	  }
	  
	  foreach ($zones as $zonename=>$zone) {
	  	$commands[] = "pl ".$zonename." ".($state?'on':'off');
	  }
	  
	  $response = $this->sendcommand($commands);

	  $responseobj = new StdClass();
  	$responseobj->command = $commands;
  	$responseobj->response = $this->process_response_onoff($response);
		
		return $responseobj;
	}
	
	
	private function process_response_onoff($responses) {
	  return $responses;
	}
	
		
	private function process_response_status($responses) {

		$responselen = count($responses);

		for($index=0; $index < $responselen; $index++) {

		  $response = $responses[$index];
		  
			switch($response->data) {
				case 'Device selected':
					//echo "Device selected data :\n";
					$index++;
					$this->selected = array();
					if (isset($responses[$index])) {
						while (preg_match('@^House\s([A-Z]):\s(\d+)$@', $responses[$index]->data, $matches)) {
							//$data = $responses[$index]->data;
							$this->selected[] = strtolower($matches[1]) . $matches[2];  
							//echo print_r($matches,TRUE);
							$index++;
						}
					}
					$index--;
				break;
				
				case 'Device status':
					//echo "Device status data :\n";
					$index++;
					while (preg_match('@^House\s([A-Z]):\s(.*)$@', $responses[$index]->data, $matches)) {
					  $housecode = $matches[1];
						$status = explode(',',$matches[2]);
						foreach($status as $value) {
							if (preg_match('@(\d+)=(\d+)@',$value, $keyvalue)) {
								$this->status[$housecode][($keyvalue[1])] = $keyvalue[2];
							}
						}
						//echo print_r($matches,TRUE);
						$index++;
					}
					$index--;
				break;
				
				case 'Security sensor status':
					//echo "Security sensor status :\n";
					$index++;
					while (preg_match('@^House\s([A-Z]):\s(.*)$@', $responses[$index]->data, $matches)) {
					  $housecode = $matches[1];
						$status = explode(',',$matches[2]);
						foreach($status as $value) {
							if (preg_match('@(\d+)=(\d+)@',$value, $keyvalue)) {
								$this->status[$housecode][($keyvalue[1])] = $keyvalue[2];
							}
						}
						//$data = $responses[$index]->data;
						//echo print_r($matches,TRUE);
						$index++;
					}
					$index--;
				break;
				
				case 'End status':
					//echo "End status : OK\n";
				break;
				
				default :
					//echo ($response->data).':';
				
			
			}
			
			//return 'xxxx';//$this->status;
			//echo $response->data;
		}
		
		//print_r($this->status);
		//return "ok";
		return $this->status;
	}

}

