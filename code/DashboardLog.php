<?php
class DashboardLog {
	private static $loggers = array();
	private static $logFileWriter = null;
	private static $DEFAULT_LOG_FILE_PATH = '/../../debug.log';
	public static $LOG_FILE_PATH = null;
	
	public static function init(){
		if(self::$logFileWriter == null){
			return;
		}
		self::$logFileWriter = new Zend_Log_Writer_Stream(
			self::getLogFilePath()); 
	}
	
	public static function getLogger($streamID){
		if(!isset(self::$loggers[$streamID])){
			$log = new Zend_Log();
			$writer = DashboardLogWriter::getLogWriter($streamID);
			$log->addWriter($writer);
			print_r(self::$logFileWriter);
			//$log->addWriter(self::$logFileWriter);
			self::$loggers[$streamID] = $log;
		}
		return self::$loggers[$streamID];
	}
	
	/**
	 * Add $message to the log.
	 * 
	 * @param string $message 
	 * @param string $streamID
	 * @param int $timestamp
	 */
	public static function log($message, $streamID = 'DEFAULT',
		$priority = Zend_Log::INFO){
		$logger = self::getLogger($streamID);
		$logger->log($message, $priority);
	}
    
	/**
	 * Figure out where our log files are stored.
	 * 
	 * @return string the log file path.
	 */
	private static function getLogFilePath(){
		if(self::$LOG_FILE_PATH == null){
			 return dirname(__FILE__).self::$DEFAULT_LOG_FILE;
		} elseif (substr(self::$LOG_FILE_PATH, 0, 2) == '..'){
			 return dirname(__FILE__).'/'.self::$LOG_FILE_PATH;
		} else { //assume absolute path
			 return self::$LOG_FILE_PATH;
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
	
}
DashboardLogWriter::init();