<?php
    
    session_start();

    require_once "functions.php";

    // Debugging
    //var_dump($_REQUEST);
    //var_dump($_POST);

    if (isset($_POST['new_case_name'])) {

        error_log("Hello world.  I am in new_case.php");
    
        // Get the json passed with the case information
        #$new_case = json_decode($_POST["q"], false);
    
        #error_log("REQUEST IS " . $_POST["q"]);
        #error_log("new_case " . $new_case->new_case_desc);

        $new_case = $_POST['new_case_name'];
        $new_desc = $_POST['new_case_desc'];
        
        // Get the case information passed by user
        $name = sanitize_string($new_case);
        $desc = sanitize_string($new_desc);
        $user = $_SESSION['user'];
    
        error_log("User is : " . $user);
    
        // Assemble the query string
        $query_str  = "INSERT INTO cases (name, description, s3bucket, s3key, owner) ";
        $query_str .= "VALUES ('" . $name . "', '" . $desc . "', 'bucket', 'key', '" . $_SESSION['user'] . "')";
    
        error_log("QUERY STRING: " . $query_str);
    
        $result = query_msql($query_str);
    
        error_log("result is " . $result);

    }
?>