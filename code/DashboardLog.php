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


	/**
	 * Get an instance of the log wrapper for the given streamID.
	 * 
	 * @param string $streamID
	 * @return DashboardLogWrapper
	 */
	public static function get_log_wrapper($streamID = 'DEFAULT') {
		$streamID = str_replace(' ', '-', $streamID); //remove spaces.
		if(!isset(self::$logWrappers[$streamID])) {
			$logWrap = new DashboardLogWrapper();
			$writer = DashboardLogWriter::get_log_writer($streamID);
			$logWrap->logger->addWriter($writer);
			if(self::$copy_to_file){
				DashboardLogFile::register_log_file(__class__, self::$log_file_path);
				$logWrap->logger->addWriter(DashboardLogFile::get_writer(__class__));
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
	 * @param int $priority
	 */
	public static function log($message, $streamID = 'DEFAULT',
			$priority = Zend_Log::INFO) {
		$wrapper = self::get_log_wrapper($streamID);
		$wrapper->log($message, $priority);
	}
}