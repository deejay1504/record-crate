<html>
<head>
<title>Record Crate Main Menu</title>
<link type="text/css" rel="stylesheet" href="/stylesheets/main.css" />
<link type="text/css" rel="stylesheet" href="/stylesheets/jquery-ui-redmond-1.10.3.custom.css" />
<script src="/js/jquery-1.9.1.js"></script>
<script src="/js/jquery-ui-1.10.3.custom.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$("#accordion").accordion({
  			active: false,
  			collapsible: true
		});
	});
</script>
</head>
<body class="menuBody">
	<?php
		require_once 'dbutils.php';
		
		$db = new DbUtils;  
		$db->countTotals($crudOp, $searchFieldValue, $searchField, $likeFieldValue);
		
        // Select the last record entered
        $sql = "SELECT * FROM crate c1
				WHERE c1.songId
				IN (
					SELECT max( c2.songId )
					FROM crate c2
				)";
				
		try { 
			$q = $db->dbConnection->query($sql); 
		} 
		catch (PDOException $e) { 
		   die("Query failure: " . $e->getMessage()); 
		}
		
		$q->setFetchMode(PDO::FETCH_ASSOC);
		$dbRow = $q->fetch(); 
					
		$artist      = trim(htmlspecialchars(stripslashes($dbRow['artist'])));
		$songTitle   = trim(htmlspecialchars(stripslashes($dbRow['songTitle'])));
		$recordLabel = trim(htmlspecialchars(stripslashes($dbRow['recordLabel'])));
		$year        = trim(htmlspecialchars(stripslashes($dbRow['year'])));
		$songFormat  = trim(htmlspecialchars(stripslashes($dbRow['songFormat'])));
		$genre       = trim(htmlspecialchars(stripslashes($dbRow['genre'])));
		$info        = '<b>' . $artist . ', ' . $songTitle . ', ' . $recordLabel . ', ' 
			. $songFormat . ', Genre: ' . $genre . '</b>';
		$totalRecs   = $db->totalASides + $db->totalAASides;
		$countInfo   = 'Total Records in Crate: <b>' . $totalRecs . '</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(<b>' . $db->totalASides 
			. '</b> A Sides &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>' . $db->totalAASides
			. '</b> AA Sides &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>' . $db->totalBSides . '</b> B Sides)';
					
	?>
	<div id="container">
		<div id="accordion">
		  <h3>Record Crate</h3>
		  <div>
		    <p><center><label class="countLabel"><?php echo $countInfo; ?></label></center></p>
		  </div>
		  <h3>Last Record Entered</h3>
		  <div>
		    <p><center><label class="infoLabel"><?php echo $info; ?></label></center></p>
		  </div>
		</div>
		<div class="crateButtonField">
			<input type="image" name="crateButton" value="Edit" src="/images/record_crate.jpg" title="Open record crate"
				onclick="location.href='displaycrate.php'";
			/>
		</div>
		<div class="recordButtonField">
			<input type="image" name="recordButton" value="Edit" src="/images/record.png" title="Add a new record"
				onclick="location.href='crud.php?crudOp=I'";
			/>
		</div>
	</div>
</body>
</html>