<?php

/**
 * Class modalConsoleMainController
 */
abstract class modalConsoleMainController extends modExtraManagerController {
	/** @var modalConsole $modalConsole */
	public $modalConsole;


	/**
	 * @return void
	 */
	public function initialize() {
		$corePath = $this->modx->getOption('modalconsole_core_path', null, $this->modx->getOption('core_path') . 'components/modalconsole/');
		require_once $corePath . 'model/modalconsole/modalconsole.class.php';

		$this->modalConsole = new modalConsole($this->modx);
		//$this->addCss($this->modTerminal->config['cssUrl'] . 'mgr/main.css');
		$this->addJavascript($this->modalConsole->config['jsUrl'] . 'mgr/modalconsole.js');
		$this->addHtml('
		<script type="text/javascript">
			modalConsole.config = ' . $this->modx->toJSON($this->modalConsole->config) . ';
			modalConsole.config.connector_url = "' . $this->modalConsole->config['connectorUrl'] . '";
		</script>
		');

		parent::initialize();
	}


	/**
	 * @return array
	 */
	public function getLanguageTopics() {
		return array('modalconsole:default');
	}


	/**
	 * @return bool
	 */
	public function checkPermissions() {
		return true;
	}
}


/**
 * Class IndexManagerController
 */
class IndexManagerController extends modalConsoleMainController {

	/**
	 * @return string
	 */
	public static function getDefaultController() {
		return 'home';
	}
}