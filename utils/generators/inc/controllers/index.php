<?php

$act = isset($_POST['act']) ? $_POST['act'] : '';
$controller_name = isset($_POST['controller_name']) ? (string)str_ireplace('Controller', '', prepare_input($_POST['controller_name'])) : '';
$templateContent = '';
$focusField = '';
$msg = '';

if($act){
    if($controller_name == ''){
        $msg = '<div class="msg_error">Controller Name cannot be empty! Please re-enter.</div>';
        $focusField = 'txtController';
    }else{
        $templateContent = file_get_contents('inc/templates/SimpleControllerClass.tpl');
        $templateContent = str_ireplace('[CONTROLLER_NAME]', ucfirst($controller_name), $templateContent);
        $templateContent = str_ireplace('[CONTROLLER_NAME_LC]', strtolower($controller_name), $templateContent);
    }
}

$content = '<h2>Generate code for Simple Controller</h2>
<p>Fill up all required entry fields and then click on Generate button to generate the code.</p>

'.$msg.'

<form action="index.php?generation_type='.$generation_type.'" method="post">
    <input type="hidden" name="act" value="post" />

    <table class="result">
    <tbody>
        <tr>
            <td width="150px">Controller Name:</td>
            <td><input type="text" id="txtController" name="controller_name" maxlength="100" value="'.htmlentities($controller_name).'" /> <span class="gray">e.g. News or NewsController</span></td>
        </tr>
        <tr>
            <td valign="top">Code:</td>
            <td>
                '.($templateContent ? '<a href="javascript:void(\'select\');" onclick="selectCode(\'selCode\')">Select</a>' : '').'
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


