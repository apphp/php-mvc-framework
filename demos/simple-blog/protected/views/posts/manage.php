<article>

	<h1>Posts</h1>

	<div id="content">
		<?php
		echo CWidget::create('CDataGrid', [
			'model'			=> 'Posts',
			'actionPath'	=> 'posts/manage',
			'pagination'	=> ['enable'=>true, 'pageSize'=>20],
			'sorting'		=> true,
			'filters'=>[
				'header' 		=> ['title'=>A::t('app', 'Post Header'), 'type'=>'textbox', 'operator'=>'%like%', 'width'=>'100px', 'maxLength'=>''],
				'post_datetime' => ['title'=>A::t('app', 'Date Created'), 'type'=>'datetime', 'operator'=>'like%', 'width'=>'80px', 'maxLength'=>'', 'format'=>''],
			],
			'fields'	=> [
				'header'   		=> ['title'=>A::t('app', 'Post Header'), 'type'=>'label', 'class'=>'left', 'headerClass'=>'left', 'stripTags'=>true, 'sortType'=>'string'],
				'category_name' => ['title'=>A::t('app', 'Category'), 'type'=>'label', 'class'=>'center', 'headerClass'=>'center', 'width'=>'120px'],
				'post_datetime' => ['title'=>A::t('app', 'Date Created'), 'type'=>'datetime', 'align'=>'center', 'width'=>'170px', 'class'=>'left', 'headerClass'=>'left', 'isSortable'=>true, 'definedValues'=>[], 'format'=>'Y-m-d H:i:s'],
				'id'    		=> ['title'=>'ID', 'type'=>'label', 'class'=>'center', 'headerClass'=>'center', 'width'=>'50px'],
			],
			'return'=>true,
		]);
		?>
	</div>

</article>
