<?php
    session_start();
    require_once "functions.php";

    // Get the users cases from database
    $query = "SELECT * FROM cases WHERE owner='". $_SESSION['user'] . "';";
    $result = query_msql($query);

    error_log("CASE_EXPLORER.PHP ::: QUERY ::: " . $query);
    
    // Write the header row
    echo "<tr class='case_explorer_table_header'>";
    echo "  <th class='select_case'  ><input type='checkbox' name='caseselectall' value='allcases'></th>";
    echo "  <th class='case_name'    >Case Name</th>";
    echo "  <th class='case_desc'    >Case Description</th>";
    echo "  <th class='case_created' >Created</th>";
    echo "  <th class='case_modified'>Last Modified</th>";
    echo "  <th class='case_id'      >Case ID</th>";
    echo "</tr>";

    if ($result->num_rows > 0) {
        while ($row = mysqli_fetch_array($result)) {
            echo "<tr>";
            echo "  <td class='select_case'  ><input type='checkbox' name='caseselect" . $row['case_id'] . "' value='caseid" . $row['case_id'] ."'></td>";
            echo "  <td class='case_name'    >" . $row['name']         . "</td>";
            echo "  <td class='case_desc'    >" . $row['description']  . "</td>";
            echo "  <td class='case_created' >" . $row['datecreated']  . "</td>";
            echo "  <td class='case_modified'>" . $row['datemodfied']  . "</td>";
            echo "  <td class='case_id'      >" . $row['case_id']      . "</td>";
            echo "</tr>";
        }
    }

?>

