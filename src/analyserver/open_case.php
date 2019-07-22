<?php

    if (!isset($_SESSION)) {
      session_start();
    }

    require_once "functions.php";

    error_log("open_case.php -- In the file");

    if (isset($_POST['open_case_name'])) {
        
        error_log("open_case.php -- Open case request");

        // Case names and id's are passed in comma delimited strings.  Break apart
        $case_names = explode(",", $_POST['open_case_name']);
        $case_ids   = explode(",", $_POST['open_case_id']);

        for ($i=0; $i<count($case_ids)-1; $i++) {

            // Get the case file from S3
            $s3key = $_SESSION['s3key'] . "/" . $_SESSION['user'] . "/" . $case_names[$i];

            $case_file = read_s3_case_object($_SESSION['s3bucket'], $s3key);

            echo $case_file;
        }
    
    }

    return true;

?>