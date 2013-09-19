<?php 
if(!empty($mainText)){
	echo '<div class="main_msg">'.$mainText.'</div>';
}
if(!empty($pages)){

    foreach($pages as $page){
   	
   		if($viewOnePage){
   			$pageHeader = $page['header_text'];
   		}else{
   			$pageHeader = '<a href="pages/view/id/'.$page['id'].'">'.$page['header_text'].'</a>';
   		}
   		
		$pageText = $page['page_text'];

   		if(empty($page['menu_id'])){
   			$menuLink = 'Not defined';
   		}else{
   			$menuLink = '<a href="menus/view/id/'.$page['menu_id'].'">'.$page['menu_name'].'</a>';
   		}
   		echo '<article>
   				<header><h2>'.$pageHeader.'</h2></header>
			  	<p>'.$pageText.'</p>
   			  </article>';
	}
    
    if(count($pages) > 1){
        echo CWidget::create('CPagination', array(
            'actionPath'   => 'pages/view',
            'currentPage'  => $currentPage,
            'pageSize'     => $pageSize,
            'totalRecords' => $totalRecords,
			'showResultsOfTotal' => false,
            'linkType' => 0,
            'paginationType' => 'prevNext'
        ));            
    }    

}    
?>

