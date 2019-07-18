<?php
    
    error_log("reload.php -- ");

    // Reload pages requested
    if (isset($_GET['reload'])) {
        error_log("reload.php :: Header Location: " . $_GET['reload']);
        header('Location:' . $_GET['reload']);
    }

?>