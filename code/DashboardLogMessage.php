<?php
class DashboardLogMessage {
	public $streamID;
	public $time;
	public $message;
	public function __construct($message, $streamID){
		$this->message = $message;
		$this->streamID = $streamID;
		$this->time = time();
	}
	
	public function __toString(){
		return "[{$this->streamID}] {$this->time} {$this->message}";
	}
}