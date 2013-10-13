	<?php
		require_once 'paginator.php';
		require_once 'dbutils.php';
		 
		try {
			$artistHeader         = "Artist";
			$songTitleHeader      = "Song Title";
			$recordLabelHeader    = "Record Label";
			$yearHeader           = "Year";
			$numberOfCopiesHeader = "Copies";
			$durationHeader       = "Duration";
			$sideHeader           = "Side";
			$songFormatHeader     = "Song Format";
			$genreHeader          = "Genre";
			$bpmHeader            = "BPM";
			
			$sortField = (empty($_GET['sortField'])) ? "artist, songTitle" : $_GET['sortField']; 
	
			if ($sortField == "artist, songTitle") {
				$sortArtistSelected  = "selected";
			} else if ($sortField == "songTitle") {
				$sortSongTitleSelected  = "selected";
			} else if ($sortField == "recordLabel") {
				$sortRecordLabelSelected  = "selected";
			} else if ($sortField == "year") {
				$sortYearSelected  = "selected";
			} else if ($sortField == "duration") {
				$sortDurationSelected = "selected";
			} else if ($sortField == "numberOfCopies") {
				$numberOfCopiesSelected = "selected";
			} else if ($sortField == "side") {
				$sortSideSelected  = "selected";
			} else if ($sortField == "songFormat") {
				$sortSongFormatSelected  = "selected";
			} else if ($sortField == "genre") {
				$sortGenreSelected  = "selected";
			} else if ($sortField == "bpm") {
				$sortBpmSelected  = "selected";
			}
			
			$searchField = (empty($_GET['searchField'])) ? "artist" : $_GET['searchField']; 
			
			if ($searchField == "Artist") {
				$searchArtistSelected  = "selected";
			} else if ($searchField == "Song Title") {
				$searchSongTitleSelected  = "selected";
			} else if ($searchField == "Record Label") {
				$searchRecordLabelSelected  = "selected";
			} else if ($searchField == "Year") {
				$searchYearSelected  = "selected";
			} else if ($searchField == "Duration") {
				$searchDurationSelected  = "selected";
			} else if ($searchField == "Copies") {
				$searchNumberOfCopiesSelected  = "selected";
			} else if ($searchField == "Side") {
				$searchSideSelected  = "selected";
			} else if ($searchField == "Song Format") {
				$searchSongFormatSelected  = "selected";
			} else if ($searchField == "Genre") {
				$searchGenreSelected  = "selected";
			} else if ($searchField == "BPM") {
				$searchBpmSelected  = "selected";
			}	
			
			$searchFieldValue = $_GET['searchFieldValue']; 
			$crudOp           = $_GET['crudOp']; 
			$orderByField     = (empty($_GET['orderByField'])) ? "asc" : $_GET['orderByField']; 
			
			if ($orderByField == "asc") {
				$ascSelected   = "selected";
				$sortOrderIcon = "/images/down_arrow.png";
				$sortTitle     = "Sort in descending order";
			} else if ($orderByField == "desc") {
				$descSelected  = "selected";
				$sortOrderIcon = "/images/up_arrow.png";
				$sortTitle     = "Sort in ascending order";
			}
			
			// If we are sorting by artist descending, remove songTitle from the field
			if ($sortField == "artist, songTitle" && $orderByField == "desc") {
				$sortField = "artist";
			}
			
			$likeFieldValue = '\'%' . $searchFieldValue . '%\'';
	
			$db = new DbUtils;  
			$db->countTotals($crudOp, $searchFieldValue, $searchField, $likeFieldValue);
		    
			$pages                   = new Paginator;  
			$pages->sortField        = $sortField;
			$pages->orderByField     = $orderByField;
			$pages->searchField      = $searchField;
			$pages->searchFieldValue = $searchFieldValue;
			$pages->crudOp           = $crudOp;
			$pages->items_total      = $db->totalRecords;
			$pages->mid_range        = 9;  
			$pages->paginate();  
			
			$addUrl = "location.href='crud.php?crudOp=I&sortField=" . $sortField . "&orderBy=" . $orderByField . "'";
			
			$processQuery = false;
	
			$sql = 'SELECT songId,
		                   artist,
		                   songTitle, 
		                   recordLabel,
		                   year, 
		                   numberOfCopies, 
		                   duration, 
		                   side,
		                   songFormat,
		                   genre,
		                   bpm
		            FROM crate';
		            
			if ($crudOp == "SEARCH" && $searchFieldValue != "null") {
				$searchUrl = $_SERVER["REQUEST_URI"];
				$processQuery = true;
		    	$sql = $sql . ' WHERE ' . $searchField . ' like ' . $likeFieldValue . 
		            ' ORDER BY ' . $searchField . ', artist, songTitle ' . $pages->limit;
		        $countSql = 'SELECT COUNT(*) as searchTotal FROM crate WHERE ' . $searchField . ' like ' . $likeFieldValue;
			} else {
				$processQuery = true;
		    	$sql = $sql . ' ORDER BY ' . $sortField . ' ' . $orderByField . ' ' . $pages->limit;
			}
			
			if ($processQuery) {		
			    try { 
				   	$q = $db->dbConnection->query($sql); 
				} 
				catch (PDOException $e) { 
				   die("Query failure: " . $e->getMessage()); 
				}
				
				if ($q == false) { 
					$processRecords = false;
				} else {
					$processRecords = true;
					$q->setFetchMode(PDO::FETCH_ASSOC);
					$totalSearchRecords = "";
					
					// If we have just done a successful search, count the number of records retrieved
					if ($crudOp == "SEARCH" && $searchFieldValue != "null") {
						try { 
					   		$countQuery = $db->dbConnection->query($countSql); 
							$countQuery->setFetchMode(PDO::FETCH_ASSOC);
							while ($dbRow = $countQuery->fetch()): 
		    					$totalSearchRecords = trim(htmlspecialchars($dbRow['searchTotal']));
	        				endwhile;
	        				$recString = $totalSearchRecords == 1 ? 'record found' : 'records found';
	        				$totalSearchRecords = '<b>' . $totalSearchRecords . '</b> ' . $recString;
						} 
						catch (PDOException $e) { 
						   die("Query failure: " . $e->getMessage()); 
						}
					}
				}
			}
	
		} catch (PDOException $pe) {
		    die("Could not connect to the database $dbname :" . $pe->getMessage());
		}
		
	?>
	
	<!DOCTYPE html>
	<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Record Crate</title>
	<link type="text/css" rel="stylesheet" href="/stylesheets/main.css" />
	<link type="text/css" rel="stylesheet" href="/stylesheets/jquery-ui-redmond-1.10.3.custom.css" />
	<script src="/js/jquery-1.9.1.js"></script>
	<script src="/js/jquery-ui-1.10.3.custom.js"></script>
	<script src="/js/script.js"></script>
	<script type="text/javascript">
		function setSongRecord(songRecord, buttonValue) {
			document.getElementById('amendedSongRecord').value = songRecord;
			document.getElementById('buttonPressed').value = buttonValue;
		}
		
		$(document).ready(function() {
			
			var searchFieldArr;
			var searchField; 
			var searchFieldName;
	
			function sortByAscDesc() {
				var sortFieldSelected = $("#sortField").val();
				var sortOrder = $("#sortOrder").attr('src');
				var orderByFieldSelected  = 'asc';
				if (sortOrder.match(/up_arrow.*/)) {
					orderByFieldSelected = 'asc';
				} else {
					orderByFieldSelected = 'desc';
				}
				window.location='/displaycrate.php?sortField=' + sortFieldSelected + '&orderByField=' + orderByFieldSelected;
			} 
	
			function processOkButton() {
				var searchFieldValue = $("#searchFieldValue").val();
				var makeSearch = false;
				if (searchFieldValue == '') {
					alert(searchFieldName + ' cannot be empty');
				} else {
					makeSearch = true;
					if ((searchFieldName == 'BPM' || searchFieldName == 'Year' || searchFieldName == 'Copies') && isNaN(searchFieldValue)) {
						alert(searchFieldName + ' must be numeric');
						makeSearch = false;
					}
					if (searchFieldName == 'Duration' && searchFieldValue.length != 8) {
						alert('Duration must be in the format 00:00:00');
						makeSearch = false;
					}
				}
	            $("#searchDialog").dialog("close");
	            if (makeSearch) {
					window.location.href = '/displaycrate.php?crudOp=SEARCH&searchField=' + searchField + '&searchFieldValue=' + searchFieldValue;
			    } else {
					$("#searchDialog").dialog("open");
			    }
			}
	
			function processYesButton() {
		       	$("#deleteOk").val("true");
	        	confirmDialogBox.dialog("close");
				$("#displayCrateForm").submit();	
			}	
			
			function setConfirmDialogMessage(event, title) {
				event.preventDefault();
				event.stopPropagation();
				
				// Override the confirmDialogBox in script.js to add our own 'Yes' button
				var confirmButtons = confirmDialogBox.dialog("option", "buttons"); 
				$.extend(confirmButtons, { 
					Yes: function() {
						processYesButton(); 
					} 
				});
				confirmDialogBox.dialog("option", "buttons", confirmButtons);
				
				var deleteTitle = '<b>' + title.substring(7) + '</b>';
				$("#confirmDialog").dialog('option', 'title', title);
				$("#confirmDialogLabel").html('Are you sure you want to delete ' + deleteTitle + '?');
				$("#confirmDialog").dialog("open");
			}

			function exportDb(event) {
				event.preventDefault();
				event.stopPropagation();
				
				$.ajax({
					type : 'POST',
					url : 'dbexport.php',
					dataType : 'json',
					success : function(data) {
						$("#searchFieldLabel").html('Data exported to <b>' + data.msg + '</b>');
					},
					error : function(XMLHttpRequest, textStatus, errorThrown) {
						$("#searchFieldLabel").html('ERROR - ' + textStatus + ' ' + errorThrown);
					}
				});
				
				// Override the dialogBox in script.js to add our own 'Ok' button
				var exportDialogBox = dialogBox;
				var dialogButtons = exportDialogBox.dialog("option", "buttons"); 
				$.extend(dialogButtons, { 
					Ok: function() {
						exportDialogBox.dialog("close");
					} 
				});
				exportDialogBox.dialog("option", "buttons", dialogButtons); 
				
				// Hide the Cancel button as we only need an Ok button for this alert dialog
				$(".ui-dialog-buttonpane button:contains('Cancel')").button().hide();
				
				exportDialogBox.dialog('option', 'title', 'Database Export');
				exportDialogBox.dialog('option', 'width', '520px');
				$("#searchFieldValue").hide();
				$(".searchFieldDiv").css({"width":"490px"});
				exportDialogBox.dialog("open");
			}
			
			$("#searchField").change(function() {
				// Override the dialogBox in script.js to add our own 'Ok' button
				var dialogButtons = dialogBox.dialog("option", "buttons"); 
				$.extend(dialogButtons, { 
					Ok: function() {
						processOkButton(); 
					} 
				});
				dialogBox.dialog("option", "buttons", dialogButtons); 
				dialogBox.dialog('option', 'width', '350px');
				
				searchFieldArr  = $("#searchField").val().split(":"); 
				searchField     = searchFieldArr[0]; 
				searchFieldName = searchFieldArr[1]; 
				$("#searchDialog").dialog('option', 'title', searchFieldName + ' Search');
				$("#searchFieldLabel").html('Please enter the ' + searchFieldName + ' search value: ');
				$("#searchFieldValue").show();
				$(".searchFieldDiv").css({"width":"320px"});
				$("#searchDialog").dialog("open");
			});
			
			$("#sortOrder").click(function() {
				sortByAscDesc();
			});
			
			// Use the CSS class here as we have multiple delete button ids
			$(".deleteButton").click(function(event) {
				setConfirmDialogMessage(event, this.title);
			});
	
			// Use the CSS class here as we have multiple delete button ids
			$(".deleteButtonEnd").click(function(event) {
				setConfirmDialogMessage(event, this.title);
			});

			$("#exportButton").click(function(event) {
				exportDb(event);
			});
			
			blink('.textMessage');
			
			$("#currentPage").val() < 10 ? $(".currentPageNumber").css({"left":"48px"}) : $(".currentPageNumber").css({"left":"44px"});
			
		});
		
	</script>
	</head>
	<body>
	<div id="container">
	    
			<div class="mainHeaderStyle dateHeaderPos">   <?php echo date("D j M, Y"); ?> </div>
			
			<div class="mainHeaderStyle sortHeaderPos">Sort by:</div>
			<div class="mainHeaderStyle sortFieldPos">
				<select id="sortField" name="sortField" onchange="window.location='/displaycrate.php?sortField='+this[this.selectedIndex].value;">
					<option value="artist, songTitle" <?php echo $sortArtistSelected      ?> > <?php echo $artistHeader         ?> </option>
					<option value="songTitle"         <?php echo $sortSongTitleSelected   ?> > <?php echo $songTitleHeader      ?> </option>
					<option value="recordLabel"       <?php echo $sortRecordLabelSelected ?> > <?php echo $recordLabelHeader    ?> </option>
					<option value="year"              <?php echo $sortYearSelected        ?> > <?php echo $yearHeader           ?> </option>
					<option value="numberOfCopies"    <?php echo $numberOfCopiesSelected  ?> > <?php echo $numberOfCopiesHeader ?> </option>
					<option value="duration"          <?php echo $sortDurationSelected    ?> > <?php echo $durationHeader       ?> </option>
					<option value="side"              <?php echo $sortSideSelected        ?> > <?php echo $sideHeader           ?> </option>
					<option value="songFormat"        <?php echo $sortSongFormatSelected  ?> > <?php echo $songFormatHeader     ?> </option>
					<option value="genre"             <?php echo $sortGenreSelected       ?> > <?php echo $genreHeader          ?> </option>
					<option value="bpm"               <?php echo $sortBpmSelected         ?> > <?php echo $bpmHeader            ?> </option>
				</select>
			</div>
			<div class="mainHeaderStyle sortOrderIconPos">
				<input type="image" id="sortOrder" name="sortOrder" src="<?php echo $sortOrderIcon ?>" title="<?php echo $sortTitle ?>"
			    />
			</div>
			<div class="mainHeaderStyle searchHeaderPos">Search by:</div>
			<div class="mainHeaderStyle searchByFieldPos">
				<select id="searchField" name="searchField" >
					<option value=""> </option>
					<option value="artist:<?php echo $artistHeader                 ?>" <?php echo $searchArtistSelected      ?> > <?php echo $artistHeader         ?> </option>
					<option value="songTitle:<?php echo $songTitleHeader           ?>" <?php echo $searchSongTitleSelected   ?> > <?php echo $songTitleHeader      ?> </option>
					<option value="recordLabel:<?php echo $recordLabelHeader       ?>" <?php echo $searchRecordLabelSelected ?> > <?php echo $recordLabelHeader    ?> </option>
					<option value="year:<?php echo $yearHeader                     ?>" <?php echo $searchYearSelected        ?> > <?php echo $yearHeader           ?> </option>
					<option value="numberOfCopies:<?php echo $numberOfCopiesHeader ?>" <?php echo $numberOfCopiesSelected    ?> > <?php echo $numberOfCopiesHeader ?> </option>
					<option value="duration:<?php echo $durationHeader             ?>" <?php echo $searchDurationSelected    ?> > <?php echo $durationHeader       ?> </option>
					<option value="side:<?php echo $sideHeader                     ?>" <?php echo $searchSideSelected        ?> > <?php echo $sideHeader           ?> </option>
					<option value="songFormat:<?php echo $songFormatHeader         ?>" <?php echo $searchSongFormatSelected  ?> > <?php echo $songFormatHeader     ?> </option>
					<option value="genre:<?php echo $genreHeader                   ?>" <?php echo $searchGenreSelected       ?> > <?php echo $genreHeader          ?> </option>
					<option value="bpm:<?php echo $bpmHeader                       ?>" <?php echo $searchBpmSelected         ?> > <?php echo $bpmHeader            ?> </option>
				</select>
			</div>
			
			<div class="mainHeaderStyle exportButtonPos">
				<button id="exportButton" title="Export Database to CSV file">Export Data</button>
			</div>
			
			<div id="searchDialog">
				<label class="searchFieldDiv" id="searchFieldLabel"></label>
				<input type="text" id="searchFieldValue"/>
			</div>
		
			<div id="confirmDialog">
				<label id="confirmDialogLabel"></label>
			</div>
			
			<div class="mainHeaderStyle mainHeaderPos"> <h1>Record Crate</h1> </div>
			<div class="mainHeaderStyle homePageLink">
				<input type="image" name="homeButton" src="/images/home.jpg" title="Home"
		    		onclick="location.href='menu.php'";
			   	 />
			</div>
			<div class="mainHeaderStyle addSongLink">
				<input type="image" name="addButton" src="/images/small_record.png" title="Add a new song"
			    	onclick="<?php echo $addUrl ?>";
			    />
			</div>
			<div class="mainHeaderStyle totalHeaderPos">  Total Records in Crate: <?php echo $db->totalASides + $db->totalAASides; ?> </div>
		
		<?php 
			if ($processRecords) {
		?>
				<div class="mainHeaderStyle aSideHeaderPos">  <?php echo $db->totalASides;  ?> A Sides </div>
				<div class="mainHeaderStyle bSideHeaderPos">  <?php echo $db->totalBSides;  ?> B Sides </div>
				<div class="mainHeaderStyle aaSideHeaderPos"> <?php echo $db->totalAASides; ?> AA Sides </div>
				
			    <div class="headerStyle artistHeaderPos">         <?php echo $artistHeader         ?> </div>
			    <div class="headerStyle songTitleHeaderPos">      <?php echo $songTitleHeader      ?> </div>
			    <div class="headerStyle recordLabelHeaderPos">    <?php echo $recordLabelHeader    ?> </div>
			    <div class="headerStyle yearHeaderPos">           <?php echo $yearHeader           ?> </div>
			    <div class="headerStyle numberOfCopiesHeaderPos"> <?php echo $numberOfCopiesHeader ?> </div>
			    <div class="headerStyle durationHeaderPos">       <?php echo $durationHeader       ?> </div>
			    <div class="headerStyle sideHeaderPos">           <?php echo $sideHeader           ?> </div>
			    <div class="headerStyle songFormatHeaderPos">     <?php echo $songFormatHeader     ?> </div>
			    <div class="headerStyle genreHeaderPos">          <?php echo $genreHeader          ?> </div>
			    <div class="headerStyle bpmHeaderPos">	          <?php echo $bpmHeader            ?> </div>
	    
		<?php } ?>

	    <br><br><br><br>
	    
	    <form id="displayCrateForm" name="displayCrateForm" method="post" action="/crud.php">
	    
			<?php 
				if ($processRecords) {
					while ($dbRow = $q->fetch()): 
					
					$songId         = trim(htmlspecialchars($dbRow['songId']));
					$artist         = trim(htmlspecialchars(stripslashes($dbRow['artist'])));
					$songTitle      = trim(htmlspecialchars(stripslashes($dbRow['songTitle'])));
					$recordLabel    = trim(htmlspecialchars(stripslashes($dbRow['recordLabel'])));
					$year           = trim(htmlspecialchars(stripslashes($dbRow['year'])));
					$numberOfCopies = trim(htmlspecialchars(stripslashes($dbRow['numberOfCopies'])));
					$duration       = trim(htmlspecialchars(stripslashes($dbRow['duration'])));
					$side           = trim(htmlspecialchars(stripslashes($dbRow['side'])));
					$songFormat     = trim(htmlspecialchars(stripslashes($dbRow['songFormat'])));
					$genre          = trim(htmlspecialchars(stripslashes($dbRow['genre'])));
					$bpm            = trim(htmlspecialchars(stripslashes($dbRow['bpm'])));
					$displayName    = $artist . ' - ' . $songTitle; 
			?>
				
			<div class="rowStyle">
	            <div class="editButton">
	            	<input type="image" id="editButton" name="editButton" value="Edit" src="/images/pencil.png" title="Edit <?php echo $displayName; ?>"
	            		onclick="setSongRecord('<?php echo $dbRow['songId'].'::'.$dbRow['artist'].'::'.$dbRow['songTitle'].'::'.$dbRow['recordLabel'].'::'.$dbRow['year']
	            			.'::'.$dbRow['numberOfCopies'].'::'.$dbRow['duration'].'::'.$dbRow['side'].'::'.$dbRow['songFormat'].'::'.$dbRow['genre'].'::'.$dbRow['bpm']; 
	            		?>', 'editButton');"  
	            	/>
				</div>
				
				<div>
					<input type="image" id="deleteButton<?php echo $songId; ?>" class="deleteButton" name="deleteButton" value="Delete" src="/images/trash.png" title="Delete <?php echo $displayName; ?>"
	            		onclick="setSongRecord('<?php echo $dbRow['songId'].'::'.$dbRow['artist'].'::'.$dbRow['songTitle'].'::'.$dbRow['recordLabel'].'::'.$dbRow['year']
	        				.'::'.$dbRow['numberOfCopies'].'::'.$dbRow['duration'].'::'.$dbRow['side'].'::'.$dbRow['songFormat'].'::'.$dbRow['genre'].'::'.$dbRow['bpm']; 
		    			?>', 'deleteButton');"
	        		/>
				</div>
				
				<div class="artistField">         <?php echo $artist;         ?> </div>
				<div class="songTitleField">      <?php echo $songTitle;      ?> </div>
				<div class="recordLabelField">    <?php echo $recordLabel;    ?> </div>
				<div class="yearField">           <?php echo $year;           ?> </div>
				<div class="numberOfCopiesField"> <?php echo $numberOfCopies; ?> </div>
				<div class="durationField">       <?php echo $duration;       ?> </div>
				<div class="sideField">           <?php echo $side;           ?> </div>
				<div class="songFormatField">     <?php echo $songFormat;     ?> </div>
				<div class="genreField">          <?php echo $genre;          ?> </div>
				<div class="bpmField">            <?php echo $bpm;            ?> </div>
				
				<div class="editButtonEnd">
					<input type="image" id="editButton" name="editButton" value="Edit" src="/images/pencil.png" title="Edit <?php echo $displayName; ?>"
	            		onclick="setSongRecord('<?php echo $dbRow['songId'].'::'.$dbRow['artist'].'::'.$dbRow['songTitle'].'::'.$dbRow['recordLabel'].'::'.$dbRow['year']
	            			.'::'.$dbRow['numberOfCopies'].'::'.$dbRow['duration'].'::'.$dbRow['side'].'::'.$dbRow['songFormat'].'::'.$dbRow['genre'].'::'.$dbRow['bpm']; 
	            		?>', 'editButton');"   
	            	/>
				</div>
				
				<div>
					<input type="image" id="deleteButton<?php echo $songId; ?>" class="deleteButtonEnd" name="deleteButton" value="Delete" src="/images/trash.png" title="Delete <?php echo $displayName; ?>"
	            		onclick="setSongRecord('<?php echo $dbRow['songId'].'::'.$dbRow['artist'].'::'.$dbRow['songTitle'].'::'.$dbRow['recordLabel'].'::'.$dbRow['year']
	        				.'::'.$dbRow['numberOfCopies'].'::'.$dbRow['duration'].'::'.$dbRow['side'].'::'.$dbRow['songFormat'].'::'.$dbRow['genre'].'::'.$dbRow['bpm']; 
		    			?>', 'deleteButton');"
	        		/>
				</div>
				
			</div>
			<?php 
					endwhile;
				} else {
			?>
					<div class="textMessage noRecordsFound">NO RECORDS FOUND</div>
			<?php 
				} 
			?>
			
	        <input type="hidden" id="deleteOk"          name="deleteOk" />
	        <input type="hidden" id="amendedSongRecord" name="amendedSongRecord" />
	        <input type="hidden" id="currentPage"       name="currentPage"  value="<?php echo $pages->current_page; ?>"/>
	        <input type="hidden" id="itemsPerPage"      name="itemsPerPage" value="<?php echo $pages->items_per_page; ?>"/>
	        <input type="hidden" id="sortField"         name="sortField"    value="<?php echo $pages->sortField; ?>"/>
	        <input type="hidden" id="searchUrl"         name="searchUrl"    value="<?php echo $searchUrl; ?>"/>
	        <input type="hidden" id="buttonPressed"     name="buttonPressed"/>
		</form>
	
		<br><br>	
	
		<?php 
			if ($processRecords) {
		?>
				<div class="rowStyle">
					<div class="currentPageField"> Page 
						<div class="circle1"></div>
						<div class="currentPageNumber"> <?php echo $pages->current_page; ?> </div>
						<div class="ofText"> of </div>
						<div class="circle2"></div>
						<div class="totalPages"> <?php echo $pages->num_pages; ?> </div>
					</div>
					<div class="searchTotal"><?php echo $totalSearchRecords ?></div>
					<div class="displayPagesField">    <?php echo $pages->display_pages(); ?> </div>
					<div class="displayJumpMenuField"> <?php echo $pages->display_jump_menu(). $pages->display_items_per_page(); ?> </div>
				</div

		<?php } ?>

	</body>
	</div>
	</html>