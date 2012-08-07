<?php
/**
 * Controller for the log file tab.
 */
class DashboardLogFileController extends Controller implements DashboardPanelContentProvider {
	private $fileKey;
	private $offset = -10;
	
	public static function add_log_file_panel(){
		$dlfc = new self();
		$dlfc->addFilePanel();
	}

	public function getPanelContent(DashboardPanel $panel) {
		$panel->addFormField(new AutomaticRefreshButton('readlogfile', 'Update'));
		$fields = array();
		foreach(DashboardLogFile::get_available_log_files() as $name){
			$this->fileKey = $name;
			$logContents = $this->readlogfile();
			$logarea = new CompositeField(new LiteralField('internalName', $logContents));
			$logarea->addExtraClass('SSDD-log-file-area-'.$name);
			$fields[$name] = $logarea;
		}
		$panel->addFormField(new SelectionGroup('SSDD-files-selection', $fields));
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
