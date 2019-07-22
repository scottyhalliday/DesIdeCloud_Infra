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

        // If the SQL query failed then return, nothing more to do here
        if ($result == 0) {
            return false;
        }

        // Create the case file in the S3 bucket
        $temp_file = tempnam("/var/www/data", "deside_cloud");

        error_log("new_case.php -- Creating temporary file for new case -- " . $temp_file);

        $new_file = fopen($temp_file, "w");
        fwrite($new_file, "{'methods': {}}:\n");
        fclose($new_file);

        // Copy the file to s3 
        error_log("new_case.php -- Copying new case to S3");

        $case_key = $_SESSION['s3key'] . "/" . $_SESSION['user'] . "/" . $name;
        cp_to_s3($_SESSION['s3bucket'], $case_key, $temp_file);

        return true;
        
    }
?>