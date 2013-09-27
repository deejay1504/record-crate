var deleteOk;
var warningImage = '<img id="warningImage" src="/images/warning.png"/>';

$(document).ready(function() {
	blink = function blink(selector) {
		$(selector).fadeOut('slow', function(){
			$(this).fadeIn('slow', function(){
				blink(this);
			});
		});
	}
	
	
	dialogBox = 
	$("#searchDialog").dialog({
	    autoOpen: false,
	    modal: true,
	    width: 350,
	    buttons: { 
        	Ok: function() {
        		// Override this to add the functionality
        	},
        	Cancel: function () {
            	$(this).dialog("close");
        	}
    	}
	});
	
    confirmDialogBox = 
    	$("#confirmDialog").dialog({
    		autoOpen: false,
    		modal: true, 
    		width: 'auto', 
    		resizable: false,
    		buttons: {
    			Yes: function () {
    				// Override this to add the functionality
    			},
    			No: function () {
    				$(this).dialog("close");
    			}
    		},
    		open: function() {
    			// Set the focus on the 'No' button
    			$(this).closest('.ui-dialog').find('.ui-dialog-buttonpane button:eq(1)').focus(); 
    		}
    	});
		
	$("#searchDialog").keydown(function(event) {
	 	// Click the 'OK' dialog button if return is pressed
        if (event.keyCode == 13) {
            $(this).parent().find("button:eq(1)").trigger("click");
            return false;
        }
    });
	

});