<?php
    
    if (!isset($_SESSION)) {
      session_start();
    }

    require_once "functions.php";
    require_once "case_explorer.php";

    // Debugging
    //var_dump($_REQUEST);
    //var_dump($_POST);
    error_log("new_case.php -- In the file");

    if (isset($_POST['new_case_name'])) {
        
        error_log("new_case.php -- New case request");

        // Get the information provided by the user
        $new_case = $_POST['new_case_name'];
        $new_desc = $_POST['new_case_desc'];
        
        // Cleanup the input
        $name = sanitize_string($new_case);
        $desc = sanitize_string($new_desc);
        $user = $_SESSION['user'];
    
        // Assemble the query string to add new case
        $query_str  = "INSERT INTO cases (name, description, s3bucket, s3key, owner) ";
        $query_str .= "VALUES ('" . $name . "', '" . $desc . "', '" . $_SESSION['s3bucket'] . "', '" . $_SESSION['s3key']  . "', '" . $_SESSION['user'] . "')";

        // Debugging
        error_log("NEW CASE CREATION :: QUERY STRING :: " . $query_str);

        $result = query_msql($query_str);
        
        // Debugging
        error_log("NEW CASE CREATION :: QUERY RESULTS :: " . $result);

        if ($result == 1) {
            return true;
        } else {
            return false;
        }

    }
?>