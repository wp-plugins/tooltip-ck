// window.addEvent('domready', function() {

    // jQueryjQuery('.radioClass').each(function(el2){el2.setStyle('opacity','0'); });
    
    // var monimage = jQueryjQuery('.boutonRadio');
    // monimage.each(function(el) {
        // el.addEvent('click',function(){
			// var boutoncontainer = el.getParent(); 
            // boutoncontainer.getElements('.boutonRadio').removeClass('coche');
            // el.addClass('coche');
            // var moninput = el.getFirst();
            

            // var identifier = el.getProperty('identifier');
            // if (jQuery(identifier)) jQuery(identifier).value = moninput.value;
            // boutoncontainer.getElements('.radioClass').each(function(el2){
				// el2.removeAttribute("checked","checked"); 
			// });
                // moninput.setAttribute("checked","checked");
            // });
        // });
    // });

function makeckradio2js() {
	jQuery('.radioClass').css('opacity','0');
	jQuery('.boutonRadio').each(function(i, el) {
		el = jQuery(el);
		el.click(function(){
			var boutoncontainer = el.parent(); 
			jQuery('.boutonRadio', boutoncontainer).removeClass('coche');
			el.addClass('coche');
			var moninput = jQuery('input', el);
			// var identifier = el.attr('identifier');
			jQuery('input[type=hidden]', boutoncontainer).val(moninput.val());
			jQuery('.radioClass',boutoncontainer).each(function(j, el2){
				jQuery(el2).removeAttr("checked","checked"); 
			});
			moninput.attr("checked","checked");
		});
	});
}

// to be called by external fonction to set the value on the field
function initckradiojs(fieldname) {
	var field = jQuery('#'+fieldname);
	var boutoncontainer = field.parents('fieldset')[0];
	jQuery(boutoncontainer).find('input[type=radio]').each(function(i, radio) {
		moninput = jQuery(radio);
		if (moninput.val() == field.val()) {
			moninput.parent().addClass('coche');
			moninput.attr("checked","checked");
		}
	});
}

// autocreate the radio buttons on page load
jQuery(document).ready( function () {
	makeckradio2js();
});