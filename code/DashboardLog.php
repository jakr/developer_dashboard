<?php
/**
 * This is the factory class for log wrappers.
 * 
 * The recommended way of logging is:
 *   1. Get a logWrapper by calling Log::get_log_wrapper.
 *     We suggest using __class__ as the streamID:
 *     $log_wrapper = Log::get_log_wrapper(__class__);
 *   2. Check if the stream is enabled:
 *     if($log_wrapper->is_enabled()) ...
 *   3. Log your message
 *	   $log_wrapper->log('Hello World');
 * 
 * If you are in a hurry, you can instead just call DashboardLog::log.
 */
require_once 'Zend/Log/Writer/Stream.php';
class DashboardLog {
	/** 
	 * @var string Override the default log file path with this path.
	 * If the path starts with .. it is assumed to be relative to this
	 *  file's location.
	 */
	public static $log_file_path = '../../assets/debug.log';
	
	/** @var boolean If true the log messages are also written to a file. */
	public static $copy_to_file = false;
	
	/** @var array the DashboardLogWrappers that were created. */
	private static $logWrappers = array();
	
	/** @var Zend_Log_Writer_Stream The writer used to write to the log file. */
	private static $log_file_writer = null;
	
	/** @var array the log files that can be viewed. */
	private static $available_log_files = array();
	
	
	/**
	 * Get an instance of the log wrapper for the given streamID.
	 * 
	 * @param string $streamID
	 * @return DashboardLogWrapper
	 */
	public static function get_log_wrapper($streamID = 'DEFAULT') {
		$streamID = str_replace(' ', '-', $streamID); //remove spaces.
		if(!isset(self::$logWrappers[$streamID])) {
			
			if(self::$log_file_writer == null) {
				self::$log_file_writer = new Zend_Log_Writer_Stream(
					self::get_log_file_path()
				);
			}
			$logWrap = new DashboardLogWrapper();
			$writer = DashboardLogWriter::get_log_writer($streamID);
			$logWrap->logger->addWriter($writer);
			if(self::$log_file_writer != null && self::$copy_to_file){
				$logWrap->logger->addWriter(self::$log_file_writer);
			}
			self::$logWrappers[$streamID] = $logWrap;
		}
		return self::$logWrappers[$streamID];
	}
	
	/**
	 * Get an instance of the log wrapper for the given streamID.
	 * 
	 * @param string $streamID
	 * @return DashboardLogWrapper
	 */
	public static function inst($streamID = 'DEFAULT') {
		return self::get_log_wrapper($streamID);
	}
	
	/**
	 * Add $message to the log.
	 * 
	 * @param string $message 
	 * @param string $streamID
	 * @param int $priority
	 */
	public static function log($message, $streamID = 'DEFAULT',
			$priority = Zend_Log::INFO) {
		$wrapper = self::get_log_wrapper($streamID);
		$wrapper->log($message, $priority);
	}
	
	/**
	 * Register the logfile at $path under as $name. It can then be read
	 *  by calling read_log_file(-1, $name).
	 * 
	 * The log file already has to exist at $path, when this function is called!
	 * 
	 * @param string $name
	 * @param string $path
	 * @throws InvalidArgumentException if there is no file at $path
	 */
	public static function register_log_file($name, $path){
		if(!is_file($path)){
			throw new InvalidArgumentException("Invalid path: $path");
		}
		self::$available_log_files[$name] = $path;
	}
    
	/**
	 * Figure out where our log files are stored.
	 * 
	 * @return string the log file path.
	 */
	private static function get_log_file_path() {
		if (substr(self::$log_file_path, 0, 2) == '..'){
			 return dirname(__FILE__).'/'.self::$log_file_path;
		} else { //assume absolute path
			 return self::$log_file_path;
		}
	}
	
	/**
	 * Read the log file from disk, starting at $offset.
	 * 
	 * @param int $offset
	 * @return array with two keys:
	 *  'last' is the offset of the last line
	 *  'text' is an array of lines.
	 */
	public static function read_log_file($offset=-1, $logFileName = 'DEFAULT') {
		if(!isset(self::$available_log_files[$logFileName])){
			if($logFileName == 'DEFAULT'){
				self::$available_log_files[$logFileName] = self::get_log_file_path();
			} else {
				return 'Unknown log file';
			}
		}
		
		$path = self::$available_log_files[$logFileName];
		$lines = array();
		if($offset < 0) $offset = 0;
		$file = fopen($path, 'r');
		fseek($file, 0, SEEK_END);
		$posEOF = ftell($file);
		fseek($file, $offset);
		while($line = fgets($file)){
			$lines[] = $line;
		}
		fclose($file);
		return array('last' => $posEOF, 'text' => $lines);	
	}
}