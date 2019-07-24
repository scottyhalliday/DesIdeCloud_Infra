<?php
    
    require_once "functions.php";

    if (isset($_GET['logout'])) {
        
        error_log('IN LOGOUT ABOUT TO DESTROY INSTANCE');

        // Detach this instance from the Auto-scaling group
        destroy_instance();

    }

?>