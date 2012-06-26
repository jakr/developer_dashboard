<?php
class AutomaticRefreshButton extends FormAction {
	public function __construct($action, $title = "", $form = null){
		parent::__construct($action, $title, $form);
	}
	
	public function Field($properties=array()){
		$innerData = parent::Field($properties);
		$fieldData = $innerData;
		return $fieldData;
	}
}