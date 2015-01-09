<article>
    <h1>Edit Page</h1>

	<?php echo $actionMessage; ?>
	
	<div class="panel-content">
    <?php
		$listData = array('' => '-- select --');
		if(!empty($menus)){
			foreach ($menus as $cat) {
				$listData[$cat['id']] = $cat['name'];
			}
		}    
		
		echo CWidget::create('CFormView', array(
			'action'=>'pages/update',
			'method'=>'post',
			'htmlOptions'=>array(
				'name'=>'frmEditPage'
			),
			'fields'=>array(
                'act'     	  =>array('type'=>'hidden', 'value'=>'send'),
				'pageId'	  =>array('type'=>'hidden', 'value'=>$pageId),
                'link_text'   =>array('type'=>'textbox', 'title'=>'Link', 'value'=>$linkText, 'mandatoryStar'=>true, 'htmlOptions'=>array('maxlength'=>'255', 'class'=>'text_header')),
                'header_text' =>array('type'=>'textbox', 'title'=>'Header', 'value'=>$headerText, 'mandatoryStar'=>true, 'htmlOptions'=>array('maxlength'=>'255', 'class'=>'text_header')),
                'menuId'	  =>array('type'=>'dropdown', 'title'=>'Menu', 'data'=>$listData, 'value'=>$menuId),
                'is_homepage' =>array('type'=>'checkbox', 'title'=>'Homepage', 'tooltip'=>'', 'value'=>'1', 'checked'=>($isHomePage ? true : false), 'htmlOptions'=>array()),
                'metaTagTitle'	=>array('type'=>'textbox', 'title'=>CHtml::encode('Page <TITLE>'), 'value'=>$metaTagTitle, 'htmlOptions'=>array('maxlength'=>'250', 'class'=>'text_header')),
                'metaTagKeywords'	=>array('type'=>'textbox', 'title'=>CHtml::encode('Page <KEYWORDS>'), 'value'=>$metaTagKeywords, 'htmlOptions'=>array('maxlength'=>'250', 'class'=>'text_header')),
                'metaTagDescription'	=>array('type'=>'textbox', 'title'=>CHtml::encode('Page <DESCRIPTION>'), 'value'=>$metaTagDescription, 'htmlOptions'=>array('maxlength'=>'250', 'class'=>'text_header')),
                'page_text'	  =>array('type'=>'textarea', 'title'=>'Page Text', 'value'=>$pageText, 'mandatoryStar'=>true, 'htmlOptions'=>array('maxlength'=>'4000', 'class'=>'large')),
			),
			'buttons'=>array(
				'submit'=>array('type'=>'submit', 'value'=>'Update'),
				'cancel'=>array('type'=>'button', 'value'=>'Cancel', 'htmlOptions'=>array('name'=>'', 'onclick'=>"$(location).attr('href','pages/index');")),
			),
			'events'=>array(
				'focus'=>array('field'=>$errorField)
			),
			'return'=>true,
		));
	?>
    </div>
    <div class="panel-settings">
        This page provides you possibility to edit selected page.
        Enter the data you need and click Update button to save the changes.
    </div>
    <div class="clear"></div>
</article>
