function ActivateOUWooPlugin( id, action, nonce ) {
  var key = jQuery( '#' + id).val();   
  var data = {
    "action"      : "ouwoo_" + action + "_plugin",
    "license_key" : key,
    "security"    : nonce
 };
 
 jQuery('#actplug').css('visibility', 'inherit');
 
 jQuery.post(ajaxurl, data, function( response ) {
    jQuery('#actplug').removeAttr('style');
    if( response != '200' ) {
      jQuery('.ouwoo-response').addClass('error').text(response);

      if( action == 'deactivate') {
      	jQuery('#btn-' + action + '-license').hide();
      	jQuery('#btn-activate-license').removeAttr('style');
      	jQuery('#ouwoo_license_key').val('');
      }
    }else {
      jQuery('#btn-' + action + '-license').hide();
      jQuery('.ouwoo-response').text('');
      jQuery('.ouwoo-response').removeClass('error');
      if( action == 'reactivate' ) {
        jQuery('td .update-nag').hide();
      }

      if( action == 'activate') {
      	jQuery('#btn-deactivate-license').removeAttr('style');
      }
    }   
    
    jQuery('.ouwoo-response').show();
 });
}

function activateComponents() {
	var data = {
			"action" 	: "ouwoo_active_components",
			"modules" 	: jQuery('input[name="active_components"]').val(),
			"security" 	: jQuery('input[name="ouwoo_nonce"]').val()
	};

	jQuery('.div-button .spinner').css('visibility', 'visible');
	jQuery('.ouwoo-comp-notice').css('display', 'none');

	jQuery.post(ajaxurl, data, function( response ) {
		jQuery('.div-button .spinner').removeAttr('style');
		if( response == 200 || response == '200' )
			jQuery('.ouwoo-comp-notice').css('display', 'block');
	});
}

(function($){
	$(function(){
		$(".section-cb").click(function(){
			var parent = $(this).closest('.ouwoo-acrd-item');
			parent.find('input:checkbox').not(this).prop('checked', this.checked).trigger('change');
		});

		Array.prototype.remove = function(x) { 
			var i;
			for(i in this){
				if(this[i].toString() == x.toString()){
					this.splice(i,1)
				}
			}
		};

		$('.check-column').on('change', function() {
			var comps = $('input[name="active_components"]').val(),
			active_components = comps.split(","),
			ckb = $(this);
			val =  ckb.val();

			if( ckb.prop("checked") ) {
				active_components.push( val );
			} 

			if( ! ckb.prop("checked") ) {
				active_components.remove( val );
			}

			$('input[name="active_components"]').val(active_components.join(",").toString());
	    });
	});
})(jQuery);