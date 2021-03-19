<?php
	session_start();

	#Application Information
	define ('APP_NAME', 	'Test Core');
	define ('APP_VERSION',	'0.0.1');
	define ('APP_SECTOKEN',	'HabeKeinToken');

	require ("./core/core_class.php");
	$core = new Core;
	$core->debugging = true;
	
	$tpl = new Template;
	
	echo $core::CORE_VERSION. "<br>";
	echo Core::CORE_VERSION. "<br>";
	$tpl->load();
	$tpl->readLanguage('de4.lng');
	$tpl->assignHTML('lang', 	'de');
	$tpl->assignHTML('title',	'Test Core Page');
	$tpl->assignHTML('favicon',	'./favicon.png');
	$tpl->assignHTML('meta', 'description',	'Rendezvous mit der Zukunft – Bezahlen 2025');
	$tpl->assignHTML('meta', 'keywords',	'');
	$tpl->assignHTML('meta', 'author',		'Marc-Andre Zweier');
	$tpl->assignHTML('meta', 'copyright',	'media-e-motion');
	$tpl->assignHTML('css',	 './css/custom.css');
	$tpl->assignHTML('js',	 './js/custom.js', 'head');

	//Just Testing CSS
	//$core->tpl->readLanguage('de.lng');
	// $tpl->assign ('TEST', Core_Function::CLASS_BUILD);
	echo APP_NAME;


	$tpl->display();
?>