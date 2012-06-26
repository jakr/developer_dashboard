<?php
class DashboardForm extends Form {
	
	private $callbacks;
	
	public function __construct($controller, $name, $fields, $actions, $validator = null){
		parent::__construct($controller, $name, $fields, $actions, $validator);
	}
	
	public function add_panel($panel){
		if(!($panel instanceof DashboardPanel)){
			throw new InvalidArgumentException('$panel must be a DashboardPanel');
		}
		//Get information from $panel and add it to our form
		$panelFields = $panel->Fields()->toArray();
		//@TODO: Preserve tab hierarchy.
		//@TODO: Remap controller
		foreach($panelFields as $formField){
			$this->fields->addFieldToTab('Root.'.$panel->GetName(), $formField);
		}
	}
	
}