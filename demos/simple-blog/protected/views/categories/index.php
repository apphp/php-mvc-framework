<article>
    <h1>Categories</h1>

	<?php echo $actionMessage; ?>
	
	<p>
	<?php 
	    if(empty($categories)){
	    	echo 'No categories found. Use "New Category" menu to add categories.';
	    }else{
    	echo '<table class="table-records">
            <thead>
            <tr>
                <th align="left">Category Name</th>
                <th width="170px" align="center">Posts</th>
                <th width="100px">Actions</th>
            </tr>
            </thead>
            <tbody>';

    		foreach ($categories as $cat) { ?> 
    			<tr>
    				<td align="left" style="cursor:pointer;" onclick="window.location.href='categories/edit/id/<?php echo $cat['id'] ?>'" title="Click to edit"><?php echo $cat['name']?></td>
    				<td align="center"><?php echo $cat['posts_count'] ?></td>
                    <td align="center">
                        <a href="categories/edit/id/<?php echo $cat['id'] ?>">Edit</a> |
                        <a href="categories/delete/id/<?php echo $cat['id'] ?>" onclick="if(!confirm('Are you sure you want to delete this category?\nNote: this will make all its category links invisible to your site visitors!'))return false;">Delete</a>
                    </td>
                </tr>
    		<?php 
    		}
            echo '</tbody>';
	    	echo '</table>';
            
            echo CWidget::create('CPagination', array(
                'actionPath'   => 'categories/index',
                'currentPage'  => $currentPage,
                'pageSize'     => $pageSize,
                'totalRecords' => $totalRecords,
				'linkType' => 1,
				'paginationType' => 'fullNumbers'
            ));    
	    }
		?>
	</p>
</article>
