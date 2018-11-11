<?php

$settings = array();

$tmp = array(
	'history_limit' => array(
		'xtype' => 'numberfield',
		'value' => 20,
		'area' => 'modalconsole_main',
	),
    'cssUrl' => array(
		'xtype' => 'textfield',
		'value' => '{assets_url}components/modalconsole/css/mgr/modalconsole.css',
		'area' => 'modalconsole_main',
	),
    'jsUrl' => array(
		'xtype' => 'textfield',
		'value' => '{assets_url}components/modalconsole/js/mgr/modalconsole.js',
		'area' => 'modalconsole_main',
	),
);

foreach ($tmp as $k => $v) {
	/* @var modSystemSetting $setting */
	$setting = $modx->newObject('modSystemSetting');
	$setting->fromArray(array_merge(
		array(
			'key' => 'modalconsole_' . $k,
			'namespace' => PKG_NAME_LOWER,
		), $v
	), '', true, true);

	$settings[] = $setting;
}

unset($tmp);
return $settings;
