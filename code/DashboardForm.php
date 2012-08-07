<?php
/**
 * The Form that is displayed in the Dashboard.
 * 
 * It takes care of forwarding submissions to the Panels' Controllers.
 * 
 * Currently, there is no special handling of sub fields. If this is required,
 *  it is necessary to override handleField().
 */
class DashboardForm extends Form {
	
	private $callbacks = array();
	
	public function __construct($controller, $name, $fields, $actions, $validator = null) {
		parent::__construct($controller, $name, $fields, $actions, $validator);
		$controllerClassname = get_class($controller);
		if(substr($this->FormAction(), 0, strlen($controllerClassname)) == $controllerClassname){
			$this->setFormAction(Director::absoluteURL($this->FormAction()));
		}
	}
	
	/**
	 * Add a panel to the Form. Each Panel will be diesplayed in a Tab.
	 * @param DashboardPanel $panel
	 */
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
	
	/**
	 * Check if the Form contains the FormField $fieldName in the Tab $panelName.
	 * 
	 * Used for testing.
	 * @param string $panelName
	 * @param string $fieldName
	 * @return boolean
	 */
	public function findField($panelName, $fieldName) {
		return $this->fields->fieldByName("Root.$panelName.$fieldName");
	}
	
	/**
	 * Check if we have a callback for the action in this request and call that.
	 * Otherwise we call the parent function.
	 * @see DashboardPanel->addFormField
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
					$return = call_user_func($callback, $funcName, $request, $this);
				} else {
					throw new BadFunctionCallException(
						'Cannot call the callback ' .print_r($callback,1));
				}
				
				//Depending on the return value, we either 
				// display the result of the function call,
				// redirect to a different location
				// or redirect back to the current location.
				//If this logic is changed, the documentation in 
				// DashboardPanel::addFormField needs to be updated!
				if($return === null){
					$this->controller->redirect($this->controller->Link());
				} elseif(is_array($return) && isset($return['redirect'])){
					$this->controller->redirect($return['redirect']);
				} elseif($return === true || $return !== ''){
					return $return;
				} else {
					$this->controller->redirect($this->controller->Link());
				}
				return;
			}
		}
		
		return parent::httpSubmission($request);
	}
}