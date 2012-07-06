<?php
/**
 * Singleton class.
 */
class DashboardSessionStorage {
	private static $instance = null;
	/** @var int Number of requests that are stored in the session */
	public static $requests_to_keep = 10;
	private static $session_data_key = 'DEVELOPER_DASHBOARD_LOG_MESSAGES';
	
	private $messages = array();
	private $request_number = -1;
	
	
	/**
	 * The constructior loads messages from past 
	 *  requests that have been saved to the session into $messages 
	 *  and sets $requestNumber. It should only be called once.
	 * Repeated calls to this function will not reset $requestNumber.
	 * It will only display messages from requests in the current session.
	 * The function was previously called loadSavedMessagesFromSession.
	 */
	private function __construct(){ //singleton
		if(self::$instance != null){ //make sure it is only called once.
			return;
		}
		Session::start();
		$this->messages = Session::get(self::$session_data_key);
		if($this->messages == null){
			$this->messages = array();
			$this->request_number = 0;
		} else {
			$keys = array_keys($this->messages);
			$this->request_number = end($keys) + 1;
			//remove old requests.
			$elements = count($keys) + 1;
			if($elements + 1 > self::$requests_to_keep){
				for($i=0; $i<$elements-self::$requests_to_keep; $i++){
					unset($this->messages[$keys[$i]]);
				}
			}
		}
		//Append new array for messages written by this request.
		$this->messages[$this->request_number] = array(); 
	}
	
	public static function inst(){
		if(self::$instance == null){
			self::$instance = new DashboardSessionStorage();
		}
		return self::$instance;
	}
	
	public function storeMessageObject($messageObj) {
		$this->messages[$this->request_number][] = $messageObj;
		Session::set(self::$session_data_key, $this->messages);
		Session::save();
	}

	/**
	 * Get the messages that have been logged during the current request.
	 * 
	 * This should be called from the global error handler, 
	 *  to ensure that messages are output even if an error occured.
	 * @return array the logged messages as strings, one message per entry. 
	 */
	public function get_log_file_messages() {
		$ret = array();
		foreach($this->messages[$this->request_number] as $message){
			$ret[] = $message->__toString();
		}
		return $ret;
	}
	
	/**
	 * Get the messages that have been saved to the session.
	 * If $newerThan is set, only entries from requests newer 
	 *  than this request number are returned
	 * 
	 * @param int $newerThan 
	 * @return ArrayList the messages. Each entry is an ArraData item with
	 * 	two members: RequestID and Children. The Children are an ArrayList
	 *  that contains the messages as DashboardLogMessage objects. 
	 */
	public function getMessagesFromSession($newerThan = 0) {
		$requests = new ArrayList();
		foreach($this->messages as $key=>$messages){
			//skip every request older than $newerThan
			if($newerThan >= $key) continue;
			$requests->push(new ArrayData(array(
				'Children' => new ArrayList($messages),
				'RequestID' => $key
			)));
		}
		return $requests;
	}
}