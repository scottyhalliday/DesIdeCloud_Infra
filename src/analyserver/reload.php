<?php

    // Reload pages requested
    if (isset($_GET['reload'])) {
        error_log("SCOTT IN RELOAD!!!!!");
        header('Location:' . $_GET['reload']);
    }

?>