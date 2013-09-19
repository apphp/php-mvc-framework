<aside class="left_side">
    General
    <ul>
        <li><a class="<?php echo (($activeLink == 'home') ? ' active' : ''); ?>" href="backend/index">Home</a>
        <li><a class="<?php echo (($activeLink == 'settings') ? ' active' : ''); ?>" href="settings/edit">Site Settings</a>
        <li><a href="index/index">Preview</a>
    </ul>
    Accounts Management
    <ul>
        <li><a class="<?php echo (($activeLink == 'myAccount') ? ' active' : ''); ?>" href="admins/myAccount">My Account</a>
        <li><a class="<?php echo (($activeLink == 'admins') ? ' active' : ''); ?>" href="admins/index">Admins</a>
    </ul>
    Menus Management
    <ul>
        <li><a class="<?php echo (($activeLink == 'add_menu') ? ' active' : ''); ?>" href="menus/add">New Menu</a>
        <li><a class="<?php echo (($activeLink == 'edit_menu') ? ' active' : ''); ?>" href="menus/index">Menus</a>
    </ul>
    Pages Management
    <ul>
        <li><a class="<?php echo (($activeLink == 'add_page') ? ' active' : ''); ?>" href="pages/add">New Page</a>
        <li><a class="<?php echo (($activeLink == 'edit_page') ? ' active' : ''); ?>" href="pages/index">Pages</a>
    </ul>
</aside>
