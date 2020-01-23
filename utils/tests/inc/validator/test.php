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
					 <th width="120px">Params</th>
					 <th width="120px" class="align-center">Expecting Value</th>
					 <th width="100px" class="align-center">Result Value</th>
					 <th>Conclusion</th>';
	}
	$content .= '<tr>';
	$count = 0;
	foreach($val as $v_key => $v_val){
		if($count++ % $span == 0) $content .= '</tr><tr>';
		
		$parameters = '';
		if(isset($v_val['allowed'])) $parameters = isset($v_val['allowed']) ? $v_val['allowed'] : '';
		elseif(isset($v_val['pattern'])) $parameters = isset($v_val['pattern']) ? $v_val['pattern'] : '';

		$result = CValidator::$key($v_key, $parameters);
		$expectedValue = isset($v_val['expected']) ? $v_val['expected'] : $v_val;
		$content .= '<td>'.$v_key.'</td>
					 <td class="align-left">';
						if(isset($v_val['allowed'])) $content .= is_array($v_val['allowed']) ? implode(',', $v_val['allowed']) : $v_val['allowed'];
						elseif(isset($v_val['pattern'])) $content .= $v_val['pattern'];
					 $content .= '</td>
					 <td class="align-center">'.($expectedValue ? '<span class="true">true</span>' : '<span class="false">false</span>').'</td>
					 <td class="align-center">'.($result ? '<span class="true">true</span>' : '<span class="false">false</span>').'</td>
					 <td>'.($result && $expectedValue || !$result && !$expectedValue ? '&nbsp;<span class="ok">OK</span>' : '&nbsp;<span class="failed">Failed</span>').'</td>';
	}
	$content .= '</tr>';
	$content .= '</table>';	
}

if(!count($test_data)){
	$content .= '<span class="failed">Wrong parameter passed! No tests were run.</span>';	
}

