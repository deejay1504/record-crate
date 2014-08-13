<html>
<head>
<title>Record Crate Main Menu</title>
<link type="text/css" rel="stylesheet" href="/php/stylesheets/main.css" />
<link type="text/css" rel="stylesheet" href="/php/stylesheets/jquery-ui-redmond-1.10.3.custom.css" />
<script src="/php/js/jquery-1.9.1.js"></script>
<script src="/php/js/jquery-ui-1.10.3.custom.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$("#accordion").accordion({
  			active: false,
  			collapsible: true
		});
		
		$("#songFormat").change(function() {
  			 var locationHref = '/php/menu.php?selectedSongFormat=' + $(this).val();
  			 window.location.href = locationHref;
		});
		
	});
	
</script>
</head>
<body class="menuBody">
	<?php
		require_once 'dbutils.php';
		require_once 'general_utils.php';
		
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
		$songFormat  = trim(htmlspecialchars(stripslashes($dbRow['songFormat'])));
		$genre       = trim(htmlspecialchars(stripslashes($dbRow['genre'])));
		$info        = '<b>' . $artist . ', ' . $songTitle . ', ' . $recordLabel . ', ' 
			. $songFormat . ', Genre: ' . $genre . '</b>';
		$totalRecs   = $db->totalASides + $db->totalAASides;
		$countInfo   = 'Total Records in Crate: <b>' . $totalRecs . '</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(<b>' . $db->totalASides 
			. '</b> A Sides &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>' . $db->totalAASides
			. '</b> AA Sides &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>' . $db->totalBSides . '</b> B Sides)';
			
		// Store the current record format for the other screens to use
		$selectedSongFormat = $_GET['selectedSongFormat']; 		
		if ($selectedSongFormat == '') {
			$selectedSongFormat = getCurrentSongFormat($db);
			$songFormatNum = setSongFormat($selectedSongFormat);
		} else {
			$songFormatNum = setSongFormat($selectedSongFormat);
			$sql = "update config ".
	               "set propertyValue  = '" . $selectedSongFormat . "' " . 
	               "where propertyName = 'currentSongFormat'";
		    try { 
			   	$q = $db->dbConnection->query($sql); 
			} 
			catch (PDOException $e) { 
			   die("Query failure: " . $e->getMessage()); 
			}
		}
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
		  <h3>Admin</h3>
		  <div>
		    <p><center>
		    	<label class="infoLabel">Song Format</label>
		    	<select id='songFormat'>
					<option value="7 inch"  <?php if ($songFormatNum == 1) echo 'selected'; ?> >7 inch</option>
					<option value="10 inch" <?php if ($songFormatNum == 2) echo 'selected'; ?> >10 inch</option>
					<option value="12 inch" <?php if ($songFormatNum == 3) echo 'selected'; ?> >12 inch</option>
					<option value="LP"      <?php if ($songFormatNum == 4) echo 'selected'; ?> >LP</option>
					<option value="CD"      <?php if ($songFormatNum == 5) echo 'selected'; ?> >CD</option>
					<option value="MP3"     <?php if ($songFormatNum == 6) echo 'selected'; ?> >MP3</option>
				</select>
			</center></p>
		  </div>
		</div>
		<div class="crateButtonField">
			<input type="image" name="crateButton" value="Edit" src="/php/images/record_crate.jpg" title="Open record crate"
				onclick="location.href='/php/displaycrate.php'";
			/>
		</div>
		<div class="recordButtonField">
			<input type="image" name="recordButton" value="Edit" src="/php/images/record.png" title="Add a new record"
				onclick="location.href='/php/crud.php?crudOp=I'";
			/>
		</div>
	</div>
	<input type="hidden" id="menuLocationHref"/>
	<input type="hidden" id="openCrateLocationHref"/>
	<input type="hidden" id="songFormatValue"/>
</body>
</html>