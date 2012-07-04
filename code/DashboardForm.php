<?php
class DashboardForm extends Form {
	
	private $callbacks = array();
	
	public function __construct($controller, $name, $fields, $actions, $validator = null) {
		parent::__construct($controller, $name, $fields, $actions, $validator);
		$controllerClassname = get_class($controller);
		if(substr($this->FormAction(), 0, strlen($controllerClassname)) == $controllerClassname){
			$this->setFormAction(Director::absoluteURL($this->FormAction()));
		}
	}
	
	public function addPanel(DashboardPanel $panel) {
		//Get information from $panel and add it to our form
		//@TODO: Preserve tab hierarchy.
		$callbacks = $panel->Callbacks();
		
		foreach($panel->Fields()->items as $formField){
			if($formField instanceof FormAction){
				$actionName = $formField->actionName();
				if(isset($callbacks[$actionName])){
					$this->callbacks[$actionName] = $callbacks[$actionName];
				} else {
					$this->callbacks[$actionName] = null;
				}
			}
			$this->fields->addFieldToTab('Root.'.$panel->getName(), $formField);
		}
	}
	
	public function findField($panelName, $fieldName) {
		return $this->fields->fieldByName("Root.$panelName.$fieldName");
	}
	
	/**
	 * Check if we have a callback for the action in this request and call that.
	 * Otherwise we call the parent function
	 */
	public function httpSubmission($request) {
		$vars = $request->requestVars();
		
		// Determine the action button clicked. Duplicated from parent method.
		$funcName = null;
		foreach($vars as $paramName => $paramVal) {
			if(substr($paramName,0,7) == 'action_') {
				// Break off querystring arguments included in the action
				if(strpos($paramName,'?') !== false) {
					list($paramName, $paramVars) = explode('?', $paramName, 2);
					$newRequestParams = array();
					parse_str($paramVars, $newRequestParams);
					$vars = array_merge((array)$vars, (array)$newRequestParams);
				}
				
				// Cleanup action_, _x and _y from image fields
				$funcName = preg_replace(array('/^action_/','/_x$|_y$/'),'',$paramName);
				break;
			}
		}
		//Check if a callback for this action exists and redirect the call.
		if(isset($this->callbacks[$funcName])){
			$callback = $this->callbacks[$funcName];
			if($callback != null){
				$return = false;
				if(is_array($callback)){
					$return = call_user_func($callback, $this, $request);
				} else {
					$return = $callback($vars, $this, $request);
				}
				if($return === true || ($return != null && $return !== '')){
					return $return;
				} else {
					$this->controller->redirect($this->controller->Link());
					return;
				}
			}
		}
		
		return parent::httpSubmission($request);
	}
	
	public function handleField($request) {
		echo $request->param('FieldName');
		//TODO: intercept/redirect
		return parent::handleField($request);
	}
	
}