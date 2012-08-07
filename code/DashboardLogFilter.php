<?php
/**
 * A filter used to enable and disable log streams.
 */
require_once 'Zend/Log/Filter/Interface.php';
class DashboardLogFilter implements Zend_Log_Filter_Interface {
	private $enabled = true;
	
    /**
     * Accepts the message if this stream is enabled.
     *
     * @param  array    $event    event data
     * @return boolean            accepted?
     */
    public function accept($event){
    	return $this->enabled;
    }
	
    /**
     * Check if the stream that this instance filters is enabled.
     */
	public function is_enabled(){
		return $this->enabled;
	}
	
	/**
	 * Enable or disable the stream that this instance filters.
	 * @param boolean $enabled
	 */
	public function set_enabled($enabled){
		if($enabled){ //Make sure only booleans are stored.
			$this->enabled = true;
		} else {
			$this->enabled = false;
		}
	}

}