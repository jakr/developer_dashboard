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
		if($this->form == null){
			$foundField = $this->form->findField($panel->getName(), $field->getName());
		} else {
			
		}
		$this->assertNotNull($foundField);
		$this->assertEquals($field->getName(), $foundField->getName());
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
	
	private function assertMessageListContains($expected, $messageList){
		$found = array();
		$notfound = array();
		$failure = false;
		
		if(is_array($expected)){
			foreach($expected as $entry){
				$found[$entry] = false;
			}
		} else {
			$found[$expected] = false;
		}
		
		foreach($messageList->toArray() as $messageListEntry){
			foreach($messageListEntry->Children as $child){
				if(isset($found[$child->Message])){
					$found[$child->Message] = true;
				}
			}
		}
		
		foreach($found as $key=>$entry){
			if($entry === false){
				$failure .= $key.', ';
			}
		}
		
		if($failure){
			$this->fail('Message list did not contain expected messages '.substr($failure, 0, -2));
		}
	}
	
}