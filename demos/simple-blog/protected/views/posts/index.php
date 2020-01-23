<article>
	
	<h1>Posts</h1>

	<div id="content">
	
	<?= $actionMessage; ?>

	<p>
	<?php
		if (empty($posts)) {
			echo 'No posts in this category. Use "New Post" menu to add posts.';
		} else {
			echo '<table class="table-records">
            <thead>
            <tr>
                <th align="left">Post Header</th>
                <th width="220px" align="center">Category</th>
                <th width="220px" align="center">Date Posted</th>
                <th width="100px">Actions</th>
            </tr>
            </thead>
            <tbody>';
			
			foreach ($posts as $post) {
				?>
				<tr>
					<td align="left" style="cursor:pointer;" onclick="window.location.href='posts/edit/id/<?= $post['id'] ?>'" title="Click to edit"><?= $post['header'] ?></td>
					<td align="center"><?= (!empty($post['category_name'])) ? $post['category_name'] : 'N/A'; ?></td>
					<td align="center"><?= CLocale::date('M j, Y, g:i a', $post['post_datetime']); ?></td>
					<td align="center">
						<a href="posts/edit/id/<?= $post['id'] ?>">Edit</a> |
						<a href="posts/delete/id/<?= $post['id'] ?>" onclick="if(!confirm('Are you sure you want to delete this post?')) return false;">Delete</a>
					</td>
				</tr>
				<?php
			}
			echo '</tbody>';
			echo '</table>';
			
			echo CWidget::create('CPagination', array(
				'actionPath' => 'posts/index',
				'currentPage' => $currentPage,
				'pageSize' => $pageSize,
				'totalRecords' => $totalRecords,
			));
			
		}
		
		
		?>
	</p>
</article>
