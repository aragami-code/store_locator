(function($) {

// jQuery on an empty object, we are going to use this as our Queue
var ajaxQueue = $({});

$.ajaxQueue = function( ajaxOpts ) {
    var jqXHR,
        dfd = $.Deferred(),
        promise = dfd.promise();

    // run the actual query
    function doRequest( next ) {
        jqXHR = $.ajax( ajaxOpts );
        jqXHR.done( dfd.resolve )
            .fail( dfd.reject )
            .then( next, next );
    }

    // queue our ajax request
    ajaxQueue.queue( doRequest );

    // add the abort method
    promise.abort = function( statusText ) {

        // proxy abort to the jqXHR if it is active
        if ( jqXHR ) {
            return jqXHR.abort( statusText );
        }

        // if there wasn't already a jqXHR we need to remove from queue
        var queue = ajaxQueue.queue(),
            index = $.inArray( doRequest, queue );

        if ( index > -1 ) {
            queue.splice( index, 1 );
        }

        // and then reject the deferred
        dfd.rejectWith( ajaxOpts.context || ajaxOpts, [ promise, statusText, "" ] );
        return promise;
    };

    return promise;
};

})(jQuery);

jQuery( document ).ready( function( $ ) { 


$( "#MB-store-overview" ).on( "click", ".MB_qedit", function() { 
	var store_id = $(this).parent( ".quick_edit" ).find( "input[name='store_id']" ).val();
	$(".qes").hide();
	$(".qes2").show();
	$("#MB-wrrap"+store_id).hide();
	$("#MB-wrap"+store_id).show();
});

$( "#MB-store-overview" ).on( "click", ".cancel", function() {

	var store_id = $(this).parent( ".submit" ).find( "input[name='store_id']" ).val();
	$("#MB-wrrap"+store_id).show();
	$("#MB-wrap"+store_id).hide();
});

$( "#MB-store-overview" ).on( "click", ".save", function() {
	var sid = $(this).parent( ".submit" ).find( "input[name='store_id']" ).val();
	$("#MB-wrrap"+sid).show();
	$("#MB-wrap"+sid).hide();

		var ajaxData = {},

		ajaxData = {
		action: "quick_edit_store",
		store_id: $(this).parent( ".submit" ).find( "input[name='store_id']" ).val(),
		name: $(".s_name"+sid ).find( "input[name='store_name']" ).val(),
		country: $(".s_country"+sid ).find( "select[name='country']" ).val(),
		city: $(".s_city"+sid ).find( "input[name='city']" ).val(),
		state: $(".s_state"+sid ).find( "input[name='state']" ).val(),


		zip_code: $(".s_zip_code"+sid ).find( "input[name='zip_code']" ).val(),
			website: $(".s_website"+sid ).find( "input[name='website']" ).val(),
		address: $(".s_address"+sid ).find( "input[name='address']" ).val(),
		latitude: $(".s_latitude"+sid ).find( "input[name='latitude']" ).val(),
		longitude: $(".s_longitude"+sid ).find( "input[name='longitude']" ).val()
		};
		jQuery.ajaxQueue({
			url: ajaxurl,
			data: ajaxData,
			type: "POST"
		}).done( function( response ) { 
			if ( response === -1 ) { 
				alert( wpslL10n.securityFail );
			} else if ( response.success ) { 
				UpdateData(sid);

						
			}
		});	
});

function UpdateData(store_id)
{
	var ajaxData = {},

		ajaxData = {
		action: "quick_edit_store_view",
		store_id: store_id
		};
		$.ajax({
            url: ajaxurl,
            type: 'post',
            data: ajaxData,
            dataType: 'json',
            success: function(json) {
            if(json['status'] == 1)
            {
            	var status = 'Active';
            } else{ var status = 'Inactive';}

             $("#MB-wrrap"+store_id).hide().fadeIn('slow');
             $('#MB-wrrap'+store_id+' .store_name strong a').text(json['store_name']);  
             $('#MB-wrrap'+store_id+' .country').text(json['country']);  
             $('#MB-wrrap'+store_id+' .city').text(json['city']);  
             $('#MB-wrrap'+store_id+' .address').text(json['address']);  
            }
          }); 
}



$( "#MB-store-overview" ).on( "click", ".MB_del", function() {
	var data = $(this),
		dialogBox = $( "#MB-delete-confirmation" ),
		cancelBtn = dialogBox.find( ".button-secondary" ),
		submitBtn = dialogBox.find( ".button-primary" );

	dialogBox.dialog({
		width: 325,
		resizable : false,
		modal: true,
		minHeight: 0
	});
	
	$( ".ui-dialog-titlebar" ).remove();
	cancelBtn.on( "click", function() {	
		dialogBox.dialog( "close" ); 
		submitBtn.unbind( "click" );
        dialogBox.unbind( "click" );
	});
	
	submitBtn.on( "click", function() {	
		dialogBox.dialog( "close" );
		deleteStore( data ); 
		submitBtn.unbind( "click" );
        dialogBox.unbind( "click" );
		
        return false;
	});
	
	return false;
});



function deleteStore( data ) { 
	var ajaxData = {},
		$parentTr = data.parents( "tr" );

	
	ajaxData = {
		action: "delete_store",
		store_id: data.parent( ".delete" ).find( "input[name='store_id']" ).val(),
		_ajax_nonce: data.parent( ".delete" ).find( "input[name='delete_nonce']" ).val()
	};
	
	jQuery.ajaxQueue({
		url: ajaxurl,
		data: ajaxData,
		type: "POST"
	}).done( function( response ) { 
		if ( response === -1 ) { 
			alert( wpslL10n.securityFail );
		} else if ( response.success ) { 
			
			/* Remove the deleted store row */
			setTimeout( function() {
				$parentTr.fadeOut( "20", function() {
					$parentTr.remove();
					updateStoreCount();
				});
			}, 100);			
		}
	});	
}



function updateStoreCount() {
	var pageNum = $( ".tablenav.top .displaying-num" ).text(),
		pageNum = pageNum.split( " " );
	
	if ( !isNaN( parseInt( pageNum[0] ) ) ) {
		$( ".tablenav .displaying-num" ).text( pageNum[0]-1 + ' ' + pageNum[1] );
	}
}


                
});

var uploader;
function upload_image(id) {

  //Extend the wp.media object
  uploader = wp.media.frames.file_frame = wp.media({
    title: 'Choose Image',
    button: {
      text: 'Choose Image'
    },
    multiple: false
  });

  //When a file is selected, grab the URL and set it as the text field's value
  uploader.on('select', function() {
    attachment = uploader.state().get('selection').first().toJSON();
    var url = attachment['url'];
    jQuery('#'+id).val(url);
  });

  //Open the uploader dialog
  uploader.open();
}
