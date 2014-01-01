<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

define('MOCHAD_CLIENT_NEWLINE', "\n");

class mochad_client {

	private $host = "127.0.0.1";
	private $port = "1099";
	private $socket = NULL;
	public $status = array();
	private $selected = array();
	public $dummyoutput = FALSE;
	
	
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
		
		if ($this->dummyoutput) {
			$str = "01/01 20:43:33 Device status\n01/01 20:43:33 House A: 1=0,2=0,3=0\n01/01 20:43:33 House B: 1=0,3=0\n01/01 20:43:33 Security sensor status\n01/01 20:43:33 End status";			
		} else {
			// get file pointer
			$current = ftell($this->socket);
			$str = fread($this->socket, $length);
		} 
		
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
	  if (!$command) $command=array();
	  if (!is_array($command)) $command = array($command);
	  $command[] = 'st';
	  $commandimploded = implode(MOCHAD_CLIENT_NEWLINE, $command) . MOCHAD_CLIENT_NEWLINE;
		fwrite($this->socket, $commandimploded);
    stream_set_timeout($this->socket, 1);
    usleep(800);
    $responses = $this->readresponse(1000000, "End status");
    //$this->close(); 
	$this->process_responses($responses);
	$responseobj = new StdClass();
  	$responseobj->command = $command;
  	$responseobj->response = $responses;
	$responseobj->status = $this->status;
	return $responseobj;
	}


	function getstatus() {
		return $this->sendcommand(FALSE);
	}

	
	function setstate($targets,$state,$level=FALSE) {
	  if (!is_array($targets)) {
	  	$targets = array($targets);
	  }
	  $commands = array();
	  $zones = array();
	  foreach($targets as $devicecode) {
	  	if (preg_match('@^([A-Za-z])(\d+)$@',$devicecode,$matches)) {
	  		if ($level!==FALSE) {
	  		  $commands[] = "pl {$devicecode} xdim " . $level;
	  		} else {
	  			$commands[] = "pl {$devicecode} ".($state?'on':'off');
	  		}
	  		$zones[($matches[1])]=TRUE;
	  	}
	  }
	  
	  
	  return $this->sendcommand($commands);

	}
	
	

	private function process_responses($responses) {

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
								$this->status[$housecode][($keyvalue[1])] = intval($keyvalue[2]);
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
			
			//return $this->status;
			//echo $response->data;
		}
		
		//print_r($this->status);
		//return "ok";
		return $this->status;
	}

}


