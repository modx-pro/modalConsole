<?php

/**
 * The home manager controller for modalConsole.
 *
 */
class modalConsoleHomeManagerController extends modalConsoleMainController {
	/* @var modalConsole $modalConsole */
	public $modalConsole;


	/**
	 * @param array $scriptProperties
	 */
	public function process(array $scriptProperties = array()) {
	}


	/**
	 * @return null|string
	 */
	public function getPageTitle() {
		return $this->modx->lexicon('modalconsole');
	}


	/**
	 * @return void
	 */
	public function loadCustomCssJs() {
		$this->addCss($this->modalConsole->config['cssUrl'] . 'mgr/main.css');
		$this->addCss($this->modalConsole->config['cssUrl'] . 'mgr/bootstrap.buttons.css');
		$this->addJavascript($this->modalConsole->config['jsUrl'] . 'mgr/misc/utils.js');
		$this->addJavascript($this->modalConsole->config['jsUrl'] . 'mgr/widgets/items.grid.js');
		$this->addJavascript($this->modalConsole->config['jsUrl'] . 'mgr/widgets/items.windows.js');
		$this->addJavascript($this->modalConsole->config['jsUrl'] . 'mgr/widgets/home.panel.js');
		$this->addJavascript($this->modalConsole->config['jsUrl'] . 'mgr/sections/home.js');
		$this->addHtml('<script type="text/javascript">
		Ext.onReady(function() {
			MODx.load({ xtype: "modalconsole-page-home"});
		});
		</script>');
	}


	/**
	 * @return string
	 */
	public function getTemplateFile() {
		return $this->modalConsole->config['templatesPath'] . 'home.tpl';
	}
}