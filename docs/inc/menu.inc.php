<?php

	echo '<ul>';
	foreach($menus as $key => $val){
		
		if(is_array($val)){			
			echo '<li class="inner">';
			echo '<span class="group" onclick="toggleGroup(\''.$key.'\')">'.$val['name'].'</span>';
			echo '<ul class="inner" id="'.$key.'">';
			foreach($val['links'] as $valKey => $valVal){
				if(empty($valVal)){
					echo '<li>'.ucwords($valKey)."\n";
				}else{
					echo '<li><a id="'.$valKey.'"'.($page == $valKey ? ' class="active"' : '').' href="index.php?page='.$valKey.'">'.$valVal.'</a></li>'."\n";					
				}
			}
			echo '</ul>';
			echo '</li>';
		}else{
			echo '<li><a id="'.$key.'"'.($page == $key ? ' class="active"' : '').' href="index.php?page='.$key.'">'.$val.'</a></li>'."\n";			
		}
	}
	echo '</ul>';

