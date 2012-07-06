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
	private static $log_writers = array();
	
	/** @var string the stream id of this log writer*/
	private $streamID;
	/** @var DashboardLogSessionStorage The object that stores our messages. */
	private $storage;
	
	/**
	 * It is required to implement this method.
	 *  
	 * @param array $config
	 * @return DashboardLogWriter
	 */
	public static function factory($config) {
		$streamID = isset($config['streamID']) ? $config['streamID'] : 'DEFAULT';
		return self::get_log_writer($streamID);
	}
	
	/**
	 * A method to get an instance for a given streamID, similar to a factory.
	 * @param string $streamID
	 * @return DashboardLogWriter the writer.
	 */
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
	
	private function __construct($streamID) {
		$this->streamID = $streamID;
		$this->storage = DashboardSessionStorage::inst();
	}

	public function _write($event) {
		$this->storage->storeMessageObject(
			new DashboardLogMessage($event, $this->streamID)
		);
	}
}