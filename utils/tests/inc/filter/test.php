<?php

$content = '';
$span = 1;
foreach($test_data as $key => $val){
	$content .= '<h2>'.ucfirst($key).'</h2>';
	$content .= '<table class="result">';
	$content .= '<tr>';
	$span_temp = $span;
	while($span_temp--){
		$content .= '<th width="140px">Type</th>
					 <th width="220px">Original Value</th>
					 <th width="220px">Sanitized Value</th>
					 <th width="120px" class="align-center">Result</th>
					 <th>Conclusion</th>';
	}
	$content .= '<tr>';
	$count = 0;
	foreach($val as $v_key => $v_val){
		if($count++ % $span == 0) $content .= '</tr><tr>';
        $parameters      = ( ! is_array($v_val)) ? [$v_val] : $v_val;
        $expected_result = isset($v_val[2]) ? $v_val[2] : true;
        $result          = call_user_func_array(['CFilter', $key], $parameters);
        $content .= '<td>'.$parameters[0].'</td>
					 <td>'.$parameters[1].'</td>
					 <td>'.$result.'</td>
					 <td class="align-center">'.(($parameters[1] === $result) ? '<span class="true">not cleaned</span>' : '<span class="false">cleaned</span>').'</td>
					 <td>'.(($parameters[1] === $result && $expected_result == true) ? '&nbsp;<span class="ok">OK</span>' : '&nbsp;<span class="failed">Failed</span>').'</td>';
	}
	$content .= '</tr>';
	$content .= '</table>';	
}

if(!count($test_data)){
	$content .= '<span class="failed">Wrong parameter passed! No tests were run.</span>';	
}

