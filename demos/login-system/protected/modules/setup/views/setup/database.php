<?php
A::app()->view->setMetaTags('title', A::t('setup', 'Database Settings'));
A::app()->getClientScript()->registerScriptFile('assets/modules/setup/js/setup.js', 2);

$this->_activeMenu = $this->_controller . '/' . $this->_action;
?>

<h1><?= A::t('setup', 'Database Settings'); ?></h1>
<p><?= A::t('setup', 'Database Settings Notice'); ?></p>

<?= $actionMessage; ?>
<br>

<?php
echo CWidget::create(
    'CFormView', [
        'action'      => 'setup/database',
        'method'      => 'post',
        'htmlOptions' => [
            'name' => 'frmSetup',
            'id'   => 'frmSetup'
        ],
        'fields'      => $formFields,
        'buttons'     => [
            'back'   => [
                'type'        => 'button',
                'value'       => A::t('setup', 'Previous'),
                'htmlOptions' => ['name' => '', 'onclick' => "$(location).attr('href','setup/requirements');"]
            ],
            'submit' => ['type' => 'submit', 'value' => A::t('setup', 'Next'), 'htmlOptions' => ['name' => '']]
        ],
        'events'      => [
            'focus' => ['field' => $errorField]
        ],
        'return'      => true,
    ]);
?>
<br>
