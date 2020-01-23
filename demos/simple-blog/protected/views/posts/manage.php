<article>

	<h1>Posts</h1>

	<div id="content">
		<?php
		echo CWidget::create('CDataGrid', array(
			'model'			=> 'Posts',
			'actionPath'	=> 'posts/manage',

//			'condition'=>'',
//			'defaultOrder'=>array('id'=>'DESC'),
//			'passParameters'=>true,
		
			'pagination'	=> array('enable'=>true, 'pageSize'=>20),
			'sorting'		=> true,
			
			'filters'=>array(
				'header' 		=> array('title'=>A::t('app', 'Post Header'), 'type'=>'textbox', 'operator'=>'%like%', 'width'=>'100px', 'maxLength'=>''),
				'post_datetime' => array('title'=>A::t('app', 'Date Created'), 'type'=>'datetime', 'operator'=>'like%', 'width'=>'80px', 'maxLength'=>'', 'format'=>''),
			),
			'fields'	=> array(
				'header'   		=> array('title'=>A::t('app', 'Post Header'), 'type'=>'label', 'class'=>'left', 'headerClass'=>'left', 'stripTags'=>true, 'sortType'=>'string'),
				'category_name' => array('title'=>A::t('app', 'Category'), 'type'=>'label', 'class'=>'center', 'headerClass'=>'center', 'width'=>'120px'),
				'post_datetime' => array('title'=>A::t('app', 'Date Created'), 'type'=>'datetime', 'align'=>'center', 'width'=>'170px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'definedValues'=>array(), 'format'=>'Y-m-d H:i:s'),
				'id'    		=> array('title'=>'ID', 'type'=>'label', 'class'=>'center', 'headerClass'=>'center', 'width'=>'50px'),
			),
			
			'return'=>true,
		));
		?>
	</div>
	
</article>
