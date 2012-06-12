<?php
class DashboardTab extends ViewableData {
	public $ID;
	public $Title;
	
	public function __construct($tabTitle, $tabID = ''){
		$this->ID = ($tabID != '') ? $tabID : $tabTitle; 
		$this->Title = $tabTitle;
	}
	
	public function getContent(){
		return "Example Content";
	}
}