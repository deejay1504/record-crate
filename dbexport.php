<html>
<head>
<title>Database Export</title>
<link type="text/css" rel="stylesheet" href="/stylesheets/main.css" />
<link type="text/css" rel="stylesheet" href="/stylesheets/jquery-ui-redmond-1.10.3.custom.css" />
<script src="/js/jquery-1.9.1.js"></script>
<script src="/js/jquery-ui-1.10.3.custom.js"></script>
<script type="text/javascript">
	$(document).ready(function() {

		$("#backExportButton").click(function(event) {
			event.preventDefault();
			window.location.href = '/displaycrate.php';
		});
		
	});
</script>
</head>

<body>
<?php
	require_once 'dbutils.php';
	
	$config = require 'config.php';

	$db = new DbUtils;  
	$db->dbExport($config['export_dir'], $config['export_name']);
	$fileName = $config['export_dir'] . $config['export_name'];
?>

<div id="container">
	
	<div class="rowStyle">
		<div id="crudMainHeader" class="crudMainHeader"><h1>Database Export</h1></div>
		<div class="crudMainDate"> <?php echo date("D j M, Y"); ?> </div>
	</div>

	<div class="rowStyle crudMainHeader dbMsg">Database has been exported to <?php echo $fileName ?> </div>
	
	<div class="rowStyle backExportDiv"><button id="backExportButton">Back</button></div>
	
</div>
</body>
</html>