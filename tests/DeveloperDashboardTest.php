<?php
class DeveloperDashboardTest extends SapphireTest {
	
	/** @var DeveloperDashboard */
	private $dashboard;
	/** @var DashboardForm the form that is associated with the dashboard. */
	private $form;
	/** @var DashboardSessionStorage */
	private $storage;
	/** @var DashboardLogWrapper */
	private $logWrapper;
	/** @var array of string */
	private $messages = array();
	
	/** @var string the path to the test logfile. Deleted after every run. */
	private $testFilePath = '../logfile_test.txt';
	/** @var resource the handle to the test logfile.*/
	private $testFile = null;

    public function setUp() {
    	parent::setUp();
    	$this->dashboard = DeveloperDashboard::inst();
    	$this->form = $this->dashboard->DashboardForm();
    	$this->storage = DashboardSessionStorage::inst();
    	$this->logWrapper = DashboardLog::get_log_wrapper('TestStream');
    	for($i=0; $i<10; $i++){
    		$this->messages[] = "Message $i";
    	}
    }
	
	public function tearDown(){
		if($this->testFile != null) fclose($this->testFile);
		if(is_file($this->testFilePath)) unlink($this->testFilePath);
		parent::tearDown();
	}
	
	public function testAddPanel() {
		$panel = new DashboardPanel('TestPanel');
		$field = new TextField('TestTextField');
		$panel->addFormField($field, null);
		$this->dashboard->addPanel($panel);
		
		$foundField = null;
		if($this->form != null){
			$foundField = $this->form->findField($panel->getName(), $field->getName());
			$this->assertEquals($field->getName(), $foundField->getName());
		} else {
			//Form not initialized
			//TODO: test if panel has been added to the preliminary list.
			echo "Warning: Form not initialized. Could not test addPanel.<br />";
		}
	}
	
	public function testAddAction(){
		//$this->fail("Not implemented");
	}
	
	public function testGetLogWrapper(){
		$wrapperDefault = DashboardLog::get_log_wrapper(); 
		$this->assertNotEquals($wrapperDefault, $this->logWrapper);
	}
	
	public function testLog(){
		//add log messages
		$this->logWrapper->log($this->messages[0]);
		$this->logWrapper->log($this->messages[1]);
		
		//retrieve & check
		$this->assertMessageListContains(
			array($this->messages[0], $this->messages[1]), 
			$this->storage->getMessagesFromSession());
	}
	
	public function testGetLog(){
		//find current requestID
		$res = $this->storage->getMessagesFromSession()->toArray();
		$lastRequestID = 0;
		$elements = 0;
		foreach($res as $entry){
			if($entry->RequestID > $lastRequestID){
				$lastRequestID = $entry->RequestID;
				$elements = count($entry->Children);
			}
		}
		//add log message
		$this->logWrapper->log($this->messages[0]);
		//check that it has been stored
		$changed = $this->storage->getMessagesFromSession()->items;
		$lastOne = array_pop($changed);
		$this->assertEquals($elements + 1, count($lastOne->Children->items));
		
		//$this->storage->getMessagesFromSession($lastRequestID - 1);
		$next = $this->storage->getMessagesFromSession($lastRequestID);
		$this->assertEquals(0, count($next->items));
		$current = $this->storage->getMessagesFromSession($lastRequestID - 1);
		$this->assertEquals(1, count($current->items));
		$this->assertEquals($elements + 1, count($current->items[0]->Children->items));
		$secondToLast = $this->storage->getMessagesFromSession($lastRequestID - 2);
		$this->assertEquals(2, count($secondToLast->items));
	}
	
	public function testReadLogFile(){
		//Write
		$this->writeToTestFile($this->messages[0]."\n");
		DashboardLog::register_log_file('TEST', $this->testFilePath);
		//read
		$res = DashboardLog::read_log_file(-1, 'TEST');
		$offset = $res['last'];
		$this->assertEquals($this->messages[0]."\n", $res['text'][0]);
		
		
		//write
		$this->writeToTestFile($this->messages[1]."\n");
		//read all
		$res = DashboardLog::read_log_file(-1, 'TEST');
		$this->assertEquals($this->messages[0]."\n", $res['text'][0]);
		$this->assertEquals($this->messages[1]."\n", $res['text'][1]);
		//read only new
		$res = DashboardLog::read_log_file($offset, 'TEST');
		$this->assertEquals($this->messages[1]."\n", $res['text'][0]);
	}
	
	/*
	 * Check if a Message list contains the messages in $expected.
	 * 
	 * This function internally calls assertDOSContains. This is possible
	 *  because DashboardLogMessage pretends to be a DataObject
	 *  by implementing toMap().
	 *  
	 * @param mixed A single message string or an array of message strings
	 * @param messageList the list of requests that each contain a list of messages.
	 * @return void. Will fail the test if the message is not found.  
	 */
	private function assertMessageListContains($expected, $messageList){
		$matches = array();
		
		if(is_array($expected)){
			foreach($expected as $entry){
				$matches[] = array('Message' => $entry);
			}
		} else {
			$matches[] = array('Message' => $expected);
		}
		$val = $messageList->items;
		$this->assertTrue(count($val) >= 1);
		$this->assertDOSContains($matches, array_pop($val)->Children);
	}
	
	private function writeToTestFile($message){
		file_put_contents($this->testFilePath, $message, FILE_APPEND);
	}
	
}