<?php
class DashboardLogWriter {
	private $streamID;
	private $logWriter;
	/* Number of requests that are stored */
	public static $REQUESTS = 10;
	public static $LOG_FILE_PATH = '';
	private static $DEFAULT_LOG_FILE_PATH = '/../../debug.log';
	private static $requestNumber = -1;
	private static $messages = array();
	private static $logWriters = array();
	private static $SESSION_DATA_KEY = 'DEVELOPER_DASHBOARD_LOG_MESSAGES';
	private static $startedSession = false;
	
	public function __construct($streamID, $copyToFile=true){
		$this->streamID = $streamID;
		$this->logWriter = new Zend_Log_Writer_Stream(self::getLogFilepath());
		self::$logWriters[] = $this;
	}

	public function write($event){
		$this->logWriter->write($event);
		self::log(print_r($event,1), $this->streamID);
	}

	/**
	 * Get the messages that have been logged during the current request.
	 * 
	 * This should be called from the global error handler, 
	 *  to ensure that messages are output even if an error occured.
	 * @return array the logged messages, one message per entry. 
	 */
	public static function getLogFileMessages(){
		self::loadSavedMessagesFromSession();
		return self::$messages[self::$requestNumber];
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
		$ret = array();
		self::loadSavedMessagesFromSession();
		foreach(self::$messages as $key=>$messages){
			if($key < $newerThan) continue;
			$ret[$key] = $messages;
		}
		return $ret;
	}
	
	/**
	 * Read the log file from disk, starting at $offset.
	 * 
	 * @param int $offset
	 * @return array with two keys:
	 *  'last' is the offset of the last line
	 *  'text' is an array of lines.
	 */
	public static function readLogFile($offset=-1){
		$lines = array();
		if($offset < 0) $offset = 0;
		$file = fopen(self::getLogFilePath());
		fseek($file, 0, SEEK_END);
		$posEOF = ftell($file);
		fseek($file, $offset);
		while($line = fgets($file) !== false){
			$lines[] = $line;
		}
		fclose($file);
		return array('last' => $posEOF, 'text' => $lines);	
	}
	
	/**
	 * Add $message to the log.
	 * 
	 * @param string $message 
	 * @param string $streamID
	 * @param int $timestamp
	 */
	public static function log($message, $streamID = 'DEFAULT',
			$timestamp = null){
		if($timestamp == null){
			$timestamp = time();
		}
		self::loadSavedMessagesFromSession();
		self::$messages[self::$requestNumber][] = 
			"[$streamID] $timestamp $message";
		Session::set(self::$SESSION_DATA_KEY, self::$messages);
		Session::save();
	}

	/**
	 * The first time this function is called, it loads messages from past 
	 *  requests that have been saved to the session into $messages 
	 *  and sets $requestNumber.
	 * Repeated calls to this function will not reset $requestNumber.
	 * It will only display messages from requests in the current session.
	 */
	private static function loadSavedMessagesFromSession(){
		if(self::$requestNumber >= 0){ //function was already called.
			return;
		}
		Session::start();
		self::$messages = Session::get(self::$SESSION_DATA_KEY);
		if(self::$messages == null){
			self::$messages = array();
			self::$requestNumber = 0;
		} else {
			self::$requestNumber = count(array_keys(self::$messages));
		}
		self::$messages[self::$requestNumber] = array(); 
	}
    
	/**
	 * Figure out where our log files are stored.
	 * 
	 * @return string the log file path.
	 */
	private static function getLogFilePath(){
		if(self::$LOG_FILE_PATH == ''){
			 return dirname(__FILE__).self::$DEFAULT_LOG_FILE;
		} elseif (substr(self::$LOG_FILE_PATH, 0, 2) == '..'){
			 return dirname(__FILE__).'/'.self::$LOG_FILE_PATH;
		} else { //assume absolute path
			 return self::$LOG_FILE_PATH;
		}
	}
}