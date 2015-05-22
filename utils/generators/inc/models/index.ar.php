<?php

$act = isset($_POST['act']) ? $_POST['act'] : '';
$model_name = isset($_POST['model_name']) ? str_ireplace('Model', '', prepare_input($_POST['model_name'])) : '';
$table_name = isset($_POST['table_name']) ? strtolower(prepare_input($_POST['table_name'])) : '';
$templateContent = '';
$msg = '';
$focusField = '';

if($act){
    if($model_name == ''){
        $msg = '<div class="msg_error">Model Name cannot be empty! Please re-enter.</div>';
        $focusField = 'txtModel';
    }else if($table_name == ''){
        $msg = '<div class="msg_error">Table Name cannot be empty! Please re-enter.</div>';
        $focusField = 'txtTable';
    }else{
        $templateContent = file_get_contents('inc/templates/ARModelClass.tpl');
        $templateContent = str_ireplace('[MODEL_NAME]', ucfirst($model_name), $templateContent);
        $templateContent = str_ireplace('[MODEL_NAME_LC]', strtolower($model_name), $templateContent);
        $templateContent = str_ireplace('[TABLE_NAME]', $table_name, $templateContent);
    }
}

$content = '<h2>Generate code for Active Records Model</h2>
<p>Fill up all required entry fields and then click on Generate button to generate the code.</p>

'.$msg.'

<form action="index.php?generation_type='.$generation_type.'" method="post">
    <input type="hidden" name="act" value="post" />

    <table class="result">
    <tbody>
        <tr>
            <td width="150px">Model Name:</td>
            <td><input type="text" id="txtModel" name="model_name" maxlength="100" value="'.htmlentities($model_name).'" /> <span class="gray">e.g. News or NewsModel</span></td>
        </tr>
        <tr>
            <td>Table Name:</td>
            <td><input type="text" id="txtTable" name="table_name" maxlength="100" value="'.htmlentities($table_name).'" /> <span class="gray">e.g. news</span></td>
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


