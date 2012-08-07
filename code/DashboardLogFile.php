<?php
/**
 * The main purpose of this class is to keep track of log files.
 * It is also a factory class for log file writers.
 * 
 * To get a writer for a log file, it is required to first register its file,
 *  by calling register_log_file. This is done to limit reading and writing
 *  to only those files that were specifically intended to be accessed.
 *  The next step then is to call get_log_file_writer.
 */
require_once 'Zend/Log/Writer/Stream.php';
class DashboardLogFile {
	/** @var array the log files that can be viewed. */
	private static $available_log_files = array();

	/** @var array of Zend_Log_Writer_Stream */
	private static $log_file_writers = array();

	/**
	 * Get the names under which log files have been registered.
	 * @return array the names
	 */
	public static function get_available_log_files(){
		return array_keys(self::$available_log_files);
	}

	/**
	 * Get a log writer that writes to the file registered as $registeredName.
	 * Make sure to call register_log_file first!
	 * 
	 * @param type $registeredName
	 * @return type
	 * @throws InvalidArgumentException
	 */
	public static function get_log_file_writer($registeredName){
		if(!isset(self::$available_log_files[$registeredName])){
			throw new InvalidArgumentException(
					"Name not registered: $registeredName."
					.' Call register_log_file first.');
		}
		if(!isset(self::$log_file_writers[$registeredName])){
			self::$log_file_writers[$registeredName] = new Zend_Log_Writer_Stream(
					self::$available_log_files[$registeredName]);
		}
		return self::$log_file_writers[$registeredName];
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
			return 'Unknown log file';
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

	/**
	 * Register the logfile at $path under $name. It can then be read
	 *  by calling read_log_file(-1, $name) and used by a log writer.
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
}

?>
