<?php

require_once 'Zend/Log/Filter/Interface.php';
/**
 * A filter that will accept and deny URLs based on regular expressions.
 * The evaluation is accept before deny and defaults to accept.
 */
class DashboardLogURLFilter implements Zend_Log_Filter_Interface {
	var $acceptPatterns;
	var $denyPatterns;
	
	/**
	 * @param mixed $acceptPatterns an array of accept patterns,
	 *  or a single pattern (as a string)..
	 * @param mixed $denyPatterns an array of deny patterns,
	 *  or a single pattern (as a string).
	 */
	public function __construct(
		$acceptPatterns = array(), $denyPatterns = array()
	){
		if(!is_array($acceptPatterns)){
			$this->acceptPatterns = array($acceptPatterns);
		} else {
			$this->acceptPatterns = $acceptPatterns;
		}
		
		if(!is_array($denyPatterns)){
			$this->denyPatterns = array($denyPatterns);
		} else {
			$this->denyPatterns = $denyPatterns;
		}
	}
	
	/**
	 * Decide if the event should be accepted.
	 * @see framework/thirdparty/Zend/Log/Filter/Zend_Log_Filter_Interface::accept()
	 */
	public function accept($event){
		$url = $_SERVER['REQUEST_URI'];
		
		foreach($this->acceptPatterns as $accept){
			if(preg_match($accept, $url)){
				return true;
			}
		}
		foreach($this->denyPatterns as $deny){
			if(preg_match($deny, $url)){
				return false;
			}
		}
		return true;
	}
	
	/**
	 * Add a rule that will accept only the given URL.
	 * @param string $url
	 * @param boolean $acceptSubdirs also accept all sub directories
	 */
	public function addAcceptURL($url, $acceptSubdirs = false){
		$this->acceptPatterns[] = $this->patternFromURL($url, $acceptSubdirs);
	}
	
	/**
	 * Adds a PCRE compatible regular expression to the list of patterns
	 *  that are accepted.
	 * 
	 * You are responsible for chosing the delimitar and applying quoting.
	 * Take care when using '/' (the forward slash) as the delimiter, 
	 *  since all forward slashes need to be quoted in that case. Consider 
	 *  using a different delimiter instead (e.g. @$/my/url/pattern/[0-9]^@).
	 * 
	 * @param string $pattern A PCRE pattern
	 */
	public function addAcceptPattern($pattern){
		$this->acceptPatterns[] = $pattern;
	}
	
	/**
	 * Add a rule that will deny only the given URL.
	 * @param string $url
	 * @param boolean $denySubdirs also deny all sub directories 
	 */
	public function addDenyURL($url, $denySubdirs = false){
		$this->denyPatterns[] = $this->patternFromURL($url, $denySubdirs);
	}

	/**
	 * Adds a PCRE compatible regular expression to the list of patterns
	 *  that are denied.
	 * 
	 * You are responsible for chosing the delimitar and applying quoting.
	 * Take care when using '/' (the forward slash) as the delimiter, 
	 *  since all forward slashes need to be quoted in that case. Consider 
	 *  using a different delimiter instead (e.g. @$/my/url/pattern/[0-9]^@).
	 * 
	 * @param string $pattern A PCRE pattern
	 */
	public function addDenyPattern($pattern, $denySubdirs = false){
		$this->denyPatterns[] = $pattern;
	}
	
	/**
	 * Allows testing if a given pattern will match the url.
	 * If no URL is given, the URL of the current request is used. 
	 * 
	 * @param string $pattern
	 * @param string $url
	 * @return boolean true if the pattern matches, else false.
	 */
	public function testPattern($pattern, $url = ''){
		if($url == ''){
			$url = $_SERVER['REQUEST_URI'];
		}
		return (preg_match($pattern, $url));
	}
	
	/**
	 * Find which of the currently available patterns would match.
	 * 
	 * @param string $url Defaults to the current URL.
	 * @return array the result and the rule that matched.
	 */
	public function findMatch($url = ''){
		if($url == ''){
			$url = $_SERVER['REQUEST_URI'];
		}
		
		foreach($this->acceptPatterns as $accept){
			if(preg_match($accept, $url)){
				return array('result' => true, 'rule' => $accept);
			}
		}
		foreach($this->denyPatterns as $deny){
			if(preg_match($deny, $url)){
				return array('result' => false, 'rule' => $deny);
			}
		}
		return array('result' => true, 'rule' => 'Defaulting to accept.');
	}
	
	/**
	 * Generate a PCRE from an url.
	 * @param string $url the url
	 * @param boolean $subdirs true if the pattern should match subdirectories.
	 * @return string the pattern
	 */
	private function patternFromURL($url, $subdirs){
		return '#'.preg_quote($url, '#').($subdirs ? '':'$').'#';
	}
}