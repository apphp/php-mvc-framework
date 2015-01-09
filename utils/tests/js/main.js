function formSubmit(operation, sub_type){
    var project_ = document.getElementById("sel_project").value;
    var action_ = (document.getElementById("sel_action")) ? document.getElementById("sel_action").value : '';
    var operation_ = (operation != null) ? operation : '';
    var sub_type_ = (sub_type != null)? sub_type : '';
    if(sub_type_ == "project") action_ = operation_ = "";
    
    window.location.href="index.php?project="+project_+"&action="+action_+'&operation='+operation_;    
}