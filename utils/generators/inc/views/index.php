<?php

$act = isset($_POST['act']) ? $_POST['act'] : '';
$view_name = isset($_POST['view_name']) ? prepare_input($_POST['view_name']) : '';
$templateContent = '';
$msg = '';
$focusField = '';

if($act){
    if($view_name == ''){
        $msg = '<div class="msg_error">View Name cannot be empty! Please re-enter.</div>';
        $focusField = 'txtView';
    }else{
        $templateContent = file_get_contents('inc/templates/SimpleView.tpl');
        $templateContent = str_ireplace('[VIEW_NAME]', ucfirst($view_name), $templateContent);
        $templateContent = str_ireplace('[VIEW_NAME_LC]', strtolower($view_name), $templateContent);
    }
}

$content = '<h2>Generate code for Simple View</h2>
<p>Fill up all required entry fields and then click on Generate button to generate the code.</p>

'.$msg.'

<form action="index.php?generation_type='.$generation_type.'" method="post">
    <input type="hidden" name="act" value="post" />

    <table class="result">
    <tbody>
        <tr>
            <td width="150px">View Name:</td>
            <td><input type="text" id="txtView" name="view_name" maxlength="100" value="'.htmlentities($view_name).'" /> <span class="gray">e.g. news or posts</span></td>
        </tr>
        <tr>
            <td valign="top">Code:</td>
            <td>
                '.($templateContent ? '<a href="javascript:void(\'select\');" onclick="selectCode(\'selCode\', \'msgAction\')">Select</a>' : '').'
                '.($templateContent ? '<a href="javascript:void(\'copy\');" onclick="copyToClipboard(\'selCode\', \'msgAction\')">Copy</a>' : '').'
                <span id="msgAction" class="msg_success hidden" style="width:98%;"></span>
                <textarea id="selCode" style="width:99%;height:300px;">'.$templateContent.'</textarea>
            </td>
        </tr>
    </tbody>
    </table>
    <br><br>
        
    <input type="submit" name="btnSubmit" value="Generate">
    - or -
    <a href="index.php?generation_type='.$generation_type.'">Cancel</a>
</form>';

if($focusField){
    $content .= '<script>setFocus("'.$focusField.'");</script>';    
}


