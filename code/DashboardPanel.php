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
	/** @var DashboardPanelContentProvider an optional content provider that 
	 * will be called to update the form fields before the form is displayed. */
	private $formContentProvider = null;
	/** @var array mixed entries. The callbacks. */
	private $callbacks;
	/** @var array mixed. The actions that should be forwarded. */
	private $actions;
	
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
		$this->actions = array();
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
	 * Get the callbacks
	 * @return array an associative array fieldName => callback.
	 */
	public function Actions(){
		return $this->actions;
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
	 * The method getPanelContent is called on $provider before the form is displayed.
	 * This can be used to add content that was not available when this panel 
	 *  was constructed, such as data from the session or the database.
	 * @param DashboardPanelContentProvider $provider
	 */
	public function setContentProvider(DashboardPanelContentProvider $provider) {
		$this->formContentProvider = $provider;
	}
	
	/**
	 * Updates the panel's content if setContentProvider was called.
	 */
	public function updateContent(){
		if($this->formContentProvider == null){
			return;
		}
		$this->formContentProvider->getPanelContent($this);
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
	 * The callback function will receive three parameters:
	 *   public function callback($actionName, $request, $form);
	 *   The first parameter is the name of the action that was triggered.
	 *   The second paramter is the current request.
	 *   The third paramter is the current DashboardForm instance. 
	 * 
	 * Depending on the return value of the callback, a redirect will happen:
	 *   * If nothing or null is returned, the form will be shown again.
	 *   * If an array with (redirect => URL) is returned, URL will be shown.
	 *   * If true is returned, no redirect will happen
	 *   * If a string is returned, the string will be displayed without redirect 
	 *   * In any other case, the form will be shown again.
	 * 
	 * Usage:
	 * From a Controller:
	 *   $panel->addFormField(new TextareaField(...));
	 *   $panel->addFormField(new FormAction(...), $this);
	 *   $panel->addFormField(new FormAction(...), $this, 'someMethod');
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
	
	/**
	 * Register an action on the DeveloperDashboard.
	 * 
	 * This allows to forward the action $action from the DeveloperDashboard to
	 *  a different controller.
	 * 
	 * @param string $action the name of the action
	 * @param Controller $controller
	 * @param string $method optional, can be omitted if same as $action.
	 * @throws InvalidArgumentException if $methodName is not a valid method on
	 *   the $controller.
	 */
	public function forwardAction($action, Controller $controller, $method=''){
		//@TODO: change this method to allow anonymous callback functions.
		if($method == ''){
			$method = $action;
		}
		if(!$controller->hasMethod($method)){
			throw new InvalidArgumentException(
					"Controller does not have a method called $method");
		}
		$this->actions[$action] = array($controller, $method);
	}
}