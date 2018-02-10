<?php

$content = '';
$span = 1;
foreach($test_data as $key => $val){
	$content .= '<h2>'.ucfirst($key).'</h2>';
	$content .= '<table class="result">';
	$content .= '<tr>';
	$span_temp = $span;
	while($span_temp--){
		$content .= '<th width="170px">Test Arguments</th>
					 <th width="120px" class="align-center">Expecting Value</th>
					 <th width="100px" class="align-center">Result Value</th>
					 <th>Conclusion</th>';
	}
	$content .= '<tr>';
	$count = 0;
	foreach($val as $v_key => $v_val){
		if($count++ % $span == 0) $content .= '</tr><tr>';
		$parameters = (!is_array($v_key)) ? array($v_key) : $v_key;
		$result = call_user_func_array(array('CValidator', $key), $parameters);
		$content .= '<td>'.$v_key.'</td>
					 <td class="align-center">'.($v_val ? '<span class="true">true</span>' : '<span class="false">false</span>').'</td>
					 <td class="align-center">'.($result ? '<span class="true">true</span>' : '<span class="false">false</span>').'</td>
					 <td>'.($result && $v_val || !$result && !$v_val ? '&nbsp;<span class="ok">OK</span>' : '&nbsp;<span class="failed">Failed</span>').'</td>';
	}
	$content .= '</tr>';
	$content .= '</table>';	
}

if(!count($test_data)){
	$content .= '<span class="failed">Wrong parameter passed! No tests were run.</span>';	
}

