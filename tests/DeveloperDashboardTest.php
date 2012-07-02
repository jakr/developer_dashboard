<?php 
class DeveloperDashboardTest extends SapphireTest {
	
	private $dashboard;
	private $form;
	private $messages = array();

    public function setUp() {
    	parent::setUp();
    	$this->dashboard = DeveloperDashboard::inst();
    	$this->form = $this->dashboard->DashboardForm();
    	for($i=0; $i<10; $i++){
    		$messages[] = "Message $i";
    	}
    }
	
	public function testAddPanel() {
		$panel = new DashboardPanel('TestPanel');
		$field = new TextField('TestTextField');
		$panel->addFormField($field, null);
		$this->dashboard->add_panel($panel);
		
		$foundField = $this->form->findField($panel->getName(), $field->getName());
		
		$this->assertNotNull($foundField);
		$this->assertEquals($field->getName(), $foundField->getName());
	}
	
	public function testAddAction(){
		$this->fail("Not implemented");
	}
	
	public function testGetLogWrapper(){
		$wrapperDefault = DashboardLog::get_log_wrapper(); 
		$wrapperTest = DashboardLog::get_log_wrapper('TestStream');
		$this->assertNotEquals($wrapperDefault, $wrapperTest);
	}
	
	public function testLog(){
		$streamID = 'TestStream';
		$logger = DashboardLog::get_log_wrapper($streamID);
		 
		$logger->log($this->messages[0], $streamID);
		$logger->log($this->messages[1], $streamID);
		
		//add log messages
		//retrieve
	}
	
	public function testGetLog(){
		//add log messages
		//retrieve
		//add more
		//retrieve only new ones
		//retrieve all
	}
	
}