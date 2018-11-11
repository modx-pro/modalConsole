<?php
if ($modx->context->key != 'mgr') return;
switch ($modx->event->name) {
    case 'OnManagerPageBeforeRender':
        $assetsUrl = $modx->getOption('modalconsole_assets_url', null, $modx->getOption('assets_url') . 'components/modalconsole/');
        /** @var modalConsole $modalConsole */
        if ($modx->hasPermission('console')) {
            $modx->controller->addLexiconTopic('modalconsole:default');
            $css = $modx->getOption('modalconsole_cssFile', null, $assetsUrl.'css/mgr/modalconsole.css');
            if ($css) $modx->controller->addCss($css);
            $js = $modx->getOption('modalconsole_jsFile', null, $assetsUrl.'js/mgr/modalconsole.js');
            if ($js) $modx->controller->addJavascript($js);


            $config = array(
                'connectorUrl' => $assetsUrl.'connector.php',
                'limit' => $modx->getOption('modalconsole_history_limit', null, 20),
            );
            $_html = "<script>modalConsole.config = {$modx->toJSON($config)} </script>";
            $modx->controller->addHtml($_html);
        }
        break;
}