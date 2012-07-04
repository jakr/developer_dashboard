<?php
/**
 * This object is similar to a FieldList. It contains 
 *  FormField and FormAction elements.
 * An action handler can be assigned to each FormField and FormAction element. 
 */
class DashboardPanel {
	/** @var string the panel's name. */
	private $panelName;
	/** @var FieldList the fields of this panel */
	private $fields;
	/** @var Controller an optional controller instance that 
	 * will be called to update the form fields before the form is displayed. */
	private $formContentCallbackController = null;
	/** @var array mixed entries. The callbacks. */
	private $callbacks;
	
	public function __construct($panelName, FieldList $fields = null,
		array $callbacks = null
	){
		if($fields == null) {
			$fields = new FieldList();
		}
		if($callbacks == null){
			$this->callbacks = array();
		} else {
			$this->callbacks = $callbacks;
		}
		//TODO: Check that number of actions matches number of fields. 
		$this->panelName = $panelName;
		$this->fields = $fields;
	}
	
	/**
	 * Get the panel's FieldList
	 * @return FieldList
	 */
	public function Fields(){
		return $this->fields;
	}
	
	/**
	 * Get the callbacks
	 * @return array an associative array fieldName => callback.
	 */
	public function Callbacks(){
		return $this->callbacks;
	}
	
	/**
	 * Get the panel's name.
	 * @return string
	 */
	public function getName(){
		return $this->panelName;
	}
	
	/**
	 * Allows to update the panel's content before it is displayed.
	 * 
	 * The method getPanelContent is called on $controller before the form is displayed.
	 * This can be used to add content that was not available when this panel 
	 *  was constructed, such as data from the session or the database.
	 * @param Controller $controller
	 */
	public function setFormContentCallback(Controller $controller){
		$this->formContentCallbackController = $controller;
	}
	
	/**
	 * Updates the panel's content if setFormContentCallback was called.
	 */
	public function updateContent(){
		if($this->formContentCallbackController == null){
			return;
		}
		$this->formContentCallbackController->getPanelContent($this);
	}
	
	/**
	 * Add a FormField to this panel.
	 * 
	 * If $controller is set, it either has to contain a method with the
	 *  same name as the FormField or methodName has to be set to a valid name.
	 * This callback function is then called when the action is triggered 
	 *  on the form. If the callback function returns content, 
	 *  that content is displayed. Otherwise, the user is redirected back 
	 *  to the original form.
	 * 
	 * Usage:
	 * From a Controller:
	 *   $panel->addFormField(new TextareaField(...));
	 *   $panel->addFormField(new FormAction(...), $this);
	 * 
	 * @param FormField $formField
	 * @param Controller $controller
	 * @param string $methodName
	 * @throws InvalidArgumentException if a controller is set, but no callback method can be found.
	 */
	public function addFormField(FormField $formField,
		Controller $controller = null, $methodName = null
	){
		$this->fields->add($formField);
		
		if($controller != null){
			$action = ($formField instanceof FormAction) 
				? $formField->actionName() 
				: $formField->getName();
			if($methodName == null || $methodName == '') {
				if(is_callable(array($controller, $action))){
					$this->callbacks[$action] = array($controller, $action);
				} else {
					throw new InvalidArgumentException("If a controller is set,"
						. " it either has to contain a method with the same "
						. "name as the form field or a valid methodName "
						."has to be set."
					);
				}
			} else { 
				if(is_callable(array($controller, $methodName))){
					$this->callbacks[$action] = array($controller, $methodName);
				} else {
					throw new InvalidArgumentException("Invalid methodName. "
						." The method '$methodName' is not a callable method.");
				}
			}
		}
	}
}