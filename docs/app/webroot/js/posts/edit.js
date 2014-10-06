$(function(){
	$(".categories").multiautocomplete({formField: "#categories"});
	
	tinyMCE.init({
        mode : "textareas",
        theme : "advanced",
        plugins : "emotions,spellchecker,advhr,insertdatetime,preview,save", 
                
        // Theme options - button# indicated the row# only
        theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,|,justifyleft,justifycenter,justifyright,fontselect,fontsizeselect,formatselect",
        theme_advanced_buttons2 : "cut,copy,paste,|,bullist,numlist,|,outdent,indent,|,undo,redo,|,link,unlink,anchor,image,|,code,preview,|,forecolor,backcolor",
        theme_advanced_buttons3 : "insertdate,inserttime,|,spellchecker,advhr,,removeformat,|,sub,sup,|,charmap,emotions",      
        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",
        theme_advanced_resizing : true,
        save_enablewhendirty: true,
  	  	save_onsavecallback: function() { ajaxSave();}
	});
	

});


function ajaxSave() {
    var post = {
    		url :	"/posts/ajaxsavedraft",
    		id	:	document.getElementById("PostId").value,
    		body	: tinyMCE.get('PostBody').getContent()
    };
    
    
    // Post to save draft of body
   	$.ajax({
   		url		:	post.url,
   		data	:	{entry_id : post.id, content : post.body},
   		dataType:	"json",
   		type	:	"post",
   		success	:	function(data){
   						$("#SaveBodyDraftStatus").html("Saved Draft");
   	  }});    
    
}

/* Calculate the position of the Lightbox Showing List of Drafts */
function positionLightboxImage() {
	
	// Get the users screen height and width, 
	// then get the height and width of the lightbox (defined in css), 
	// and subtract half of lightbox height of width / 2, to get the middle of the screen from the top and the left.
	var top = ($(window).height() - $('#lightbox').height()) / 2;
	var left = ($(window).width() - $('#lightbox').width()) / 2;
	  
	// Position the lightbox and fade it in.
	// The overflow-y scrollbar (auto) is not pretty with the rounded borders, but it needs to be there for now.
	$('#lightbox')
	    .css({
	      'top': top + $(document).scrollTop(),
	      'left': left,
	      'overflow-y': 'auto',
		  'overflow-x': 'auto'
	    }).fadeIn();
}


// Remove both the opaque background and the lightbox on top of it, with its entire contents 
function removeLightbox() {
  $('#overlay, #lightbox')
    .fadeOut('slow', function() {
      $(this).remove();
      $('body').css('overflow-y', 'auto'); // show scrollbars!
    });
}


/* An AJAX call is made to retrieve the most recent drafts saved of this entries content. It only retrieves the Bodies content */
function getDrafts()
{
	// Set up the post url to save a draft of this content, and the id of this post
	 var post = {
	    		url :	"/posts/getdrafts",
	    		id	:	document.getElementById("PostId").value	    		
	    };
	 var draftsDisplay = "<ul>"; //Start of the list of drafts
	 var closeDrafts = '<div class="closeLightBox">X</div>'; // Will allow us to close the lightbox
	 var newHeight = 0; // The lightbox height will be calculated based on the number of drafts returned, to make the lightbox expand accordingly.
	 var maxHeight = $(window).height(); // We really don't want the lightbox to expand past the bottom of the screen
	 
	 
	 /* Retrieve the last few posts */
	 $.ajax({
			type: 'POST',
	  		url: post.url,
	  		dataType:	"json",
	  		beforeSend:	function()
	  		  		 {
						//$('#lightbox').html("Please wait. Saving....<img src='/admin/images/ajax-loader.gif' border='0'>");
				      },
	  		
	  		data: {id : post.id},
	  		success:	function( data ) 
	  			{	  				
	  				// List Date/Time and Length of each post and initally hide the content with a click event displaying it
		  			if(data.message.length > 3)
		  			{
		  				newHeight = (data.message.length * 50) + 68;
		  				
		  				// Prevent the lightbox from going past the screen
		  				if (newHeight > maxHeight) newHeight = maxHeight;
		  				
		  				newHeight = newHeight + "px";
		  				
		  				// Set the new height of the lightbox
		  				document.getElementById("lightbox").style.height = newHeight;
		  				
		  			}
		  			
		  			// Function to reposition the lightbox based on the new heights
		  			positionLightboxImage();
	  			
	  				
	  				// Loop through all the draft entries retrieved from our AJAX repsonse
	  				for (x in data.message)
	  				{
	  					// Build our list of draft entries, with the date created and the first 40 characters of the bodies text
	  					draftsDisplay +=  "<li draftId=\"" + 
	  						data.message[x].EntryDrafts.id + 
	  						"\">" + 
	  						data.message[x].EntryDrafts.created + 
	  						" <span class=\"draftRevert\">[Revert]</span> <span class=\"draftView\">[View]</span>" + 
	  						"<span class='draftContent'><br>" + 
	  						data.message[x].EntryDrafts.content.substr(0,40) + 
	  						"</span>" +
	  						"</li>";			
	  					
	  				}
	  				
	  				// Close our list of drafts
	  				draftsDisplay += "</ul>";
	  				
		  			// Use the close button and our list of drafts and set it as the HTML of the lightbox	  			
		  			$('#lightbox').html(closeDrafts + draftsDisplay);
		  			
		  			
		  			// When a link to view the draft is clicked, call the getDraft function to retrieve that version of the post 
		  			 $(".draftView").click(function(){
		  				// The draft id is contained in the parent <li> of each draft listed 
		  				var $draftId = $(this).parent().attr("draftId");
		  				
		  				// Call function that uses AJAX to retrieve this draft id from the server's database
		  				getDraft($draftId);
		  		    	
		  			 });
	  				
	  			},
	  		complete: function() 
	  				{
	  					// When the AJAX call is complete, all the useer to click the X 
	  					// and anywhere outside of the lightbox (#overlay) to close the lightbox
						$('.closeLightBox, #overlay').click(function() 
						{
							removeLightbox();
						});
	  				},
	  		error: function()
	  			{
	  				$('#lightbox').html(divClose + "An error has occurred.");
	  			}
	  		});
}

/* Retrieve this draft id's content */
function getDraft(id)
{
	var post = {
    		url :	"/posts/getdraft",
    		id	:	id	    		
    		};

 	var closeDrafts = "<div class=\"closeLightBox\">X</div>";
 	var backToDrafts = "<div id=\"backToDrafts\">Back To Drafts&gt;&gt;</div>";
 
	/* Retrieve the last few posts */
	 $.ajax({
			type: 'POST',
	  		url: post.url,
	  		dataType:	"json",
	  		beforeSend:	function()
	  		  		 {
						//$('#lightbox').html("Please wait. Saving....<img src='ajax-loader.gif' border='0'>");
				      },
	  		
	  		data: {id : post.id},
	  		success:	function( data ) 
	  			{	  				
	  				document.getElementById("lightbox").style.width = "640px";
	  				document.getElementById("lightbox").style.height = "480px";
	  				
	  				positionLightboxImage();	  				
	  				
	  				$('#lightbox').html( closeDrafts + backToDrafts + data.message[0].EntryDrafts.content);
	  				
	  				$("#backToDrafts").click(function(){
	  					 getDrafts();
	  				});
	  			},
	  		complete: function() 
	  				{
						$('.closeLightBox, #overlay').click(function() 
						{
							removeLightbox();
						});
	  				},
	  		error: function()
	  			{
	  				$('#lightbox').html(divClose + "An error has occurred.");
	  			}
	  		});
}

$(function(){

	/* View Drafts Overlay */
	$("#ViewDrafts").click(function(){
		
	 
	 	// Overlay Start
		$('body').css('overflow-y', 'hidden'); // hide scrollbars!
	    
	    $('<div id="overlay"></div>')
	      .css('top', $(document).scrollTop())
	      .css('opacity', '0')
	      .animate({'opacity': '0.5'}, 'slow')
	      .appendTo('body');
	      
	    $('<div id="lightbox"></div>')
	     .appendTo('body');
	   
	    positionLightboxImage();
	    // Overlay End
	    
	    /* Retrieve list of drafts */
	    getDrafts();		  
	});
	
	
});