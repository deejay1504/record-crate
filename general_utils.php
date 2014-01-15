<?php 
	function setSongFormat($songFormatValue) {
		if ($songFormatValue == "7 inch") {
			$songFormatNum = 1;
		} else if ($songFormatValue == "10 inch") {
			$songFormatNum = 2;
		} else if ($songFormatValue == "12 inch") {
			$songFormatNum = 3;
		} else if ($songFormatValue == "LP") {
			$songFormatNum = 4;
		} else if ($songFormatValue == "CD") {
			$songFormatNum = 5;
		} else if ($songFormatValue == "CD Single") {
			$songFormatNum = 6;
		} else if ($songFormatValue == "MP3") {
			$songFormatNum = 7;
		}
		return $songFormatNum;
    }
    
    function getCurrentSongFormat($db) {
    	// Set the selected value for the drop down 'songFormat' field
		$sql = "SELECT propertyValue FROM config 
				WHERE propertyName = 'currentSongFormat'";
				
		try { 
			$q = $db->dbConnection->query($sql); 
		} 
		catch (PDOException $e) { 
		   die("Query failure: " . $e->getMessage()); 
		}
		
		$q->setFetchMode(PDO::FETCH_ASSOC);
		
		while ($dbRow = $q->fetch()): 
			$selectedSongFormat = trim(htmlspecialchars($dbRow['propertyValue']));
		endwhile;
		$songFormatNum = setSongFormat($selectedSongFormat);
		return $selectedSongFormat;
    }
?>  