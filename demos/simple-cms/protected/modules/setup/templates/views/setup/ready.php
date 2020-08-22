<?php
    $this->_activeMenu = $this->_controller.'/'.$this->_action;
?>

<h1>Ready to Install</h1>

<p>
    We are now ready to proceed with installation. At this step we will attempt to create all
    required tables and populate them with data. Should something go wrong, go back to the
    Database Settings step and make sure everything is correct.
</p>

<?php
    if(is_array($componentsList)){
        echo 'The list of components, that will be loaded:';
        echo '<ul>';
        foreach($componentsList as $component => $compValue){
            $enable = (isset($compValue['enable']) && $compValue['enable']) ? '<span class="enabled">enabled</span>' : '<span class="disabled">disabled</span>';
            $class = isset($compValue['class']) ? $compValue['class'] : '';
            if(!empty($class)){                
                echo '<li>'.$class.' - '.$enable.'</li>';          
            }            
        }
        echo '</ul>';
    }
    if(is_array($modulesList)){
        echo 'The list of modules, that will be loaded:';
        echo '<ul>';
        foreach($modulesList as $module => $modValue){
            $enable = (isset($modValue['enable']) && $modValue['enable']) ? '<span class="enabled">enabled</span>' : '<span class="disabled">disabled</span>';
            echo '<li>'.ucwords(str_replace('_', ' ', $module)).' - '.$enable.'</li>';          
        }
        echo '</ul>';
    }
?>

<?php echo ($actionMessage) ? $actionMessage.'<br>' : ''; ?>

<?php

echo CWidget::create(
    'CFormView',
    [
        'action'      => 'setup/ready',
        'method'      => 'post',
        'htmlOptions' => ['name' => 'frmSetup'],
        'fields'      => [
            'act' => ['type' => 'hidden', 'value' => 'send'],
        ],
        'buttons'     => [
            'back'   => [
                'type'        => 'button',
                'value'       => 'Previous',
                'htmlOptions' => ['name' => '', 'onclick' => "$(location).attr('href','setup/administrator');"]
            ],
            'submit' => ['type' => 'submit', 'value' => 'Next', 'htmlOptions' => ['name' => '']]
        ],
        'return'      => true,
    ]
);

?>
<br>    

