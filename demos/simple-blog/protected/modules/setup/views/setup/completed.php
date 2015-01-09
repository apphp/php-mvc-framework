<?php
    $this->_activeMenu = $this->_controller.'/'.$this->_action;
?>

<h1>Completed</h1>

<?php echo $actionMessage; ?>
    
<p>
    Your website is available at <a href="<?php echo A::app()->getRequest()->getBaseUrl(); ?>"><?php echo A::app()->getRequest()->getBaseUrl(); ?></a>
    <br><br>
    You may login using these details:<br>
    Username is: <i><?php echo $username; ?></i>
    <br>
    Password is: <i><?php echo $password; ?></i>
    <br><br>
</p>


