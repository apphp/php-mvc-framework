<aside class="left_side">
    General
    <ul>
        <li><a class="<?php echo (($activeLink == 'home') ? ' active' : ''); ?>" href="authors/index">Home</a>
        <li><a href="index/index">View Site</a>
        <li><a class="<?php echo (($activeLink == 'settings') ? ' active' : ''); ?>" href="settings/edit">Site Settings</a>
        <li><a class="<?php echo (($activeLink == 'author') ? ' active' : ''); ?>" href="authors/edit">My Account</a>
    </ul>
    Categories Management
    <ul>
        <li><a class="<?php echo (($activeLink == 'add_category') ? ' active' : ''); ?>" href="categories/add">New Category</a>
        <li><a class="<?php echo (($activeLink == 'edit_category') ? ' active' : ''); ?>" href="categories/index">Categories</a>
    </ul>
    Posts Management
    <ul>
        <li><a class="<?php echo (($activeLink == 'add_post') ? ' active' : ''); ?>" href="posts/add">New Post</a>
        <li><a class="<?php echo (($activeLink == 'edit_post') ? ' active' : ''); ?>" href="posts/index">Posts</a>
    </ul>
</aside>
