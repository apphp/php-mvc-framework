<article>
    <h1>Add Post</h1>

	<?php echo $actionMessage; ?>
	
	<div class="panel-content">
    <?php
        $listData = array('' => '-- select --');
        if(!empty($categories)){
            foreach ($categories as $cat) {
                $listData[$cat['id']] = $cat['name'];
            }
        }    
    
        echo CWidget::create('CFormView', array(
            'action'=>'posts/insert',
            'method'=>'post',
            'htmlOptions'=>array(
                'name'=>'frmAddPost'
            ),
            'fields'=>array(
                'act'     	=>array('type'=>'hidden', 'value'=>'send'),
                'header'	=>array('type'=>'textbox', 'title'=>'Header', 'value'=>$header, 'mandatoryStar'=>true, 'htmlOptions'=>array('maxlength'=>'100', 'class'=>'text_header')),
                'categoryId'	=>array('type'=>'dropdown', 'title'=>'Category', 'data'=>$listData, 'value'=>$categoryId),
                'postText'	=>array('type'=>'textarea', 'title'=>'Post Text', 'value'=>$postText, 'mandatoryStar'=>true, 'htmlOptions'=>array('maxlength'=>'4000', 'class'=>'post_text')),
                'metaTagTitle'	=>array('type'=>'textarea', 'title'=>CHtml::encode('Tag <TITLE>'), 'value'=>$metaTagTitle, 'htmlOptions'=>array('maxlength'=>'250', 'class'=>'small')),
                'metaTagKeywords'	=>array('type'=>'textarea', 'title'=>CHtml::encode('Meta Tag <KEYWORDS>'), 'value'=>$metaTagKeywords, 'htmlOptions'=>array('maxlength'=>'250', 'class'=>'middle')),
                'metaTagDescription'	=>array('type'=>'textarea', 'title'=>CHtml::encode('Meta Tag <DESCRIPTION>'), 'value'=>$metaTagDescription, 'htmlOptions'=>array('maxlength'=>'250', 'class'=>'middle')),
            ),
            'buttons'=>array(
                'cancel'=>array('type'=>'button', 'value'=>'Cancel', 'htmlOptions'=>array('name'=>'', 'onclick'=>"$(location).attr('href','posts/index');")),
                'submit'=>array('type'=>'submit', 'value'=>'Create'),
            ),
            'events'=>array(
                'focus'=>array('field'=>$errorField)
            ),
            'return'=>true,
        ));
	?>    
    </div>
    <div class="panel-settings">
        This page provides you possibility to add new post.
        Enter all needed information and click Create button.
    </div>
    <div class="clear"></div>
</article>
