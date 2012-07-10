<?php
class AutomaticRefreshButton extends FormAction {
	public function __construct($action, $title = "", $form = null){
		parent::__construct($action, $title, $form);
	}
	
	public function Field($properties=array()){
		$this->addExtraClass('btn');
		$innerData = parent::Field($properties);
		$fieldData = $innerData;
		return $fieldData;
	}
}