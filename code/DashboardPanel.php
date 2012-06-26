<?php
/**
 * This object is similar to a FieldList. It contains 
 *  FormField and FormAction elements.
 * An action handler can be assigned to each FormField and FormAction element. 
 */
class DashboardPanel {
	private $panelName;
	private $fields;
	
	public function __construct($panelName, $fields){
		if(!($fields instanceof FieldList)){
			throw new InvalidArgumentException('$fields must be a FieldList');
		}
		$this->panelName = $panelName;
		$this->fields = $fields;
	}
	
	public function Fields(){
		return $this->fields;
	}
	
	public function GetName(){
		return $this->panelName;
	}
}