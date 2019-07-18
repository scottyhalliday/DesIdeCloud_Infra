<?php
    session_start();

    require_once "functions.php";
    require_once "case_explorer.php";

    // Case ID's were passed as a string of case id's seperated by commas
    $case_ids = explode(",", $_POST['action']);
    error_log("CASE_IDS : " . $case_ids . " post " . $_POST['action']);
    for ($i=0; $i < count($case_ids)-1; $i++) {
        $query = "DELETE FROM cases WHERE owner='". $_SESSION['user'] . "' AND case_id='" . $case_ids[$i] . "'";
        error_log("Delete Query Is :: " . $query);
        $result = query_msql($query);
    }
    
    // Debugging
    //error_log("DELETE CASE :: QUERY RESULTS :: " . $result);
    // Redirect back to the case explorer to avoid double posts (POST/REDIRECT/GET)
    error_log("I am about to redirect");
    header('Location: main.php');
    error_log("I redirected");

?>