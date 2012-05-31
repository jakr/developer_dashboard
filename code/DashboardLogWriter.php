<?php
require_once 'Zend/Log.php';
require_once 'Zend/Log/Writer/Abstract.php';
class DashboardLogWriter extends Zend_Log_Writer_Abstract {
	private $streamID;
	private static $copyToFile = false;
	/** Number of requests that are stored */
	public static $REQUESTS = 10;
	private static $requestNumber = -1;
	private static $messages = array();
	private static $logWriters = array();
	private static $SESSION_DATA_KEY = 'DEVELOPER_DASHBOARD_LOG_MESSAGES';
	
	private function __construct($streamID, $copyToFile=true){
		$this->streamID = $streamID;
	}
	
	/**
	 * It is required to implement this method.
	 *  
	 * @param array $config
	 */
	public static function factory($config){
		$streamID = isset($config['streamID']) ? $config['streamID'] : 'DEFAULT';
		return self::getLogWriter($streamID);
	}
	
	static function getLogWriter($streamID){
		if(!isset(self::$logWriters[$streamID])){
			self::$logWriters[$streamID] = 
				new DashboardLogWriter($streamID);
		}
		return self::$logWriters[$streamID];
	}

	function _write($event){
		self::storeMessageInSession(
			new DashboardLogMessage($event, $this->streamID));
	}

	/**
	 * Get the messages that have been logged during the current request.
	 * 
	 * This should be called from the global error handler, 
	 *  to ensure that messages are output even if an error occured.
	 * @return array the logged messages as strings, one message per entry. 
	 */
	public static function getLogFileMessages(){
		$ret = array();
		foreach(self::$messages[self::$requestNumber] as $message){
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
	 * @return array the messages as an array of 
	 *   requestNumber=>array('message1', 'message2',...). 
	 */
	public static function getMessagesFromSession($newerThan = 0){
		$requests = new ArrayList();
		foreach(self::$messages as $key=>$messages){
			//skip every request older than $newerThan
			if($newerThan > $key) continue;
			$requests->push(new ArrayData(array(
				'Children' => new ArrayList($messages),
				'RequestID' => $key
			)));
		}
		return $requests;
	}
	
	private static function storeMessageInSession($messageObj){
		self::$messages[self::$requestNumber][] = $messageObj;
		Session::set(self::$SESSION_DATA_KEY, self::$messages);
		Session::save();
	}
	
	public static function getStreamIDs(){
		return array_keys(self::$logWriters);
	}

	/**
	 * The first time this function is called, it loads messages from past 
	 *  requests that have been saved to the session into $messages 
	 *  and sets $requestNumber.
	 * Repeated calls to this function will not reset $requestNumber.
	 * It will only display messages from requests in the current session.
	 * The function was previously called loadSavedMessagesFromSession.
	 */
	public static function init(){//
		if(self::$requestNumber >= 0){ //function was already called.
			return;
		}
		Session::start();
		self::$messages = Session::get(self::$SESSION_DATA_KEY);
		if(self::$messages == null){
			self::$messages = array();
			self::$requestNumber = 0;
		} else {
			$keys = array_keys(self::$messages);
			self::$requestNumber = end($keys) + 1;
			//remove old requests.
			$elements = count($keys) + 1;
			if($elements + 1 > self::$REQUESTS){
				for($i=0; $i<$elements-self::$REQUESTS; $i++){
					unset(self::$messages[$keys[$i]]);
				}
			}
		}
		self::$messages[self::$requestNumber] = array(); 
	}
}
DashboardLogWriter::init();