<?php

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Opauth',
	'description' => 'Integrates Opauth into TYPO3. Credits go to the developers of Oauth - see oauth.org',
	'category' => 'Backend',
	'author' => 'Sebastian Michaelsen',
	'author_email' => 'sebastian.michaelsen@t3seo.de',
	'author_company' => 't3seo.de',
	'state' => 'beta',
	'uploadfolder' => 0,
	'clearCacheOnLoad' => 1,
	'version' => '0.0.0',
	'constraints' => array(
		'depends' => array(
			'php' => '5.3.0-0.0.0',
			'typo3' => '6.0.0-6.99.99',
			'extbase' => '0.0.0-0.0.0',
			'fluid' => '0.0.0-0.0.0',
		),
	),
	'_md5_values_when_last_written' => '',
);

?>