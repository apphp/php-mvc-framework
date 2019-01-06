<?php
	A::app()->view->setMetaTags('title', A::t('setup', 'Ready to Install Updates'));
	
    $this->_activeMenu = $this->_controller.'/'.$this->_action;
?>

<h1><?= (A::app()->getSession()->get('setupType') == 'update' ? A::t('setup', 'Ready to Install Updates') : A::t('setup', 'Ready to Install')); ?></h1>
<p><?= A::t('setup', 'Ready to Install Notice'); ?></p>

<?php
    if(is_array($componentsList)){
        echo A::t('setup', 'The list of components that will be loaded').':';
        echo '<ul>';
        foreach($componentsList as $component => $compValue){
            $enable = (isset($compValue['enable']) && $compValue['enable']) ? '<span class="enabled">'.A::t('setup', 'enabled').'</span>' : '<span class="disabled">'.A::t('setup', 'disabled').'</span>';
            $class = isset($compValue['class']) ? $compValue['class'] : '';
            if(!empty($class)){                
                echo '<li>'.$class.' - '.$enable.'</li>';          
            }            
        }
        echo '</ul>';
    }
    if(is_array($modulesList)){
        echo A::t('setup', 'The list of modules that will be loaded').':';
        echo '<ul>';
        foreach($modulesList as $module => $modValue){
            $enable = (isset($modValue['enable']) && $modValue['enable']) ? '<span class="enabled">'.A::t('setup', 'enabled').'</span>' : '<span class="disabled">'.A::t('setup', 'disabled').'</span>';
            echo '<li>'.ucwords(str_replace('_', ' ', $module)).' - '.$enable.'</li>';          
        }
        echo '</ul>';
    }

    echo ($actionMessage) ? $actionMessage.'<br>' : '';

    $actionBack = (A::app()->getSession()->get('setupType') == 'update') ? 'setup/database' : 'setup/administrator';

    echo CWidget::create('CFormView', array(
        'action'=>(($installed) ? 'setup/completed' : 'setup/ready'),
        'method'=>'post',
        'htmlOptions'=>array(
            'name'=>'frmSetup'
        ),
        'fields'=>array(
            'act'=>array('type'=>'hidden', 'value'=>'send'),            
        ),
        'buttons'=>array(
            'back'=>array('type'=>'button', 'value'=>A::t('setup', 'Previous'), 'htmlOptions'=>array('name'=>'', 'onclick'=>"$(location).attr('href','".$actionBack."');")),
            'submit'=>array('type'=>'submit', 'value'=>A::t('setup', 'Next'), 'htmlOptions'=>array('name'=>''))
        ),
        'return'=>true,
    ));

?>
<br>    

