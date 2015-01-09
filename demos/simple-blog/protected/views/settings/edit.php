<article>
    <h1>General Site Settings</h1>

	<?php echo $actionMessage; ?>
	
	<div class="panel-content">
    <?php
	        echo CWidget::create('CFormView', array(
	            'action'=>'settings/update',
	            'method'=>'post',
	            'htmlOptions'=>array(
	                'name'=>'frmSettings'
	            ),
	            'fields'=>array(
	                'act'     	=>array('type'=>'hidden', 'value'=>'send'),
	                'blogName'	=>array('type'=>'textbox', 'value'=>$blogName, 'title'=>'Blog Name', 'mandatoryStar'=>true, 'htmlOptions'=>array('maxlength'=>'100', 'class'=>'text_header')),
	                'slogan'	=>array('type'=>'textarea', 'value'=>$slogan, 'title'=>'Slogan', 'mandatoryStar'=>false, 'htmlOptions'=>array('maxlength'=>'250', 'class'=>'small')),
	                'footer'	=>array('type'=>'textarea', 'value'=>$footer, 'title'=>'Footer', 'mandatoryStar'=>false, 'htmlOptions'=>array('maxlength'=>'250', 'class'=>'middle')),
	  	            'postMaxChars'=>array('type'=>'textbox', 'value'=>$postMaxChars, 'title'=>'Maximum Post Length', 'mandatoryStar'=>true, 'htmlOptions'=>array('maxlength'=>'5','class'=>'numeric')),
              		'metaTagTitle'	=>array('type'=>'textarea', 'value'=>$metaTagTitle, 'title'=>CHtml::encode('Tag <TITLE>'), 'mandatoryStar'=>true, 'htmlOptions'=>array('maxlength'=>'250', 'class'=>'small')),
	                'metaTagKeywords'	=>array('type'=>'textarea', 'value'=>$metaTagKeywords, 'title'=>CHtml::encode('Meta Tag <KEYWORDS>'), 'mandatoryStar'=>false, 'htmlOptions'=>array('maxlength'=>'250', 'class'=>'middle')),
	                'metaTagDescription'=>array('type'=>'textarea', 'value'=>$metaTagDescription, 'title'=>CHtml::encode('Meta Tag <DESCRIPTION>'), 'mandatoryStar'=>false, 'htmlOptions'=>array('maxlength'=>'250', 'class'=>'middle')),
	                ),
	            'buttons'=>array(
	                'reset'=>array('type'=>'reset', 'value'=>'Reset', 'htmlOptions'=>array('type'=>'reset')),
	                'submit'=>array('type'=>'submit', 'value'=>'Update')
	            ),
	            'events'=>array(
	                'focus'=>array('field'=>$errorField)
	            ),
	            'return'=>true,
	        ));
	    ?>    
    </div>
    <div class="panel-settings">
        This page provides you possibility to edit global site settings.
        Enter the data you need and click Update button to save the changes.
    </div>
    <div class="clear"></div>
</article>
