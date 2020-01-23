<article>
	<h1>Pages</h1>
	
	<?= $actionMessage; ?>

	<p>
		<?php
		if (empty($pages)) {
			echo 'No pages in this menu. Use "New Page" menu to add pages.';
		} else {
			echo '<table class="table-records">
            <thead>
            <tr>
                <th align="left">Page Header</th>
                <th align="center" width="190px">Menu</th>
                <th align="center" width="170px">Date Created</th>
				<th align="center" width="120px">Homepage</th>
				<th align="center" width="80px">ID</th>
                <th align="center" width="90px">Actions</th>
            </tr>
            </thead>
            <tbody>';
			
			foreach ($pages as $page) {
				?>
				<tr>
					<td align="left" style="cursor:pointer;" onclick="window.location.href='pages/edit/id/<?= $page['id'] ?>'" title="Click to edit"><?= $page['link_text'] ?></td>
					<td align="center"><?= (!empty($page['menu_name'])) ? $page['menu_name'] : 'N/A'; ?></td>
					<td align="center"><?= CLocale::date('M j, Y, g:i a', $page['created_at']); ?></td>
					<td align="center"><?= ($page['is_homepage'] == '1') ? '<span style="color:#009900;">Yes</span>' : 'No'; ?></td>
					<td align="center"><?= $page['id'] ?></td>
					<td align="center">
						<a href="pages/edit/id/<?= $page['id'] ?>">Edit</a> |
						<a href="pages/delete/id/<?= $page['id'] ?>" onclick="if(!confirm('Are you sure you want to delete this page?')) return false;">Delete</a>
					</td>
				</tr>
				<?php
			}
			echo '</tbody>';
			echo '</table>';
			
			echo CWidget::create('CPagination', array(
				'actionPath' => 'pages/index',
				'currentPage' => $currentPage,
				'pageSize' => $pageSize,
				'totalRecords' => $totalRecords,
			));
			
		}
		
		
		?>
	</p>
</article>
