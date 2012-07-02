<?php
require_once 'Zend/Log.php';
/**
 * This class wraps Zend_Log. If you just want to log something call log().
 * For more advanced tasks (e.g. adding filters), you can directly access
 *  the underlying Zend_Log instance via $logger;
 */
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
	
	public function is_enabled(){
		return $this->filter->is_enabled();
	}
	
	public function set_enabled($enabled){
		$this->filter->set_enabled($enabled);
	}
	
	public function log($message, $priority = Zend_Log::INFO, $extras=null){
		$this->logger->log($message, $priority, $extras);
	}
	
}