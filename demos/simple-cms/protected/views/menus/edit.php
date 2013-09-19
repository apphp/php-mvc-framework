<article>
    <h1>Edit Menu</h1>

	<?php echo $actionMessage; ?>
	
	<div class="panel-content">
    <?php
        
        echo CWidget::create('CFormView', array(
            'action'=>'menus/update',
            'method'=>'post',
            'htmlOptions'=>array(
                'name'=>'frmEditMenu'
            ),
            'fields'=>array(
                'act'     	  =>array('type'=>'hidden', 'value'=>'send'),
                'menuId'	  =>array('type'=>'hidden', 'value'=>$menuId),
                'menuIdLabel' =>array('type'=>'label', 'title'=>'Menu ID', 'value'=>$menuId),
            	'menuName'	  =>array('type'=>'textbox', 'title'=>'Menu Name', 'value'=>$menuName, 'mandatoryStar'=>true, 'htmlOptions'=>array('maxlength'=>'50', 'class'=>'text_header', 'encode'=>true)),
				'sortOrder'  =>array('type'=>'textbox', 'title'=>'Sort Order', 'value'=>$sortOrder, 'mandatoryStar'=>true, 'htmlOptions'=>array('maxlength'=>'3', 'style'=>'width:40px', 'encode'=>true)),
            ),
            'buttons'=>array(
            	'submit'=>array('type'=>'submit', 'value'=>'Update'),
                'cancel'=>array('type'=>'button', 'value'=>'Cancel', 'htmlOptions'=>array('name'=>'', 'onclick'=>"$(location).attr('href','menus/index');"))
            ),
            'events'=>array(
                'focus'=>array('field'=>$errorField)
            ),
            'return'=>true,
        ));
    ?>    
    </div>
    <div class="panel-settings">
        This page provides you possibility to edit selected menu.
    </div>
    <div class="clear"></div>
</article>
