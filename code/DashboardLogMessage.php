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
	
	public function toXML(){
		return (string)$this;
		//return '<p class="">'.(string)$this.'</p>';
	}
	
	/**
	 * Get the fields that are defined in this class
	 *  and return them as a key to value map.
	 * Ignores the fields defined in the parent class ViewableData.
	 * This lets our class pretend to be a DataObject. Used in testing.
	 */
	public function toMap(){
		$data = array();
		$myFields = get_class_vars(get_called_class());
		$parentFields = get_class_vars('ViewableData');
		foreach(array_keys($myFields) as $field){
			if(array_key_exists($field, $parentFields)){
				continue;
			}
			$data[$field] = $this->$field;
		}
		return $data;
	}
}