<?php
/**
 * Singleton class.
 */
class DashboardSessionStorage {
	private static $instance = null;
	/** @var int Number of requests that are stored in the session */
	public static $requests_to_keep = 2;
	/** @var string the key under which the values are stored in the session.*/
	public static $session_key = 'DEVELOPER_DASHBOARD';
	/** 
	 * @var string the key under which the log messages are stored in the session
	 * This is an entry in the array $_SESSION[$session_key].
	 */
	private static $log_message_key = 'LOG_MESSAGES';
	
	private $sessionData = array();
	private $requestNumber = -1;
	
	
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
		$this->sessionData = Session::get(self::$session_key);
		if($this->sessionData == null){
			$this->sessionData = array();
			$this->sessionData[self::$log_message_key] = array();
			$this->requestNumber = 0;
		} else {
			$keys = array_keys($this->sessionData[self::$log_message_key]);
			$this->requestNumber = end($keys) + 1;
			//remove old requests.
			$elements = count($keys) + 1;
			if($elements + 1 > self::$requests_to_keep){
				for($i=0; $i<$elements-self::$requests_to_keep; $i++){
					unset($this->sessionData[self::$log_message_key][$keys[$i]]);
				}
			}
		}
		//Append new array for messages written by this request.
		$this->sessionData[self::$log_message_key][$this->requestNumber] = array(
			'messages' => array(),
			'method' => $_SERVER['REQUEST_METHOD'],
			'URI' => $_SERVER['REQUEST_URI'],
			'userID' => Member::currentUserID() ? Member::currentUserID() : 'none'
		); 
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
		$this->sessionData[$name] = $value;
		$this->updateSession();
	}
	
	public function loadSetting($name){
		if(!isset($this->sessionData[$name])){
			return false;
		} else {
			return $this->sessionData[$name];
		}		
	}
	
	public function storeMessageObject($messageObj) {
		$this->sessionData[self::$log_message_key][$this->requestNumber]
				['messages'][] = $messageObj;
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
		$data = $this->sessionData[self::$log_message_key][$this->requestNumber];
		foreach($data['messages'] as $message){
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
		foreach($this->sessionData[self::$log_message_key] as $key=>$data){
			//skip every request older than $newerThan and those without messages.
			if($newerThan >= $key || count($data['messages']) == 0) continue;
			$requests->push(new ArrayData(array(
				'Children' => new ArrayList($data['messages']),
				'RequestID' => $key,
				'RequestMethod' => $data['method'],
				'RequestURI' => $data['URI'],
				'UserID' => $data['userID']
			)));
		}
		return $requests;
	}
	
	private function updateSession(){
		Session::set(self::$session_key, $this->sessionData);
		Session::save();
	}
}