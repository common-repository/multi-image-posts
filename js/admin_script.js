$ = jQuery.noConflict();
$(document).ready(function () {

	var media;var k=0; var j=0; var m=0;var i=0;
	$('.mip_image_button').click(function() {
		 media = wp.media({
			title: 'Choose or Upload an Image',
            button: { text:  'Use this image' },
            library: { type: 'image' },
            multiple: true
			});
						 
   			media.on('select', function(e) {
				
				 var myTemplate = '<form method="post">';
				 var selection = media.state().get('selection');
				 var number_of_selected_images = selection.length;
					selection.map( function( attachment ) {
						
						attachment = attachment.toJSON();
				
				//added template into preview div
				myTemplate +="<table><tr><td rowspan='9'><img src='"+attachment.url+"' id='blah-"+k+"' width='250' height='300' /></td><td colspan='2'>&nbsp;</td><td></td></tr><tr><td colspan='8' rowspan='8'>&nbsp;</td><td><strong>File Name:</strong></td><td colspan='8' rowspan='8'>&nbsp;</td><td><input id='url-"+k+"' class='url mip_input' name='url[]' type='text' disabled='disabled' value='"+attachment.name+"' /><input id='url_id-"+k+"' name='url_id[]' type='hidden' value='"+attachment.id+"' /></td></tr><tr><td><strong>Image Title:</strong></td><td><input id='title-"+k+"' class='mip_input' name='title[]' type='text' value='"+attachment.title+"' required='required'/></td></tr><tr><td><strong>Alt Text:</strong></td><td><input id='alt_text-"+k+"' class='mip_input' name='alt_text[]' type='text' value='"+attachment.alt+"' required='required'/></td></tr><tr><td><strong>Post Title:</strong></td><td><input id='post_title-"+k+"' class='mip_input' name='post_title[]' type='text' value='"+attachment.title+"' required='required'/></td></tr><tr><td><strong>Caption:</strong></td><td><input id='caption-"+k+"' class='mip_input' name='caption[]' type='text' value='"+attachment.caption+"' required='required'/></td></tr><tr><td><strong>Tags:</strong></td><td><input id='tag-"+k+"' class='mip_input' name='tags[]' type='text' placeholder='Enter comma(,) separated tags here' /></td></tr><tr><td><strong>Categories:</strong></td><td>"+mip_getcategories( k )+"</td></tr></table><br><br>";
				//End template into preview div
				
					k++;
					i++;
					});
					myTemplate +="<input id='no_of_imgs' name='no_of_imgs' type='hidden' value="+k+" />";
			myTemplate +='<br><input type="submit" class="button-primary" value="Submit" name="submit" /></form>';
				    $('#guide').hide();
					$('#preview').html(myTemplate);
					 
		});
								   
			
	media.open();
	 });
	//start Function for categories 
	function mip_getcategories( n ){
			var cList = $('#mip_CategoreisList').val();
			var str_array = cList.split(',');
			var cats_template = '';
			cats_template += "<table>";
			for( i = 0; i < str_array.length; i++) {
				j++;
				if(j==m+3){ cats_template +="<tr>";
				 m+=3;}
				// Trim the excess whitespace.
				str_array[i] = str_array[i].replace(/^\s*/, "").replace(/\s*$/, "");
				
				cats_template += "<td><input type='checkbox' name='categories["+n+"][]' id='categories' value='"+str_array[i]+"'>"+str_array[i]+"</td>";
			if(j==j+2){ cats_template +="</tr>";}
			}
			
			cats_template += "</table>";
			return cats_template;
	}
	//End Function
}); 