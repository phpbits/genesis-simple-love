/**
*
* Genesis Simple Love Plugin
*
*/

// Utility
if ( typeof Object.create !== 'function' ) {
	Object.create = function( obj ) {
		function F() {};
		F.prototype = obj;
		return new F();
	};
}

(function( $, window, document, undefined ) {	
	$('.share-filled, .share-outlined').append( function(){
		love_html = simple_love.fe;
		fix = $(this).parent('.entry-content').find('.simple-love-fix');
		if(fix.length > 0){
			love_txt = simple_love.fe.replace('<!--__love__-->', fix.val());
			love_html = love_txt.replace('--__id__--', fix.attr('data-id'));
			love_html = love_html.replace('"--__id__--"', '"' + fix.attr('data-id') + '"');
			// console.log(love_html);
		}
		return love_html;
	} );
	$('.share-filled .simple-love, .share-outlined .simple-love').on('click',function(e){
		var post_id = parseInt( $(this).attr('data-id') );
		var get_this = $(this).attr('id'); 
		if( post_id > 0){
			jQuery.ajax({
				type : "post",
				dataType : "json",
				url : simple_love.ajaxurl,
				data : {action: "genesis_simple_love", post_id : post_id, nonce: simple_love.nonce},
				success: function(response){
					if(response.type == 'success'){
						$('#'+ get_this + ' .count').html( response.count );
					}
					alert(response.message);
					
				}
 			});
		}
		e.preventDefault();
		e.stopPropagation();
	});
})( jQuery, window, document );
