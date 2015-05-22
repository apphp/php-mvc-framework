var docs_menu = new Array(
    'group-utils',
    'group-framework-structure',
    'group-application-development',
    'group-special-topics',
    'group-working-with-forms',
    'group-application-modules'
);	
    
function getValue(param){
    // First, we load the URL into a variable
    var url = window.location.href;      
    // Next, split the url by the ?
    var qparts = url.split("?");      
    // Check that there is a querystring, return "" if not
    if(qparts.length == 0) return '';              
    // Then find the querystring, everything after the ?
    var query = (qparts[1] != null) ? qparts[1] : '';      
    // Split the query string into variables (separates by &s)
    var vars = query.split("&");
  
    // Iterate through vars, checking each one for varname
    for (i=0;i<vars.length;i++){
        // Split the variable by =, which splits name and value
        var parts = vars[i].split("=");          
        // Check if the correct variable
        if(parts[0] == param){
            return parts[1];
        }
    }
    return '';
}	

function toggleGroup(el_d){
    var el = document.getElementById(el_d);
    if(el){
        if(el.style.display == 'none'){
            el.style.display = '';
            setCookie(el_d, 'opened', 1);
        }else{
            el.style.display = 'none';
            setCookie(el_d, 'closed', 1);
        }			
    }
}

function setCookie(c_name, value, exdays)
{
    var exdate=new Date();
    exdate.setDate(exdate.getDate() + exdays);
    var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
    document.cookie=c_name + "=" + c_value;
}	

function getCookie(c_name){
    var c_value = document.cookie;
    var c_start = c_value.indexOf(" " + c_name + "=");
    if(c_start == -1){
        c_start = c_value.indexOf(c_name + "=");
    }
    if(c_start == -1){
        c_value = null;
    }else{
        c_start = c_value.indexOf("=", c_start) + 1;
        var c_end = c_value.indexOf(";", c_start);
        if (c_end == -1){
            c_end = c_value.length;
        }
        c_value = unescape(c_value.substring(c_start,c_end));
    }
    return c_value;
}

jQuery(function($){
    // scroll
    $(window).scroll(function(){
		var scrollTop = $(this).scrollTop();

		if(scrollTop > 90){
			$('header').css({"opacity":"0.95"});
		}else{
			$('header').css({"opacity":"1"});
		}

        if(scrollTop > 200){
            $('.scrollup').fadeIn();    
        }else{
            $('.scrollup').fadeOut();   
        }        
    });     

    $('.scrollup').click(function(){
        $("html, body").animate({ scrollTop: 0 }, 200);
        return false;
    });
})