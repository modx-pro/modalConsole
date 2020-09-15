<?php

$settings = array();

$tmp = array(
	'cssUrl' => array(
		'xtype' => 'textfield',
		'value' => '{assets_url}components/modalconsole/css/mgr/modalconsole.css',
		'area' => 'modalconsole_main',
	),
	'enable' => array(
		'xtype' => 'combo-boolean',
		'value' => true,
		'area' => 'modalconsole_main',
	),
	'files_path' => array(
		'xtype' => 'textfield',
		'value' => '{core_path}components/modalconsole/files/',
		'area' => 'modalconsole_main',
	),
	'history_limit' => array(
		'xtype' => 'numberfield',
		'value' => 20,
		'area' => 'modalconsole_main',
	),
	'jsUrl' => array(
		'xtype' => 'textfield',
		'value' => '{assets_url}components/modalconsole/js/mgr/modalconsole.js',
		'area' => 'modalconsole_main',
	),
	'position' => array(
		'xtype' => 'textfield',
		'value' => 'right',
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
