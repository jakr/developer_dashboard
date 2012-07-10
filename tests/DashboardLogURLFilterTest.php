<?php
/**
 * This test class only tests that the rule matching is done in the right order
 *  and that the different functions return consistent results.
 * It does not test regular expressions, since DashboardLogURLFilter relies on
 *  the builtin preg_match functions.
 */
class DashboardLogURLFilterTest extends SapphireTest {
	/** @var DashboardLogURLFilter system under test*/
	var $urlFilter;
	var $somePattern = '#some/url#';
	var $someURL = '/just/some/url';
	var $someURLSubDir = '/just/some/url/with/subdir';
	var $otherURL = '/a/completely/different/url';
	
    public function setUp() {
    	parent::setUp();
    	$this->urlFilter = new DashboardLogURLFilter();
    }
    
	public function testDefaultAccept() {
		$this->assertTrue($this->plainFindMatch());
	}
	
	
	public function testAddAcceptURL(){
		//to test accepting we need to set up a deny rule first.
		$this->assertTrue($this->plainFindMatch($this->someURL));
		$this->urlFilter->addDenyPattern('#.*#');
		$this->assertFalse($this->plainFindMatch($this->someURL));
		
		//test acceptSubdir = false
		$this->urlFilter->addAcceptURL($this->someURL, false);
		$this->assertTrue($this->plainFindMatch($this->someURL));
		$this->assertFalse($this->plainFindMatch($this->someURLSubDir));
		$this->assertFalse($this->plainFindMatch($this->otherURL));
		
		//test acceptSubdir = true
		$this->urlFilter->addAcceptURL($this->someURL, true);
		$this->assertTrue($this->plainFindMatch($this->someURLSubDir));
		$this->assertFalse($this->plainFindMatch($this->otherURL));
	}
	
	public function testAddAcceptPattern(){
		//to test accepting we need to set up a deny rule first.
		$this->assertTrue($this->plainFindMatch($this->someURL));
		$this->urlFilter->addDenyPattern('#.*#');
		$this->assertFalse($this->plainFindMatch($this->someURL));
		
		$this->urlFilter->addAcceptPattern($this->somePattern);
		$this->assertTrue($this->plainFindMatch($this->someURL));
		$this->assertTrue($this->plainFindMatch($this->someURLSubDir));
		$this->assertFalse($this->plainFindMatch($this->otherURL));
		
		$this->urlFilter->addAcceptPattern('#.*#');
		$this->assertTrue($this->plainFindMatch($this->otherURL));
	}

	public function testAddDenyURL(){
		$this->assertTrue($this->plainFindMatch($this->someURL));
		
		//test denySubdir = false
		$this->urlFilter->addDenyURL($this->someURL, false);
		$this->assertFalse($this->plainFindMatch($this->someURL));
		$this->assertTrue($this->plainFindMatch($this->someURLSubDir));
		$this->assertTrue($this->plainFindMatch($this->otherURL));
		
		//test denySubdir = true
		$this->urlFilter->addDenyURL($this->someURL, true);
		$this->assertFalse($this->plainFindMatch($this->someURL));
		$this->assertFalse($this->plainFindMatch($this->someURLSubDir));
		$this->assertTrue($this->plainFindMatch($this->otherURL));
	}

	public function testAddDenyPattern(){
		$this->urlFilter->addDenyPattern($this->somePattern);
		$this->assertFalse($this->plainFindMatch($this->someURL));
		$this->assertFalse($this->plainFindMatch($this->someURLSubDir));
		$this->assertTrue($this->plainFindMatch($this->otherURL));
		
		$this->urlFilter->addDenyPattern('#.*#');
		$this->assertFalse($this->plainFindMatch($this->otherURL));
	}
	//addAcceptURL
	//addAcceptPattern
	//addDenyURL
	//addDenyPattern
	//testPattern
	public function testFindMatchAgreesWithTestPattern(){
		//$this->sut->testPattern
	}
	//findMatch
	public function testFindMatchIsEquivalentToAccept(){
		//default case
		$this->assertEquals($this->urlFilter->accept(null), $this->plainFindMatch());
		//matching deny rule
		$this->urlFilter->addDenyURL($_SERVER['REQUEST_URI']);
		$this->assertEquals($this->urlFilter->accept(null), $this->plainFindMatch());
		//matching accept rule
		$this->urlFilter->addAcceptURL($_SERVER['REQUEST_URI']);
		$this->assertEquals($this->urlFilter->accept(null), $this->plainFindMatch());
	}
	
	/**
	 * Get only the boolean part of the result of 
	 *  DashboardLogURLFilter->findMatch().
	 *  
	 * @param string $url
	 * @return boolean
	 */
	private function plainFindMatch($url = ''){
		$res = $this->urlFilter->findMatch($url);
		return $res['result'];
	}
}