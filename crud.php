<html>
<head>
<title>Php Crud</title>
<link type="text/css" rel="stylesheet" href="/php/stylesheets/main.css" />
<link type="text/css" rel="stylesheet" href="/php/stylesheets/jquery-ui-redmond-1.10.3.custom.css" />
<script src="/php/js/jquery-1.9.1.js"></script>
<script src="/php/js/jquery-ui-1.10.3.custom.js"></script>
<script src="/php/js/script.js"></script>
<script type="text/javascript">
	$(document).ready(function() {

		function validateForm(event, songBpmWindow, discogsWindow) {
			var makeSearch = true;
			var errorText = '';

			if ($("#artist").val() == '') {
				errorText = errorText + 'Artist field cannot be empty<br>';
				makeSearch = false;
			}
			if ($("#songTitle").val() == '') {
				errorText = errorText + 'Song Title field cannot be empty<br>';
				makeSearch = false;
			}
			if ($("#bpm").val() == '' || isNaN($("#bpm").val())) {
				errorText = errorText + 'BPM field must be numeric<br>';
				makeSearch = false;
			}
			if ($("#year").val() == '' || isNaN($("#year").val())) {
				errorText = errorText + 'Year field must be numeric<br>';
				makeSearch = false;
			}
			if ($("#numberOfCopies").val() == '' || isNaN($("#numberOfCopies").val())) {
				errorText = errorText + 'Copies field must be numeric<br>';
				makeSearch = false;
			}
			if (makeSearch) {
				songBpmWindow.close();
				discogsWindow.close();
				$("#saveDetails").val("saveDetails");
				$("#crudForm").submit();
			} else {
				event.preventDefault();
				event.stopPropagation();
				setAlertDialog('Errors found!', errorText);
		  }
		}

		function setAlertDialog(dialogTitle, errorMsg) {
			// Override the dialogBox in script.js to add our own 'Ok' button
			var dialogButtons = dialogBox.dialog("option", "buttons");
			$.extend(dialogButtons, {
				Ok: function() {
					$("#searchDialog").dialog("close");
				},
			});
			dialogBox.dialog("option", "buttons", dialogButtons);

			// Hide the Cancel button as we only need an Ok button for this alert dialog
			$(".ui-dialog-buttonpane button:contains('Cancel')").button().hide();

			$("#searchDialog").dialog('option', 'title', dialogTitle);
			$("#searchFieldLabel").html(errorMsg);
			$("#searchDialog").dialog("open");
		}

		// Display the Song BPM website to the left of the input form
    var songBpmWindow = window.open('https://songbpm.com', '', 'scrollbars=1, top=170, left=20, width=700, height=800');
    songBpmWindow.focus();

		// Display the Discogs website to the right of the input form
    var discogsWindow = window.open('http://www.discogs.com', '', 'scrollbars=1, top=170, left=1227, width=670, height=800');
    discogsWindow.focus();

		$("#discogsButton").click(function(event) {
			discogs(event);
		});

		$("#submitButton").click(function(event) {
			validateForm(event, songBpmWindow, discogsWindow);
		});

		$("#backButton").click(function(event) {
			event.preventDefault();
			songBpmWindow.close();
			discogsWindow.close();
			window.location.href = $("#displayCrateHref").val();
		});

		$("#submitButton").button({icons: {primary:"ui-icon-disk"}});
		$("#backButton").button({icons: {primary:"ui-icon-circle-triangle-w"}});
		$("#songBpmButton").button({icons: {primary:"ui-icon-circle-triangle-s"}, label: "Hide Song BPM"});

		$("#artist").focus();

		$("#inputChanged").val("false");

		$("#artist").change(function() {
  			$("#inputChanged").val("true");
		});

		$("#songTitle").change(function() {
  			$("#inputChanged").val("true");
		});

		$("#recordLabel").change(function() {
  			$("#inputChanged").val("true");
		});

		$("#songFormat").change(function() {
  			$("#inputChanged").val("true");
		});

		var iframe = $('<iframe frameborder="0" marginwidth="0" marginheight="0" allowfullscreen></iframe>');
    var dialog = $("<div></div>").append(iframe).appendTo("body").dialog({
        autoOpen: false,
        modal: true,
        resizable: false,
        width: "auto",
        height: "auto",
        close: function () {
            iframe.attr("src", "");
        }
    });
    $(".thumb a").on("click", function (e) {
        e.preventDefault();
        var src = $(this).attr("href");
        var title = $(this).attr("data-title");
        var width = $(this).attr("data-width");
        var height = $(this).attr("data-height");
        iframe.attr({
            width: +width,
            height: +height,
            src: src
        });
        dialog.dialog("option", "title", title).dialog("open");
    });

	});
</script>
</head>

<body>
<?php

	function createHours($id='hours_select', $selected=null) {
        /*** range of hours ***/
        $r = range(0, 12);

        /*** current hour ***/
        $selected = is_null($selected) ? date('h') : $selected;

        $select = "<select name=\"$id\" id=\"$id\">\n";
        foreach ($r as $hour)
        {
        	$paddedHour = str_pad($hour, 2, '0', STR_PAD_LEFT);
            $select .= "<option value=\"$paddedHour\"";
            $select .= ($hour==$selected) ? ' selected="selected"' : '';
            $select .= ">".$paddedHour."</option>\n";
        }
        $select .= '</select>';
        return $select;
    }

    function createMMSS($id='minute_select', $selected=null) {
        /*** array of mins ***/
        $mmss_range = range(0, 59);

   		  $selected = in_array($selected, $mmss_range) ? $selected : 0;

        $select = "<select name=\"$id\" id=\"$id\">\n";
        foreach($mmss_range as $mm_ss) {
        	$paddedMMSS = str_pad($mm_ss, 2, '0', STR_PAD_LEFT);
            $select .= "<option value=\"$paddedMMSS\"";
            $select .= ($mm_ss==$selected) ? ' selected="selected"' : '';
            $select .= ">".$paddedMMSS."</option>\n";
        }
        $select .= '</select>';
        return $select;
    }

    function formatDuration($hh, $mm, $ss) {
    	$hh = createHours('duration_hh', $hh) . ':' ;
  		$mm = createMMSS('duration_mm', $mm) . ':';
	  	$ss = createMMSS('duration_ss', $ss);
	  	return $hh . $mm . $ss;
    }

    function displayMessage($title, $message) {
    	echo "<script>$(document).ready(function() {var dialogButtons = dialogBox.dialog(\"option\", \"buttons\");
			$.extend(dialogButtons, {
				Ok: function() {
					$(\"#searchDialog\").dialog(\"close\");
				},
			});
			dialogBox.dialog(\"option\", \"buttons\", dialogButtons);

			// Hide the Cancel button as we only need an Ok button for this alert dialog
			$(\".ui-dialog-buttonpane button:contains('Cancel')\").button().hide();

			$(\"#searchDialog\").dialog('option', 'title', '" . $title . "');
			$(\"#searchFieldLabel\").html('" . addslashes($message) . "');
			$(\"#searchDialog\").dialog(\"open\"); });</script>";
    }

    function setSide($sideValue) {
		if ($sideValue == "A Side") {
			$sideNum = 1;
		} else if ($sideValue == "B Side") {
			$sideNum = 2;
		} else if ($sideValue == "AA Side") {
			$sideNum = 3;
		}
		return $sideNum;
    }

	$config = require 'config.php';
	require_once 'dbutils.php';
	require_once 'general_utils.php';

	$db            = new DbUtils;
	$db->countTotals("", "", "", "");

	$crudType      = (empty($_GET['crudOp'])) ? $_POST['crudOp'] : $_GET['crudOp'];
	$sortField     = (empty($_GET['sortField'])) ? $_POST['sortField'] : $_GET['sortField'];
	$crudSubmit    = $_GET['crudSubmit'];
	$buttonPressed = urldecode($_POST['buttonPressed']);
	$currentPage   = urldecode($_POST['currentPage']);
 	$itemsPerPage  = urldecode($_POST['itemsPerPage']);
	$searchUrl     = urldecode($_POST['searchUrl']);

	$displayCrateHref = urldecode($_POST['displayCrateHref']);
   	if (empty($displayCrateHref)) {
	   	$displayCrateHref = '/php/displaycrate.php?page=';
	   	if (empty($currentPage)) {
	   		$displayCrateHref = $displayCrateHref . '1';
	   	} else {
	   		$displayCrateHref = $displayCrateHref . $currentPage;
	   	}

	   	if (!empty($itemsPerPage)) {
	   		$displayCrateHref = $displayCrateHref . '&ipp=' . $itemsPerPage;
	   	}

	   	if (!empty($sortField)) {
	   		$displayCrateHref = $displayCrateHref . '&sortField=' . $sortField;
	   	}

	  	if (!empty($searchUrl)) {
	   		$displayCrateHref = $searchUrl;
	   	}
  }

	if ($crudType == "I") {
		$crudHeader        = 'Add a funky new Song';
		$formattedDuration = formatDuration(0, 0, 0);
		$bpm               = 0;
		$numberOfCopies    = 1;

		// Set the selected value for the drop down 'songFormat' field
		$selectedSongFormat = getCurrentSongFormat($db);
		$songFormatNum = setSongFormat($selectedSongFormat);
	} else {
		$songRecord = urldecode($_POST['amendedSongRecord']);
	   	$deleteOk   = urldecode($_POST['deleteOk']);

		list($songId, $artist, $songTitle, $recordLabel, $year, $numberOfCopies, $duration, $side, $songFormat, $genre, $bpm) =
    		split("::", $songRecord, 11);

    	$songFormatNum = setSongFormat($songFormat);

		if (! is_null($duration) && ! empty($duration)) {
			list($duration_hh, $duration_mm, $duration_ss) = split(":", $duration, 3);
			$formattedDuration = formatDuration($duration_hh, $duration_mm, $duration_ss);
		} else {
			$formattedDuration = formatDuration(0, 0, 0);
		}

		if ($buttonPressed == 'editButton') {
			$crudHeader = 'Amend a Song';
			$previousRecordLabel = $recordLabel;
		}

		if ($buttonPressed == 'deleteButton') {
			if ($deleteOk == 'true') {
				$crudHeader = 'Delete a Song';

				$sql = "delete from crate where songId = :songId";

				try {
					$stmt = $db->dbConnection->prepare($sql);
					$stmt->execute(array(':songId'=>$songId));
				}
				catch (PDOException $e) {
			   		die("Insert failure: " . $e->getMessage());
				}
			}
			// Redirect back to the display crate php
			header('Location: ' . $displayCrateHref);
		}
	}

	// Set the selected value for the drop down 'side' field
	if (! is_null($side) && ! empty($side)) {
		$sideNum = setSide($side);
	}

	$saveDetails = urldecode($_POST['saveDetails']);

	if (! is_null($saveDetails) && ! empty($saveDetails) && $saveDetails == 'saveDetails') {
		try {

			$songId         = urldecode($_POST['songId']);
			$artist         = urldecode($_POST['artist']);
			$songTitle      = urldecode($_POST['songTitle']);
			$recordLabel    = urldecode($_POST['recordLabel']);
			$year           = urldecode($_POST['year']);
			$numberOfCopies = urldecode($_POST['numberOfCopies']);
			$duration_hh    = urldecode($_POST['duration_hh']);
			$duration_mm    = urldecode($_POST['duration_mm']);
			$duration_ss    = urldecode($_POST['duration_ss']);
			$side 		      = urldecode($_POST['side']);
			$songFormat     = urldecode($_POST['songFormat']);
			$genre          = urldecode($_POST['genre']);
			$bpm            = urldecode($_POST['bpm']);
			$inputChanged   = urldecode($_POST['inputChanged']);

			$duration = $duration_hh . ':' . $duration_mm . ':' . $duration_ss;

			// Insert a new record but first check if it has already been inserted
			if ($crudType == "I") {
				$recordCount = $db->checkDuplicateRecord($artist, $songTitle, $recordLabel, $songFormat);

				if ($recordCount > 0) {
					displayMessage("Warning", '<b>' . $artist . ' - ' . $songTitle . ' - ' . $recordLabel . "</b> has already been added!");
				} else {
					$sql = "INSERT INTO crate (artist, songTitle, recordLabel, year, numberOfCopies, duration, side, songFormat, genre, bpm) " .
					       "VALUES (:artist, :songTitle, :recordLabel, :year, :numberOfCopies, :duration, :side, :songFormat, :genre, :bpm)";

					try {
						$stmt = $db->dbConnection->prepare($sql);
						$stmt->execute(array(':artist'=>addslashes($artist), ':songTitle'=>addslashes($songTitle), ':recordLabel'=>addslashes($recordLabel), ':year'=>$year,
							':numberOfCopies'=>$numberOfCopies, ':duration'=>$duration, ':side'=>$side, ':songFormat'=>$songFormat, ':genre'=>addslashes($genre), ':bpm'=>$bpm));

						// Redirect back to the form to re-enter more data
						header('Location: /php/crud.php?crudOp=I');
					}
					catch (PDOException $e) {
				   		die("Insert failure: " . $e->getMessage());
					}
				}
			} else {
				// Update an existing record first check if it already exists if the artist, song title or record label has been changed
				$crudHeader = 'Amend a Song';

				$recordCount = ($inputChanged == "false") ? 0 : $db->checkDuplicateRecord($artist, $songTitle, $recordLabel, $songFormat);

				if ($recordCount > 0) {
					displayMessage("Warning", '<b>' . $artist . ' - ' . $songTitle . ' - ' . $recordLabel . "</b> has already been added!");
					$recordLabel = urldecode($_POST['previousRecordLabel']);
				} else {
					$sql = "update crate ".
	                       "set artist         = :artist,         " .
	                       " 	songTitle      = :songTitle,      " .
	                       " 	recordLabel    = :recordLabel,    " .
	                       "    year           = :year,           " .
	                       "    numberOfCopies = :numberOfCopies, " .
	                       "    duration       = :duration,       " .
	                       "    side		   = :side,           " .
	                       "    songFormat     = :songFormat,     " .
	                       "    genre          = :genre,          " .
	                       "    bpm            = :bpm             " .
	                       "where songId       = :songId";

					try {
						$stmt = $db->dbConnection->prepare($sql);
						$stmt->execute(array(':artist'=>addslashes($artist), ':songTitle'=>addslashes($songTitle), ':recordLabel'=>addslashes($recordLabel), ':year'=>$year,
							':numberOfCopies'=>$numberOfCopies, ':duration'=>$duration, ':side'=>$side, ':songFormat'=>$songFormat, ':genre'=>addslashes($genre), ':bpm'=>$bpm, ':songId'=>$songId));

						displayMessage("Updated!", "Details successfully updated");

						$formattedDuration = formatDuration($duration_hh, $duration_mm, $duration_ss);
					}
					catch (PDOException $e) {
				   		die("Update failure: " . $e->getMessage());
					}
				}
			}
		} catch (PDOException $pe) {
		    die("Could not connect to the database $dbname :" . $pe->getMessage());
		}

		// Reset the Song Format and Side drop down in case the values have been amended
		if (is_null($songFormat) || empty($songFormat)) {
			$selectedSongFormat = getCurrentSongFormat($db);
			$songFormatNum = setSongFormat($selectedSongFormat);
		} else {
			$songFormatNum = setSongFormat($songFormat);
		}

		// Set the selected value for the drop down 'side' field
		if (! is_null($side) && ! empty($side)) {
			$sideNum = setSide($side);
		}
	}

?>

<!-- Enter and validate details to be persisted into a form -->
<div id="container">

	<div class="rowStyle">
		<div id="crudMainHeader" class="crudMainHeader"><h1><?php echo $crudHeader ?></h1></div>
		<div class="crudMainDate"> <?php echo date("D j M, Y"); ?> </div>
		<div class="crudTotalRecords">  Total Records in Crate: <?php echo $db->totalASides + $db->totalAASides; ?> </div>
		<div class="crudASides">  <?php echo $db->totalASides;  ?> A Sides </div>
		<div class="crudBSides">  <?php echo $db->totalBSides;  ?> B Sides </div>
		<div class="crudAAsides"> <?php echo $db->totalAASides; ?> AA Sides </div>
	</div>


	<div id="searchDialog">
		<label id="searchFieldLabel"></label>
	</div>

	<form id="crudForm" name="crudForm" method="post" action="/php/crud.php?crudOp=<?php echo $crudType ?>">
		<div class="rowStyle">
			<div class="crudHeader">Artist</div>
			<div class="crudField"><input type="text" id="artist" name="artist" size="35" maxlength="100" value="<?php echo $artist ?>"/></div>
			<div id="artistError">
				<label id="artistErrorLabel"></label>
			</div>
		</div>

		<div class="rowStyle">
			<div class="crudHeader">Song Title</div>
			<div class="crudField"><input type='text' id='songTitle' name='songTitle' size="35" maxlength="100" value="<?php echo $songTitle ?>"/></div>
		</div>

		<div class="rowStyle">
			<div class="crudHeader">Record Label</div>
			<div class="crudField"><input type='text' id='recordLabel' name='recordLabel' size="35" maxlength="100" value="<?php echo $recordLabel ?>"/></div>
		</div>

		<div class="rowStyle">
			<div class="crudHeader">Year</div>
			<div class="crudField"><input type='text' id='year' name='year' size="8" maxlength="4" value="<?php echo $year ?>"/></div>
		</div>

		<div class="rowStyle">
			<div class="crudHeader">Genre</div>
			<div class="crudField"><input type='text' id='genre' name='genre' size="35" maxlength="100" value="<?php echo $genre ?>"/></div>
		</div>

		<div class="rowStyle">
			<div class="crudHeader">Number Of Copies</div>
			<div class="crudField"><input type='text' id='numberOfCopies' name='numberOfCopies' size="8" maxlength="2" value="<?php echo $numberOfCopies ?>"/></div>
		</div>

		<div class="rowStyle">
			<div class="crudHeader">Duration (hh:mm:ss)</div>
			<div id="duration" class="crudField"><?php echo $formattedDuration; ?></div>
		</div>

		<div class="rowStyle">
			<div class="crudHeader">Side</div>
			<div class="crudField">
				<select name='side'>
					<option value="A Side"  <?php if ($sideNum == 1) echo 'selected'; ?> >A Side</option>
					<option value="B Side"  <?php if ($sideNum == 2) echo 'selected'; ?> >B Side</option>
					<option value="AA Side" <?php if ($sideNum == 3) echo 'selected'; ?> >AA Side</option>
				</select>
			</div>
		</div>

		<div class="rowStyle">
			<div class="crudHeader">Song Format</div>
			<div class="crudField">
				<select name='songFormat'>
					<option value="7 inch"    <?php if ($songFormatNum == 1) echo 'selected'; ?> >7 inch</option>
					<option value="10 inch"   <?php if ($songFormatNum == 2) echo 'selected'; ?> >10 inch</option>
					<option value="12 inch"   <?php if ($songFormatNum == 3) echo 'selected'; ?> >12 inch</option>
					<option value="LP"        <?php if ($songFormatNum == 4) echo 'selected'; ?> >LP</option>
					<option value="CD"        <?php if ($songFormatNum == 5) echo 'selected'; ?> >CD</option>
					<option value="CD Single" <?php if ($songFormatNum == 6) echo 'selected'; ?> >CD Single</option>
					<option value="MP3"       <?php if ($songFormatNum == 7) echo 'selected'; ?> >MP3</option>
				</select>
			</div>
		</div>

		<div class="rowStyle">
			<div class="crudHeader">BPM</div>
			<div class="crudField"><input type='text' id='bpm' name='bpm' size="8" maxlength="4" value="<?php echo $bpm ?>"/></div>
		</div>

		<br>

		<div class="rowStyle">
			<div class="saveCrudButton"><button id="submitButton">Save</button></div>
			<div class="backCrudButton"><button id="backButton"  >Back</button></div>
			<div class="thumb">
			    <a href="http://songbpm.com"   data-title="Std 4:3 ratio video" data-width="512" data-height="384"><img src="http://dummyimage.com/120x90/000/f00&text=Std+4-3+ratio+video" /></a></li>
			    <a href="http://www.discogs.com" data-title="HD 16:9 ratio video" data-width="512" data-height="288"><img src="http://dummyimage.com/120x90/000/f00&text=HD+16-9+ratio+video" /></a></li>
			</div>
		</div>

		<input type="hidden" name="songId" value='<?php echo $songId ?>'/>
		<input type="hidden" id="displayCrateHref" name="displayCrateHref" value="<?php echo $displayCrateHref ?>"/>
		<input type="hidden" id="currentPage" name="currentPage" value="<?php echo $currentPage; ?>"/>
		<input type="hidden" id="itemsPerPage" name="itemsPerPage" value="<?php echo $itemsPerPage; ?>"/>
		<input type="hidden" id="sortField" name="sortField" value="<?php echo $sortField; ?>"/>
		<input type="hidden" id="crudOp" name="crudOp" value="<?php echo $crudType; ?>"/>
		<input type="hidden" id="previousRecordLabel" name="previousRecordLabel" value="<?php echo $previousRecordLabel; ?>"/>
		<input type="hidden" id="inputChanged" name="inputChanged"/>
		<input type="hidden" id="saveDetails" name="saveDetails"/>
	</form>

</div>
</body>
</html>
