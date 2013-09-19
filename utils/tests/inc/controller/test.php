<?php

$content = '';
$span = 1;
foreach($test_data as $key => $val){

    $ctest = new $key();
    $result = $ctest->testAction();
    
    $content .= '<h2>'.$key.'</h2>';
    $content .= '<table class="result">';    
    $content .= '<tr><th width="150px">Action</th><th width="140px">Result Value</th><th>Conclusion</th></tr>';		
    $content .= '<tr>';
    $content .= '   <td>index routing</td>';
    $content .= '   <td>'.($result ? 'true' : 'false').'</td>';
    $content .= '   <td>'.($result ? '<span class="ok">Passed</span>' : '<span class="failed">Failed</span>').'</td>';
    $content .= '</tr>';
    $content .= '</table>';	
}

if(!count($test_data)){
    $content .= '<span class="failed">Wrong parameter passed! No tests were run.</span>';	
}

