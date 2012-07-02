<?php
/*
 * TODO this class contains a mix of two different concerns
 *  and a large number of static methods. Refactor needed.
 * The first concern is the management of a single stream, which is done 
 *  by the instances of this class. The second one is storing and loading data
 *  from and to the session for all streams. This is done by the static methods. 
 */
require_once 'Zend/Log.php';
require_once 'Zend/Log/Writer/Abstract.php';
class DashboardLogWriter extends Zend_Log_Writer_Abstract {
	/** @var int Number of requests that are stored in the session */
	public static $requests_to_keep = 10;
	private static $request_number = -1;
	private static $messages = array();
	private static $log_writers = array();
	private static $session_data_key = 'DEVELOPER_DASHBOARD_LOG_MESSAGES';
	
	/** @var string the stream id of this log writer*/
	private $streamID;
	
	/**
	 * It is required to implement this method.
	 *  
	 * @param array $config
	 */
	public static function factory($config) {
		$streamID = isset($config['streamID']) ? $config['streamID'] : 'DEFAULT';
		return self::get_log_writer($streamID);
	}

	/**
	 * Get the messages that have been logged during the current request.
	 * 
	 * This should be called from the global error handler, 
	 *  to ensure that messages are output even if an error occured.
	 * @return array the logged messages as strings, one message per entry. 
	 */
	public static function get_log_file_messages() {
		$ret = array();
		foreach(self::$messages[self::$request_number] as $message){
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
	public static function get_messages_from_session($newerThan = 0) {
		$requests = new ArrayList();
		foreach(self::$messages as $key=>$messages){
			//skip every request older than $newerThan
			if($newerThan >= $key) continue;
			$requests->push(new ArrayData(array(
				'Children' => new ArrayList($messages),
				'RequestID' => $key
			)));
		}
		return $requests;
	}
	
	public static function get_log_writer($streamID) {
		if(!isset(self::$log_writers[$streamID])){
			self::$log_writers[$streamID] = new DashboardLogWriter($streamID);
		}
		return self::$log_writers[$streamID];
	}
	
	/**
	 * Get the available stream ids.
	 * @return ArrayList each ArrayData item has an attribute StreamID.
	 */
	public static function get_stream_ids() {
		$streamIds = new ArrayList();
		foreach(array_keys(self::$log_writers) as $streamId){
			$streamIds->push(new ArrayData(array('StreamID' => $streamId)));
		}
		return $streamIds;
	}

	/**
	 * The first time this function is called, it loads messages from past 
	 *  requests that have been saved to the session into $messages 
	 *  and sets $requestNumber.
	 * Repeated calls to this function will not reset $requestNumber.
	 * It will only display messages from requests in the current session.
	 * The function was previously called loadSavedMessagesFromSession.
	 */
	public static function init() {
		if(self::$request_number >= 0){ //function was already called.
			return;
		}
		Session::start();
		self::$messages = Session::get(self::$session_data_key);
		if(self::$messages == null){
			self::$messages = array();
			self::$request_number = 0;
		} else {
			$keys = array_keys(self::$messages);
			self::$request_number = end($keys) + 1;
			//remove old requests.
			$elements = count($keys) + 1;
			if($elements + 1 > self::$requests_to_keep){
				for($i=0; $i<$elements-self::$requests_to_keep; $i++){
					unset(self::$messages[$keys[$i]]);
				}
			}
		}
		self::$messages[self::$request_number] = array(); 
	}
	
	private static function store_message_in_session($messageObj) {
		self::$messages[self::$request_number][] = $messageObj;
		Session::set(self::$session_data_key, self::$messages);
		Session::save();
	}
	
	private function __construct($streamID) {
		$this->streamID = $streamID;
	}

	public function _write($event) {
		self::store_message_in_session(
			new DashboardLogMessage($event, $this->streamID)
		);
	}
}
//Make sure our class is initialized.
DashboardLogWriter::init();