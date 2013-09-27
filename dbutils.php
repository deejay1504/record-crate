<?php  

class DbUtils {  
    
    var $host = 'localhost';
    var $dbname = 'main_db';
    var $username = 'root';
    var $password = 'nativesun';
    var $dbConnection;
    var $totalRecords;
    var $totalASides;
    var $totalBSides;
    var $totalAASides;
  
    function DbUtils() { 
    	try {
	        $this->dbConnection = 
	        	new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->username, $this->password, 
	        		array(PDO::ATTR_PERSISTENT => true));
    	} catch (PDOException $pe) {
		    die("Could not connect to the database $dbname :" . $pe->getMessage());
		}
    }  
    
    function countTotals($crudOp, $searchFieldValue, $searchField, $likeFieldValue) {
    	// Count the genuine number of records for the pagination
	    $sql = 'SELECT count(*) as totalRecords
	            FROM crate';
	    
	    if ($crudOp == "SEARCH" && $searchFieldValue != "null") {
	    	$sql = $sql . ' WHERE ' . $searchField . ' like ' . $likeFieldValue;
	    }
	    
	    try { 
		   $countQuery = $this->dbConnection->query($sql); 
		} 
		catch (PDOException $e) { 
		   die("Query failure: " . $e->getMessage()); 
		}
		
		$countQuery->setFetchMode(PDO::FETCH_ASSOC);

		// Count the number of A Sides to display as part of the total
	    $sql = 'SELECT count(*) as totalASides
	            FROM crate
				WHERE side = \'A Side\'';
	    
	    try { 
		   $aSideQuery = $this->dbConnection->query($sql); 
		} 
		catch (PDOException $e) { 
		   die("Query failure: " . $e->getMessage()); 
		}
		
		$aSideQuery->setFetchMode(PDO::FETCH_ASSOC);

		// Count the number of B Sides 
	    $sql = 'SELECT count(*) as totalBSides
	            FROM crate
				WHERE side = \'B Side\'';
	    
	    try { 
		   $bSideQuery = $this->dbConnection->query($sql); 
		} 
		catch (PDOException $e) { 
		   die("Query failure: " . $e->getMessage()); 
		}
		
		$bSideQuery->setFetchMode(PDO::FETCH_ASSOC);
	
		// Count the number of AA Sides to display as part of the total
	    $sql = 'SELECT count(*) as totalAASides
	            FROM crate
				WHERE side = \'AA Side\'';
	    
	    try { 
		   $aaSideQuery = $this->dbConnection->query($sql); 
		} 
		catch (PDOException $e) { 
		   die("Query failure: " . $e->getMessage()); 
		}
		
		$aaSideQuery->setFetchMode(PDO::FETCH_ASSOC);
		
		while ($dbRow = $countQuery->fetch()): 
	    	$this->totalRecords = trim(htmlspecialchars($dbRow['totalRecords']));
        endwhile;

		while ($dbRow = $aSideQuery->fetch()): 
	    	$this->totalASides = trim(htmlspecialchars($dbRow['totalASides']));
        endwhile;

		while ($dbRow = $bSideQuery->fetch()): 
	    	$this->totalBSides = trim(htmlspecialchars($dbRow['totalBSides']));
        endwhile;
        
		while ($dbRow = $aaSideQuery->fetch()): 
	    	$this->totalAASides = trim(htmlspecialchars($dbRow['totalAASides']));
        endwhile;
        
        $this->totalAASides = ($this->totalAASides / 2);
    }
  
}  
?>  