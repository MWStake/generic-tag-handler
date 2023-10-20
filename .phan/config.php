<?php

$cfg = require __DIR__ . '/../vendor/mediawiki/mediawiki-phan-config/src/config.php';

$cfg['file_list'] = array_merge(
	$cfg['file_list'],
	[
		'includes/ServiceWiring.php',
		'bootstrap.php',
	]
);

$cfg['directory_list'] = array_merge(
	$cfg['directory_list'],
	[
		realpath( getenv( 'MW_INSTALL_PATH' ) ),
		realpath( __DIR__ . '/../vendor' ),
	]
);
$cfg['exclude_analysis_directory_list'] = array_merge(
	$cfg['exclude_analysis_directory_list'],
	[
		realpath( getenv( 'MW_INSTALL_PATH' ) ),
		realpath( __DIR__ . '/../vendor' ),
	]
);
return $cfg;
