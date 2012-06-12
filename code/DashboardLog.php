<?php
require_once 'Zend/Log/Writer/Stream.php';
class DashboardLog {
	/** 
	 * @var string Override the default log file path with this path.
	 * If the path starts with .. it is assumed to be relative to this
	 *  file's location.
	 */
	public static $log_file_path = '../../debug.log';
	
	/** @var boolean If true the log messages are also written to a file. */
	public static $copy_to_file = false;
	
	/** @var array the DashboardLogWrappers that were created. */
	private static $logWrappers = array();
	
	/** @var Zend_Log_Writer_Stream The writer used to write to the log file. */
	private static $log_file_writer = null;
	
	public static function init() {
		if(self::$log_file_writer != null) {
			return;
		}
		self::$log_file_writer = new Zend_Log_Writer_Stream(
			self::get_log_file_path()
		); 
	}
	
	public static function get_log_wrapper($streamID) {
		if(!isset(self::$logWrappers[$streamID])) {
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
	 * Add $message to the log.
	 * 
	 * @param string $message 
	 * @param string $streamID
	 * @param int $timestamp
	 */
	public static function log($message, $streamID = 'DEFAULT',
			$priority = Zend_Log::INFO) {
		$wrapper = self::get_log_wrapper($streamID);
		$wrapper->log($message, $priority);
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
	public static function read_log_file($offset=-1) {
		$lines = array();
		if($offset < 0) $offset = 0;
		$file = fopen(self::get_log_file_path());
		fseek($file, 0, SEEK_END);
		$posEOF = ftell($file);
		fseek($file, $offset);
		while($line = fgets($file) !== false){
			$lines[] = $line;
		}
		fclose($file);
		return array('last' => $posEOF, 'text' => $lines);	
	}
	
}
DashboardLog::init();