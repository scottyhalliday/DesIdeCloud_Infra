<?php
    
    session_start();

    require_once "functions.php";
    require_once "case_explorer.php";

    // Debugging
    //var_dump($_REQUEST);
    //var_dump($_POST);

    if (isset($_POST['new_case_name'])) {

        // Get the information provided by the user
        $new_case = $_POST['new_case_name'];
        $new_desc = $_POST['new_case_desc'];
        
        // Cleanup the input
        $name = sanitize_string($new_case);
        $desc = sanitize_string($new_desc);
        $user = $_SESSION['user'];
    
        // Assemble the query string
        $query_str  = "INSERT INTO cases (name, description, s3bucket, s3key, owner) ";
        $query_str .= "VALUES ('" . $name . "', '" . $desc . "', 'bucket', 'key', '" . $_SESSION['user'] . "')";

        // Debugging
        //error_log("NEW CASE CREATION :: QUERY STRING :: " . $query_str);
    
        $result = query_msql($query_str);
        
        // Debugging
        //error_log("NEW CASE CREATION :: QUERY RESULTS :: " . $result);

    }
?>