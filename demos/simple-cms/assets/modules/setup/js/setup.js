jQuery(document).ready(function(){
	// Toggle password fields
	jQuery(".toggle_password").on("click", function(){
		var field = jQuery(this).data("field");
		if(jQuery("#"+field).prop("type") == "password"){
			jQuery("#"+field).prop("type", "text");
		}else{
			jQuery("#"+field).prop("type", "password");
		}
		jQuery(this).toggleClass("toggle_password_closed");
	});
	
	// Reload page
	jQuery('select[name="dbConnectType"]').on("change", function(){
		jQuery(this).closest("form").submit();
	});
});	