<?php
/**
 * Data object for a log message.
 */
class DashboardLogMessage extends ViewableData {
	public $StreamID;
	public $Timestamp;
	public $Message;
	public $XMLSafeMessage;
	
	/**
	 * Construct from a Zend_Log message.
	 * @param array $event a Zend_Log message
	 * @param string $streamID
	 */
	public function __construct($event, $streamID){
		$this->Message = $event['message'];
		$this->XMLSafeMessage = isset($event['XMLSafe']) ? $event['XMLSafe'] : null;
		$this->StreamID = $streamID;
		$this->Timestamp = $event['timestamp'];
	}
	
	/**
	 * Cast the object to a string.
	 * @return string
	 */
	public function __toString(){
		return "[{$this->StreamID}] {$this->Timestamp} {$this->Message}";
	}
	
	/**
	 * Get an XML safe representation of this object.
	 * @return string
	 */
	public function toXML(){
		return "[{$this->StreamID}] {$this->Timestamp} "
			.$this->XMLSafeMessage != null ? $this->XMLSafeMessage 
				: Convert::raw2xml($this->Message);
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