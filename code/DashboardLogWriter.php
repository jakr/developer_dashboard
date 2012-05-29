<?php
class DashboardLogWriter {
	public static $DEFAULT_LOG_FILE = '';
	/* Number of requests that are stored */
	public static $REQUESTS = 10;
	private $streamID;
	private $logWriter;
	private static $sessionKey = -1;
	private static $messages = array();
	private static $logWriters = array();
	private static $SESSION_DATA_KEY = 'DEVELOPER_DASHBOARD_LOG_MESSAGES';
	private static $startedSession = false;
	
	public function __construct($streamID, $copyToFile=true){
		$this->streamID = $streamID;
		$logFilePath = self::getLogFilepath();
		$this->logWriter = new Zend_Log_Writer_Stream($logFilePath);
		self::$logWriters[] = $this;
	}

	public function write($event){
		$this->logWriter->write($event);
	}

	/**
	 * 
	 * Method 
	 * This should be called from the error handler, 
	 *  to ensure that messages are output even if an error occured.
	 */
	public static function getLogFileMessages($newerThan = 0){
		fopen(self::getLogFilePath()){
			
		}
	}

	public static function getSavedMessages($newerThan = 0){
		$ret = array();
		self::loadSavedMessagesFromSession();
		foreach(self::$messages as $key=>$messages){
			if($key < $newerThan) continue;
			$ret[] = $messages;
		}
		return $ret;
	}

	private static function loadSavedMessagesFromSession(){
		if(self::$sessionKey < 0){
			Session::start();
			self::$messages = Session::get(self::$SESSION_DATA_KEY);
			if(self::$messages == null){
				self::$messages = array();
				self::$sessionKey = 0;
			} else {
				self::$sessionKey = count(array_keys(self::$messages));
			}
			self::$messages[self::$sessionKey] = array();
		} 
	}
    
	private static function getLogFilePath(){
		$logFilePath = '';
		if(self::$DEFAULT_LOG_FILE == ''){
			 $logFilePath = dirname(__FILE__).'/../../debug.log';
		} elseif (substr(self::$DEFAULT_LOG_FILE, 0, 2) == '..'){
			 $logFilePath = dirname(__FILE__).'/'.self::$DEFAULT_LOG_FILE;
		} else { //assume absolute path
			 $logFilePath = self::$DEFAULT_LOG_FILE;
		}
		return $logFilePath;
	}

	public static function log($string, $streamID = 'DEFAULT', $timestamp = null){
		if($timestamp == null){
			$timestamp = time();
		}
		self::loadSavedMessagesFromSession();
		self::$messages[self::$sessionKey][] = "[$streamID] $timestamp $string";
		Session::set(self::$SESSION_DATA_KEY, self::$messages);
		Session::save();
	}
}