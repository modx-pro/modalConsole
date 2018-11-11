<?php
if (file_exists(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php')) {
    /** @noinspection PhpIncludeInspection */
    require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
}
else {
    require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/config.core.php';
}
/** @noinspection PhpIncludeInspection */
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
/** @noinspection PhpIncludeInspection */
require_once MODX_CONNECTORS_PATH . 'index.php';
$modx->lexicon->load('modalconsole:default');

// handle request
$corePath = $modx->getOption('modalconsole_core_path', null, $modx->getOption('core_path') . 'components/modalconsole/');
$path = $corePath . 'processors/';

/** @var modConnectorRequest $request */
$modx->request->handleRequest(array(
	'processors_path' => $path,
	'location' => '',
));