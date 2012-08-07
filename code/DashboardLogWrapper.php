<?php
/**
 * This class wraps Zend_Log. If you just want to log something call log().
 * For more advanced tasks (e.g. adding filters), you can directly access
 *  the underlying Zend_Log instance via $logger;
 * The purpose of this wrapper is to attach a filter to the logger that allows
 *   enabling or disabling the log stream. It also exposes the state of the
 *   stream (enabled or disabled) via the is_enabled method.
 */
require_once 'Zend/Log.php';
class DashboardLogWrapper {
	/** @var Zend_Log The instance that is wrapped. */
	public $logger;
	/** @var DashboardLogFilter The filter to enable or disable the logger. */
	private $filter;
	
	public function __construct(){
		$this->logger = new Zend_Log();
		$this->filter = new DashboardLogFilter();
		$this->logger->addFilter($this->filter);
	}
	
	/**
	 * Check if the logger is enabled.
	 * @return boolean
	 */
	public function is_enabled(){
		return $this->filter->is_enabled();
	}
	
	/**
	 * Enable or disable the logger.
	 * @param boolean $enabled
	 */
	public function set_enabled($enabled){
		$this->filter->set_enabled($enabled);
	}
	
	/**
	 * Log a message to the logger.
	 * @see Zend_Log
	 * @param string $message
	 * @param type $priority
	 * @param type $extras
	 */
	public function log($message, $priority = Zend_Log::INFO, $extras=null){
		if(is_object($message) || is_array($message)){
			$message = '<pre>'.print_r($message,1).'</pre>';
		}
		$this->logger->log($message, $priority, $extras);
	}
	
}