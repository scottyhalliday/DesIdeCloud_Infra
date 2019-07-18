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
        $query_str  = "INSERTY INTO cases (name, description, s3bucket, s3key, owner) ";
        $query_str .= "VALUES ('" . $name . "', '" . $desc . "', 'bucket', 'key', '" . $_SESSION['user'] . "')";

        // Debugging
        //error_log("NEW CASE CREATION :: QUERY STRING :: " . $query_str);
//        try {
//            $result = query_msql($query_str);
//        } catch (Exception $e) {
//            echo 'INVALID SQL QUERY.  DID NOT ADD NEW CASE.  CONTACT SYSTEM ADMINISTRATOR';
//        }
//        header("Content-type/json");

        try {
            if (!query_msql($query_str)) {
                throw new Exception("Bad SQL Query", 400);
            } else {
                return true;
            }
        } catch(Exception $e) {
            return false;
        }


        try {
            if (!query_msql($query_str)) {
                throw new Exception("Bad SQL Query", 400);
            } else {
                echo json_encode(
                    array (
                        'status' => true
                    )
                );
            }
        } catch(Exception $e) {

            echo json_encode(
                array(
                    'status'     => false,
                    'error'      => $e->getMessage(),
                    'error_code' => $e->getCode()
                )
            );
        }
        
        // Debugging
        //error_log("NEW CASE CREATION :: QUERY RESULTS :: " . $result);

        // Redirect back to the case explorer to avoid double posts (POST/REDIRECT/GET)
        //header('Location: main.php');

    }
?>