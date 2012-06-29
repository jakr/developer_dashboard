<?php
/**
 * This object is similar to a FieldList. It contains 
 *  FormField and FormAction elements.
 * An action handler can be assigned to each FormField and FormAction element. 
 */
class DashboardPanel {
	private $panelName;
	private $fields;
	
	public function __construct($panelName, $fields = null){
		if($fields == null) {
			$fields = new FieldList();
		} else if(!($fields instanceof FieldList)){
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
	
	public function addFormField($formField){
		if(!($formField instanceof FormField)){
			throw new InvalidArgumentException('$formField must be a FormField');
		}
		$this->fields->add($formField);
	}
}