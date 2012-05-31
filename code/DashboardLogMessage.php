<?php
class DashboardLogMessage extends ViewableData {
	public $StreamID;
	public $Timestamp;
	public $Message;
	
	/**
	 * Construct from a Zend_Log message.
	 * @param array $event a Zend_Log message
	 * @param string $streamID
	 */
	public function __construct($event, $streamID){
		$this->Message = $event['message'];
		$this->StreamID = $streamID;
		$this->Timestamp = $event['timestamp'];
	}
	
	public function __toString(){
		return "[{$this->StreamID}] {$this->Timestamp} {$this->Message}";
	}
}