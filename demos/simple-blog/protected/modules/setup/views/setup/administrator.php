<?php
A::app()->view->setMetaTags('title', A::t('setup', 'Administrator Account'));
A::app()->getClientScript()->registerScriptFile('assets/modules/setup/js/setup.js', 2);

$this->_activeMenu = $this->_controller . '/' . $this->_action;
?>

<h1><?= A::t('setup', 'Administrator Account'); ?></h1>
<p><?= A::t('setup', 'Administrator Account Notice'); ?></p>

<?= $actionMessage; ?>
<br>

<?php
echo CWidget::create(
    'CFormView',
    [
        'action'      => 'setup/administrator',
        'method'      => 'post',
        'htmlOptions' => [
            'name' => 'frmSetup',
        ],
        'fields'      => [
            'act'      => ['type' => 'hidden', 'value' => 'send'],
            'username' => ['type' => 'textbox', 'value' => $username, 'title' => A::t('setup', 'Username'), 'mandatoryStar' => true, 'htmlOptions' => ['maxLength' => '32', 'autocomplete' => 'off']],
            'password' => ['type' => 'password', 'value' => $password, 'title' => A::t('setup', 'Password'), 'mandatoryStar' => true, 'htmlOptions' => ['maxLength' => '25', 'autocomplete' => 'off', 'id' => 'password'], 'appendCode'    => '<div for="password" class="toggle_password" data-field="password"></div>'],
            'email'    => ['type' => 'textbox', 'value' => $email, 'title' => A::t('setup', 'Email'), 'mandatoryStar' => false, 'htmlOptions' => ['maxLength' => '100', 'autocomplete' => 'off']],
        ],
        'buttons'     => [
            'back'   => [
                'type'        => 'button',
                'value'       => A::t('setup', 'Previous'),
                'htmlOptions' => ['name' => '', 'onclick' => "$(location).attr('href','setup/database');"]
            ],
            'submit' => ['type' => 'submit', 'value' => A::t('setup', 'Next'), 'htmlOptions' => ['name' => '']],
        ],
        'events'      => [
            'focus' => ['field' => $errorField],
        ],
        'return'      => true,
    ]
);

?>
<br>