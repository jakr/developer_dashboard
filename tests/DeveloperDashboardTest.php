<?php 
class DeveloperDashboardTest extends SapphireTest {
	
	private $dashboard;
	private $form;
	private $storage;
	private $logWrapper;
	private $messages = array();

    public function setUp() {
    	parent::setUp();
    	$this->dashboard = DeveloperDashboard::inst();
    	$this->form = $this->dashboard->DashboardForm();
    	$this->storage = DashboardLogSessionStorage::inst();
    	$this->logWrapper = DashboardLog::get_log_wrapper('TestStream');
    	for($i=0; $i<10; $i++){
    		$this->messages[] = "Message $i";
    	}
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
			echo "Warning: Form not initialized.";
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
	
}