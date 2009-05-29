<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Webgrind Controller
 *
 * @package			Webgrind
 * @author			Justin Hernandez <justin@transphorm.com>
 * @copyright		2009
 */
class Webgrind_Controller extends Template_Controller
{

	public $template = FALSE;
	// don't allow in production mode
	const ALLOW_PRODUCTION = FALSE;
	
	// required
	public function __construct()
	{ 
		parent::__construct();
		
		// check if hooks are enabled
		if ( ! Kohana::config('config.enable_hooks'))
			throw new Kohana_User_Exception('Webgrind Exception Error', 'Hooks need to be enabled in config.php.');

		// Make sure we have a timezone for date functions.
		if (ini_get('date.timezone') == '')
			date_default_timezone_set( Kohana::config('webgrind.defaultTimezone') );
		
		// check for op in query string and redirect to function
		if (@$_GET['op'])
		{
			$this->$_GET['op']();
			die();
		}
	}
	
	// load webgrind
	public function index()
	{
		die(new View('webgrind_index'));
	}
	
	// print callinfo list
	public function callinfo_list()
	{
		$reader = Webgrind_Filehandler::getInstance()->getTraceReader(webgrind::get('file'), webgrind::get('costFormat', Kohana::config('webgrind.defaultCostformat')));
		$functionNr = webgrind::get('functionNr');
 		$function = $reader->getFunctionInfo($functionNr);
	
		$result = array('calledFrom'=>array(), 'subCalls'=>array());
		$foundInvocations = 0;
		for($i=0;$i<$function['calledFromInfoCount'];$i++){
			$invo = $reader->getCalledFromInfo($functionNr, $i);
			$foundInvocations += $invo['callCount'];
			$callerInfo = $reader->getFunctionInfo($invo['functionNr']);
			$invo['file'] = $callerInfo['file'];
			$invo['callerFunctionName'] = $callerInfo['functionName'];
			$result['calledFrom'][] = $invo;
		}
		$result['calledByHost'] = ($foundInvocations<$function['invocationCount']);

		for($i=0;$i<$function['subCallInfoCount'];$i++){
			$invo = $reader->getSubCallInfo($functionNr, $i);
			$callInfo = $reader->getFunctionInfo($invo['functionNr']);
			$invo['file'] = $function['file']; // Sub call to $callInfo['file'] but from $function['file']
			$invo['callerFunctionName'] = $callInfo['functionName'];
			$result['subCalls'][] = $invo;
		}
		echo json_encode($result);
	}
	
	// for viewing files
	public function fileviewer()
	{
		$view = new View('webgrind_fileviewer');
	
		$file = webgrind::get('file');
		$line = webgrind::get('line');

		if($file && $file!=''){
			$message = '';
			if(!file_exists($file)){
				$message = $file.' does not exist.';
			} else if(!is_readable($file)){
				$message = $file.' is not readable.';
			} else if(is_dir($file)){
				$message = $file.' is a directory.';
			} 		
		} else {
			$message = 'No file to view';
		}
		
		$view->file = $file;
		$view->line = $line;
		$view->message = $message;
		
		print $view;
	}
	
	// print function list info
	public function function_list()
	{
		$dataFile = webgrind::get('dataFile');
		if($dataFile=='0'){
			$files = Webgrind_Filehandler::getInstance()->getTraceList();
			$dataFile = $files[0]['filename'];
		}
		$reader = Webgrind_Filehandler::getInstance()->getTraceReader($dataFile, webgrind::get('costFormat', Kohana::config('webgrind.defaultCostformat')));
		$functions = array();
		$shownTotal = 0;
		$breakdown = array('internal' => 0, 'user' => 0, 'class' => 0, 'include' => 0);

		for($i=0;$i<$reader->getFunctionCount();$i++) {
			$functionInfo = $reader->getFunctionInfo($i);
		
		
			if (false !== strpos($functionInfo['functionName'], 'php::')) {
				$breakdown['internal'] += $functionInfo['summedSelfCost'];
				$humanKind = 'internal';
				$kind = 'blue';
			} elseif (false !== strpos($functionInfo['functionName'], 'require_once::') ||
				      false !== strpos($functionInfo['functionName'], 'require::') || 
				      false !== strpos($functionInfo['functionName'], 'include_once::') ||
				      false !== strpos($functionInfo['functionName'], 'include::')) {
		        $breakdown['include'] += $functionInfo['summedSelfCost'];
				$humanKind = 'include';
				$kind = 'grey';
			} else {
				if (false !== strpos($functionInfo['functionName'], '->') || false !== strpos($functionInfo['functionName'], '::')) {
				    $breakdown['class'] += $functionInfo['summedSelfCost'];
				    $humanKind = 'class';
				    $kind = 'green';
				} else {
				    $breakdown['user'] += $functionInfo['summedSelfCost'];
				    $humanKind = 'procedural';
				    $kind = 'orange';
				}
		    }
			if (!(int)webgrind::get('hideInternals', 0) || strpos($functionInfo['functionName'], 'php::') === false) {
				$shownTotal += $functionInfo['summedSelfCost'];
				$functions[$i] = $functionInfo;
				$functions[$i]['nr'] = $i;
				$functions[$i]['kind'] = $kind;
				$functions[$i]['humanKind'] = $humanKind;
			} 

		}
		usort($functions, array('webgrind', 'cost_cmp'));

		$remainingCost = $shownTotal*webgrind::get('showFraction');

		$result['functions'] = array();
		foreach($functions as $function){
		
			$remainingCost -= $function['summedSelfCost'];
				
			$result['functions'][] = $function;
			if($remainingCost<0)
				break;
		}
		$result['summedInvocationCount'] = $reader->getFunctionCount();
		$result['summedRunTime'] = $reader->formatCost($reader->getHeader('summary'), 'msec');
		$result['dataFile'] = $dataFile;
		$result['invokeUrl'] = $reader->getHeader('cmd');
		$result['runs'] = $reader->getHeader('runs');
		$result['breakdown'] = $breakdown;
		$result['mtime'] = date(Kohana::config('webgrind.dateFormat'),filemtime(webgrind::xdebugOutputDir().$dataFile));
		echo json_encode($result);
	}
	
	// list available cachegrind files
	public function file_list()
	{
		echo json_encode(Webgrind_Filehandler::getInstance()->getTraceList());
	}
	
	// print version info
	public function version_info()
	{
		$response = @file_get_contents('http://jokke.dk/webgrindupdate.json?version='.webgrind.$webgrindVersion);	
		echo $response;
	}
}
