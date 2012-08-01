<?php
/**
 * Singleton class.
 */
class DashboardSessionStorage {
	private static $instance = null;
	/** @var int Number of requests that are stored in the session */
	public static $requests_to_keep = 10;
	public static $session_key = 'DEVELOPER_DASHBOARD';
	private static $log_message_key = 'LOG_MESSAGES';
	
	private $session_data = array();
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
		$this->session_data = Session::get(self::$session_key);
		if($this->session_data == null){
			$this->session_data = array();
			$this->session_data[self::$log_message_key] = array();
			$this->request_number = 0;
		} else {
			$keys = array_keys($this->session_data[self::$log_message_key]);
			$this->request_number = end($keys) + 1;
			//remove old requests.
			$elements = count($keys) + 1;
			if($elements + 1 > self::$requests_to_keep){
				for($i=0; $i<$elements-self::$requests_to_keep; $i++){
					unset($this->session_data[self::$log_message_key][$keys[$i]]);
				}
			}
		}
		//Append new array for messages written by this request.
		$this->session_data[self::$log_message_key][$this->request_number] = array(); 
	}
	
	/**
	 * Get an instance.
	 * @return DashboardSessionStorage
	 */
	public static function inst(){
		if(self::$instance == null){
			self::$instance = new DashboardSessionStorage();
		}
		return self::$instance;
	}
	
	public function storeSetting($name, $value){
		$this->session_data[$name] = $value;
		$this->updateSession();
	}
	
	public function loadSetting($name){
		if(!isset($this->session_data[$name])){
			return false;
		} else {
			return $this->session_data[$name];
		}		
	}
	
	public function storeMessageObject($messageObj) {
		$this->session_data[self::$log_message_key][$this->request_number][] = $messageObj;
		$this->updateSession();
	}

	/**
	 * Get the messages that have been logged during the current request.
	 * 
	 * This should be called from the global error handler, 
	 *  to ensure that messages are output even if an error occured.
	 * @return array the logged messages as strings, one message per entry. 
	 */
	public function getLogFileMessages() {
		$ret = array();
		foreach($this->session_data[self::$log_message_key][$this->request_number] as $message){
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
		foreach($this->session_data[self::$log_message_key] as $key=>$messages){
			//skip every request older than $newerThan
			if($newerThan >= $key) continue;
			$requests->push(new ArrayData(array(
				'Children' => new ArrayList($messages),
				'RequestID' => $key
			)));
		}
		return $requests;
	}
	
	private function updateSession(){
		Session::set(self::$session_key, $this->session_data);
		Session::save();
	}
}