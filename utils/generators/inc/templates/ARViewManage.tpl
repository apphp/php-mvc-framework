<?php
    $this->_activeMenu = '[MODULE_CODE]/manage';
    $this->_breadCrumbs = array(
        array('label'=>A::t('[MODULE_CODE]', 'Modules'), 'url'=>'modules/'),
        array('label'=>A::t('[MODULE_CODE]', '[MODULE_NAME]'), 'url'=>'modules/settings/code/[MODULE_CODE]'),
        array('label'=>A::t('[MODULE_CODE]', '[MODEL_NAME] Management')),
    );    
?>

<h1><?php echo A::t('[MODULE_CODE]', '[MODEL_NAME] Management'); ?></h1>

<div class="bloc">
    <?php echo $tabs; ?>

    <div class="content">
    <?php 
        echo $actionMessage;

        if(Admins::hasPrivilege('modules', 'edit') && Admins::hasPrivilege('[MODULE_CODE]', 'add')){
            echo '<a href="[MODULE_CODE]/add" class="add-new">'.A::t('[MODULE_CODE]', 'Add New').'</a>';
        }
        
        echo CWidget::create('CGridView', array(
            'model'=>'[MODEL_NAME]',
            'actionPath'=>'[CONTROLLER_NAME_LC]/manage',
            'condition'=>'',
            'defaultOrder'=>array(),
            'passParameters'=>true,
            'pagination'=>array('enable'=>true, 'pageSize'=>20),
            'sorting'=>true,
            'filters'=>array(
                'field_1' => array('title'=>A::t('[MODULE_CODE]', 'Field 1'), 'type'=>'textbox', 'operator'=>'like%', 'width'=>'140px', 'maxLength'=>'255'),
                'field_2' => array('title'=>A::t('[MODULE_CODE]', 'Field 2'), 'type'=>'textbox', 'operator'=>'like%', 'width'=>'140px', 'maxLength'=>'255'),
            ),
            'fields'=>array(
                'field_1'    => array('title'=>A::t('[MODULE_CODE]', 'Field 1'), 'type'=>'label', 'align'=>'', 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>''),
                'field_2'    => array('title'=>A::t('[MODULE_CODE]', 'Field 2'), 'type'=>'label', 'align'=>'', 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>''),
                'field_3'    => array('title'=>A::t('[MODULE_CODE]', 'Field 3'), 'type'=>'label', 'align'=>'', 'width'=>'', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>''),
            ),
            'actions'=>array(
                'edit'    => array(
                    'disabled'=>!Admins::hasPrivilege('modules', 'edit') || !Admins::hasPrivilege('[MODULE_CODE]', 'edit'),
                    'link'=>'[CONTROLLER_NAME_LC]/edit/id/{id}', 'imagePath'=>'templates/backend/images/edit.png', 'title'=>A::t('[MODULE_CODE]', 'Edit this record')
                ),
                'delete'  => array(
                    'disabled'=>!Admins::hasPrivilege('modules', 'edit') || !Admins::hasPrivilege('[MODULE_CODE]', 'delete'),
                    'link'=>'[CONTROLLER_NAME_LC]/delete/id/{id}', 'imagePath'=>'templates/backend/images/delete.png', 'title'=>A::t('[MODULE_CODE]', 'Delete this record'), 'onDeleteAlert'=>true
                ),
            ),
            'return'=>true,
        ));        
    ?>
    </div>
</div>
