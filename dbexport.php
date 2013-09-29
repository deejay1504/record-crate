<?php
	header('Content-Type: text/html; charset=utf-8');
	
	require_once 'dbutils.php';
	
	$config = require 'config.php';

	$db = new DbUtils;  
	$db->dbExport($config['export_dir'], $config['export_name']);
	$fileName = $config['export_dir'] . $config['export_name'];
	
	$return['msg'] = $config['export_dir'] . $config['export_name'];
	echo json_encode($return);
?>