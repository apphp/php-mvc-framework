<?php

$act = isset($_POST['act']) ? $_POST['act'] : '';

$module_code = isset($_POST['module_code']) ? prepare_input($_POST['module_code']) : '';
$controller_name = isset($_POST['controller_name']) ? str_ireplace('Controller', '', prepare_input($_POST['controller_name'])) : '';
$model_name = isset($_POST['model_name']) ? str_ireplace('Model', '', prepare_input($_POST['model_name'])) : '';

$view_name_manage = isset($_POST['view_name_manage']) ? prepare_input($_POST['view_name_manage']) : 'manage';
$view_name_add = isset($_POST['view_name_add']) ? prepare_input($_POST['view_name_add']) : 'add';
$view_name_edit = isset($_POST['view_name_edit']) ? prepare_input($_POST['view_name_edit']) : 'edit';
$templateManageContent = '';
$msg = '';
$focusField = '';

if($act){
    if($module_code == ''){
        $msg = '<div class="msg_error">Module Code cannot be empty! Please re-enter.</div>';
        $focusField = 'txtModuleCode';
    }elseif($controller_name == ''){
        $msg = '<div class="msg_error">Controller Name cannot be empty! Please re-enter.</div>';
        $focusField = 'txtController';
    }elseif($model_name == ''){
        $msg = '<div class="msg_error">Model Name cannot be empty! Please re-enter.</div>';
        $focusField = 'txtModel';
    }elseif($view_name_manage == ''){
        $msg = '<div class="msg_error">Manage View Name cannot be empty! Please re-enter.</div>';
        $focusField = 'txtViewManage';
    }elseif($view_name_add == ''){
        $msg = '<div class="msg_error">Add View Name cannot be empty! Please re-enter.</div>';
        $focusField = 'txtViewAdd';
    }elseif($view_name_edit == ''){
        $msg = '<div class="msg_error">Edit View Name cannot be empty! Please re-enter.</div>';
        $focusField = 'txtViewEdit';
    }else{
        $templateManageContent = file_get_contents('inc/templates/ARViewManage.tpl');
        $templateManageContent = str_ireplace('[MODULE_CODE]', strtolower($module_code), $templateManageContent);
        $templateManageContent = str_ireplace('[CONTROLLER_NAME]', ucfirst($controller_name), $templateManageContent);
        $templateManageContent = str_ireplace('[CONTROLLER_NAME_LC]', strtolower($controller_name), $templateManageContent);
        $templateManageContent = str_ireplace('[MODEL_NAME]', ucfirst($model_name), $templateManageContent);
        $templateManageContent = str_ireplace('[MODEL_NAME_LC]', strtolower($model_name), $templateManageContent);
        $templateManageContent = str_ireplace('[VIEW_NAME]', ucfirst($view_name_manage), $templateManageContent);
        $templateManageContent = str_ireplace('[VIEW_NAME_LC]', strtolower($view_name_manage), $templateManageContent);

        $templateAddContent = file_get_contents('inc/templates/ARViewAdd.tpl');
        $templateAddContent = str_ireplace('[MODULE_CODE]', strtolower($module_code), $templateAddContent);
        $templateAddContent = str_ireplace('[CONTROLLER_NAME]', ucfirst($controller_name), $templateAddContent);
        $templateAddContent = str_ireplace('[CONTROLLER_NAME_LC]', strtolower($controller_name), $templateAddContent);
        $templateAddContent = str_ireplace('[MODEL_NAME]', ucfirst($model_name), $templateAddContent);
        $templateAddContent = str_ireplace('[MODEL_NAME_LC]', strtolower($model_name), $templateAddContent);

        $templateEditContent = file_get_contents('inc/templates/ARViewEdit.tpl');
        $templateEditContent = str_ireplace('[MODULE_CODE]', strtolower($module_code), $templateEditContent);
        $templateEditContent = str_ireplace('[CONTROLLER_NAME]', ucfirst($controller_name), $templateEditContent);
        $templateEditContent = str_ireplace('[CONTROLLER_NAME_LC]', strtolower($controller_name), $templateEditContent);
        $templateEditContent = str_ireplace('[MODEL_NAME]', ucfirst($model_name), $templateEditContent);
        $templateEditContent = str_ireplace('[MODEL_NAME_LC]', strtolower($model_name), $templateEditContent);
    }
}

$content = '<h2>Generate code for Active Records Views</h2>
<p>Fill up all required entry fields and then click on Generate button to generate the code.</p>

'.$msg.'

<form action="index.php?generation_type='.$generation_type.'" method="post">
    <input type="hidden" name="act" value="post" />

    <table class="result">
    <tbody>
        <tr>
            <td width="150px">Module Code:</td>
            <td><input type="text" id="txtModuleCode" name="module_code" maxlength="100" value="'.htmlentities($module_code).'" /> <span class="gray">e.g. "news" or "testimonials"</span></td>
        </tr>
        <tr>
            <td>Controller Name:</td>
            <td><input type="text" id="txtController" name="controller_name" maxlength="100" value="'.htmlentities($controller_name).'" /> <span class="gray">e.g. News or NewsController</span></td>
        </tr>
        <tr>
            <td>Model Name:</td>
            <td><input type="text" id="txtModel" name="model_name" maxlength="100" value="'.htmlentities($model_name).'" /> <span class="gray">e.g. News or NewsModel</span></td>
        </tr>
    </tbody>
    </table>

    <table class="result">
    <tbody>
        <tr>
            <td>Manage View:</td>
            <td><input type="text" id="txtViewManage" name="view_name_manage" maxlength="100" value="'.htmlentities($view_name_manage).'" /> <span class="gray">e.g. manage (views/{sub-directory}/manage.php file)</span></td>
        </tr>
        <tr>
            <td valign="top">Manage View Code:</td>
            <td>
                '.($templateManageContent ? '<a href="javascript:void(\'select\');" onclick="selectCode(\'selCode\', \'msgActionManage\')">Select</a>' : '').'
                '.($templateManageContent ? '<a href="javascript:void(\'copy\');" onclick="copyToClipboard(\'selCode\', \'msgActionManage\')">Copy</a>' : '').'
                <span id="msgActionManage" class="msg_success hidden" style="width:98%;"></span>
                <textarea id="selCode" style="width:99%;height:100px;">'.$templateManageContent.'</textarea>
            </td>
        </tr>

        <tr>
            <td width="150px">Add View:</td>
            <td><input type="text" id="txtViewAdd" name="view_name_add" maxlength="100" value="'.htmlentities($view_name_add).'" /> <span class="gray">e.g. add (views/{sub-directory}/add.php file)</span></td>
        </tr>
        <tr>
            <td valign="top">Add View Code:</td>
            <td>
                '.($templateAddContent ? '<a href="javascript:void(\'select\');" onclick="selectCode(\'selAddCode\', \'msgActionAdd\')">Select</a>' : '').'
                '.($templateAddContent ? '<a href="javascript:void(\'copy\');" onclick="copyToClipboard(\'selAddCode\', \'msgActionAdd\')">Copy</a>' : '').'
                <span id="msgActionAdd" class="msg_success hidden" style="width:98%;"></span>
                <textarea id="selAddCode" style="width:99%;height:100px;">'.$templateAddContent.'</textarea>
            </td>
        </tr>

        <tr>
            <td width="150px">Edit View:</td>
            <td><input type="text" id="txtViewEdit" name="view_name_edit" maxlength="100" value="'.htmlentities($view_name_edit).'" /> <span class="gray">e.g. edit (views/{sub-directory}/edit.php file)</span></td>
        </tr>
        <tr>
            <td valign="top">Edit View Code:</td>
            <td>
                '.($templateEditContent ? '<a href="javascript:void(\'select\');" onclick="selectCode(\'selEditCode\', \'msgActionEdit\')">Select</a>' : '').'
                '.($templateEditContent ? '<a href="javascript:void(\'copy\');" onclick="copyToClipboard(\'selEditCode\', \'msgActionEdit\')">Copy</a>' : '').'
                <span id="msgActionEdit" class="msg_success hidden" style="width:98%;"></span>
                <textarea id="selEditCode" style="width:99%;height:100px;">'.$templateEditContent.'</textarea>
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


