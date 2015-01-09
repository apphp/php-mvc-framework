<article>
    <h1>Menus</h1>

	<?php echo $actionMessage; ?>
	
	<p>
	<?php 
	    if(empty($menus)){
	    	echo 'No menus found. Use "New Menu" menu to add menus.';
	    }else{
    	echo '<table class="table-records">
            <thead>
            <tr>
                <th align="left">Menu Name</th>
                <th width="170px" align="center">Order</th>
                <th align="center" width="90px">Actions</th>
            </tr>
            </thead>
            <tbody>';

    		foreach ($menus as $cat) { ?> 
    			<tr>
    				<td align="left" style="cursor:pointer;" onclick="window.location.href='menus/edit/id/<?php echo $cat['id'] ?>'" title="Click to edit"><?php echo $cat['name']?></td>
    				<td align="center"><?php echo $cat['sort_order']?></td>
                    <td align="center">
                        <a href="menus/edit/id/<?php echo $cat['id'] ?>">Edit</a> |
                        <a href="menus/delete/id/<?php echo $cat['id'] ?>" onclick="if(!confirm('Are you sure you want to delete this menu?\nNote: this will make all its menu links invisible to your site visitors!'))return false;">Delete</a>
                    </td>
                </tr>
    		<?php 
    		}
            echo '</tbody>';
	    	echo '</table>';
            
            echo CWidget::create('CPagination', array(
                'actionPath'   => 'menus/index',
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
