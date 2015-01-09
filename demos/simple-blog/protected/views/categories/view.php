<?php

if(!empty($mainText)){
	echo '<div class="main_msg">'.$mainText.'</div>';
}

if(!empty($posts)){

	if(!CAuth::isLoggedIn()){
		echo '<div class="alert alert-info">Click <a href="login/index"><b>here</b></a> to log into the system as Blog Author.</div>';
	}

    foreach($posts as $post){
   	
   		if($viewOnePost){
   			$postHeader = $post['header'];
   		}else{
   			$postHeader = '<a href="posts/view/id/'.$post['id'].'">'.$post['header'].'</a>';
   		}
   		
   		if(A::app()->getSession()->get('loggedIn') == true){
   			$editLink = ' <a href="posts/edit/id/'.$post['id'].'" class="edit_link">[edit]</a>';
   		}else{
   			$editLink = '';
   		}
   		
   		if(!$viewOnePost && strlen($post['post_text']) > $postMaxChars){
   			$postText = BlogHelper::strTruncate($post['post_text'], $postMaxChars).' <a href="posts/view/id/'.$post['id'].'">read more...</a>';
   		}else{
   			$postText = $post['post_text'];
   		}

   		if(empty($post['category_id'])){
   			$categoryLink = 'Not defined';
   		}else{
   			$categoryLink = '<a href="categories/view/id/'.$post['category_id'].'">'.$post['category_name'].'</a>';
   		}
   		$dateFormatted = date('M j, Y, g:i a', strtotime($post['post_datetime']));
   		echo '<article>
   				<header><h2>'.$postHeader.$editLink.'</h2></header>
			  	<p>'.$postText.'</p>
			  	<div class="post_footer">
			  		Posted'.($post['login'] ? ' by '.$post['login'] : '').' at '.$dateFormatted.'<br>
					Category: '.$categoryLink.'
					</div>
   			  </article>';
	}
    
    if(count($posts) > 1){
        echo CWidget::create('CPagination', array(
            'actionPath'   => 'posts/view',
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

