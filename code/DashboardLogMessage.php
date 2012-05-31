<?php
class DashboardLogMessage {
	public $streamID;
	public $timestamp;
	public $message;
	
	/**
	 * Construct from a Zend_Log message.
	 * @param array $event a Zend_Log message
	 * @param string $streamID
	 */
	public function __construct($event, $streamID){
		$this->message = $event['message'];
		$this->streamID = $streamID;
		$this->timestamp = $event['timestamp'];
	}
	
	public function __toString(){
		return "[{$this->streamID}] {$this->timestamp} {$this->message}";
	}
}