function formSubmit(operation, sub_type){
    var generation_types_ = document.getElementById("sel_generation_types").value;
    var operation_ = (operation != null) ? operation : '';
    var sub_type_ = (sub_type != null)? sub_type : '';
    if(sub_type_ == "generation_type") action_ = operation_ = "";
    
    window.location.href="index.php?generation_type="+generation_types_+'&operation='+operation_;    
}

function selectCode(el, msg){
    if(document.getElementById(el)) document.getElementById(el).select();
    document.getElementById(msg).style.display = "none";
    document.getElementById(msg).innerHTML = "";
}

function setFocus(el){
    if(document.getElementById(el)) document.getElementById(el).focus();
}

function copyToClipboard(el, msg) {
    document.getElementById(el).select();
    document.execCommand("copy");
    document.getElementById(msg).style.display = "block";
    document.getElementById(msg).innerHTML = "Copied!";
}
