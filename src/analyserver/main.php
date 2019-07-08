<html>
<head>
  <link   rel='stylesheet' href='main.css'>
  <link   rel='stylesheet' href='modal.css'>
  <script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
</head>
<body>

<!-- Create the top header bar showing user and give user logout ability -->
<?php
    session_start();

    require_once "functions.php";
    get_user();

    // Build the header bar above menu
    echo "<table class=titlebar>";
    echo "    <tr>";
    echo "        <th align='left'>DesIde Cloud - " . $_SESSION['user'] . "</th>";
    echo "        <th align='right'>Logout</th>";
    echo "        </tr>";
    echo "</table>";
?>

<!-- Build the tab bar -->
<div class="tabbar">
    <button class="tablinks" id="case_explorer_tab" onclick="go_to_tab(event, 'case_explorer')" autofocus>Case Explorer</button>
</div>

<!-- Case Explorer Tab Content -->
<div id="case_explorer">

    <input type='image' class='ce_img' id='ce_new'     src='images/new_case.png'  title='Create a new case' onclick='create_new_case_form(event)'/>
    <input type='image' class='ce_img' id='ce_open'    src='images/open_case.png' title='Open selected cases'/>
    <input type='image' class='ce_img' id='ce_delete'  src='images/delete.png'    title='Delete selected cases'/>    
    <input type='image' class='ce_img' id='ce_refresh' src='images/refresh.svg'   title='Refresh the table', onclick='load_case_explorer_table()'/>    

    <table class='case_explorer_table'>
    <?php
        require_once "case_explorer.php";
        require_once "new_case.php";
    ?>
    </table>

    <script>
        // Make sure the tab is set to active
        var btn_obj = document.getElementById("case_explorer_tab");
        btn_obj.className = "active";
    </script>
</div>

<!-- Build the new case modal window -->
<div id='modal' class='modal_new_case_input'>
  <div class='modal-content'>
    <span class='modal_new_case_close' onclick='close_new_case_input_form()'>&times;</span>
    <h2>Create a new case</h2>
      <form method="post">
        <label for="new_case_name"><b>Case Name</b></label>
        <input class="new_case_input" type='text' name='new_case_name' id="new_case_name" placeholder='Enter a unique case name' required>
        
        <label for="new_case_desc"><b>Case Description</b></label>
        <textarea class="new_case_input" name='new_case_desc' cols='40' rows='5' id="new_case_desc" placeholder='Enter a description of your case' required></textarea>
        
        <button class="new_case_button" type='submit' onclick="create_new_case()">Create New Case</button>
      </table>
      </form>
  </div>
</div>

<!-- Javascript Functions -->
<script>

    // Calls new_case.php to create new case for entered case name and description
    function create_new_case() {
      /*var xhttp = new XMLHttpRequest();
          
      // Assemble a json file to send to server
      var json = {}
      json.new_case_name = document.getElementById("new_case_name").value;
      json.new_case_desc = document.getElementById("new_case_desc").value;

      xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
          return;
        }
      };

      xhttp.open("POST", "new_case.php");
      xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xhttp.send("q=" + JSON.stringify(json));

      // Reload the case explorer table
      load_case_explorer_table();
      */
      
      // Assemble a json file to send to server
      var json = {}
      json.new_case_name = document.getElementById("new_case_name").value;
      json.new_case_desc = document.getElementById("new_case_desc").value;

      $.ajax({
        type: "POST", 
        url: "./new_case.php",
        data: JSON.stringify(json),
        error: function(response) {
          var_dump(response);
          alert("ERROR:: " + response);
        },
        success: function(response) {
          alert("Success");
          load_case_explorer_table();
        }
      });

    }

    function create_new_case_sql(jsonStr) {

      $.ajax({
        type: "POST", 
        url: "./new_case.php",
        data: JSON.stringify(json),
        error: function(response) {
          alert("ERROR:: " + response);
        }

      });
    }

    function load_case_explorer_table() {
//      $.ajax({
//        url: "case_explorer.php",
//        success: function(data) {
//          alert(data);
//          $(".case_explorer_table").html(data);
//        }
//      });
      $.get("case_explorer.php", function(data) {
        alert(data);
        $(".case_explorer_table").html(data);
      });
    }

    function create_new_case_form(event) {

        // Modal new case window handling
        var modal = document.getElementById("modal");
        modal.style.display = "block";

    }

    function close_new_case_input_form() {
        var modal = document.getElementById("modal");
        modal.style.display = "none";
    }

</script>