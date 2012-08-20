<?php
/**
 * Controller for the log file tab.
 */
class DashboardLogFileController extends Controller implements DashboardPanelContentProvider {
	private $fileKey;
	private $offset = -100;
	
	public static function add_log_file_panel(){
		$dlfc = new self();
		$dlfc->addFilePanel();
	}

	public function getPanelContent(DashboardPanel $panel) {
		$fields = array();
		foreach(DashboardLogFile::get_available_log_files() as $name){
			$this->fileKey = $name;
			$logContents = $this->readlogfile();
			$logarea = new CompositeField(new LiteralField('internalName', $logContents));
			$logarea->addExtraClass('SSDD-log-file-area-'.$name);
			$fields[$name] = $logarea;
		}
		if(count($fields) > 0){
			$panel->addFormField(new AutomaticRefreshButton('readlogfile', 'Update'));
			$lines = array('10'=>'10', '20'=>'20', '50' => '50', '100'=>'100', '100+'=>'100+');
			$panel->addFormField(new DropDownField('show_last_lines', 
				'Number of lines to show', $lines, '100+'));
			$panel->addFormField(new SelectionGroup('SSDD-files-selection',
				$fields));
		} else {
			$panel->addFormField(new LiteralField('internalName',
					'Use DashboardLogFile::register_log_file() to register log files.'));
		}
	}
	
	public function ReadLogData(){
		$data = DashboardLogFile::read_log_file($this->offset, $this->fileKey);
		if(!is_array($data)){
			echo 'Tried to read file "'.Convert::raw2xml($this->fileKey)
					.'". Result: '.Convert::raw2xml($data);
			return;
		}
		$items = new ArrayList();
		foreach($data['text'] as $line){
			if(trim($line) == '') continue;
			$items->push(new ArrayData(array('Line' => $line)));
		}
		return new ArrayData(array(
			'LogFileName' => $this->fileKey,
			'posEOF' => $data['last'],
			'Children' => $items
		));
	}
	
	/**
	 * This is an action controller that returns the newest log messages.
	 * It gets called via AJAX.
	 * @return string the HTML.
	 */
	public function readlogfile(SS_HTTPRequest $request = null){
		if($request != null) {
			if($request->latestParam('OtherID') != ''){
				$this->offset = intval($request->latestParam('OtherID'));
			}
			$this->fileKey = $request->latestParam('ID');
		}
		return $this->renderWith('DashboardLogFileAjax');
	}
	
	private function addFilePanel(){
		$panel = new DashboardPanel('Files');
		$panel->setContentProvider($this);
		$panel->forwardAction('readlogfile', $this);
		DeveloperDashboard::inst()->addPanel($panel);
	}
}

?>
