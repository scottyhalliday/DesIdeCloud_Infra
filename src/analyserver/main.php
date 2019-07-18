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
    <input type='image' class='ce_img' id='ce_delete'  src='images/delete.png'    title='Delete selected cases' onclick='delete_selected_cases()'/>    
    <input type='image' class='ce_img' id='ce_refresh' src='images/refresh.svg'   title='Refresh the table', onclick='load_case_explorer_table()'/>    

    <table class='case_explorer_table'>
    <?php
        require_once "case_explorer.php";
        require_once "new_case.php";
        build_case_explorer_table();
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
      
      // Assemble a json file to send to server
      var json = {}
      json.new_case_name = document.getElementById("new_case_name").value;
      json.new_case_desc = document.getElementById("new_case_desc").value;

//      $.ajax({
//        type: "post", 
//        url: "./new_case.php",
//        contentType: "application/json; charset=utf-8",
//        dataType: "html",
//        data: JSON.stringify(json),
//        error: function(response) {
//          console.log(response);
//          alert("ERROR:: " + response);
//        },
//        complete: function(response) {
//          alert("Success");
//          console.log(response);
//          //load_case_explorer_table();
//        }
//      });

      var cname   = document.getElementById("new_case_name").value;
      var cdesc   = document.getElementById("new_case_desc").value;
      var params  = "new_case_name=" + cname + "&new_case_desc=" + cdesc;
      var request = new XMLHttpRequest();

      request.open("POST", "new_case.php", true);
      request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      request.setRequestHeader("Content-length", params.length);
      request.setRequestHeader("Connection", "close");

      // Callback function
      request.onreadystatechange = function()
      {
        //alert("STATUS CODE :: " + this.status + " READY STATE :: " + this.readyState);
        if (this.readyState == 4 && this.status == 200) {
          //alert("STATUS CODE :: " + this.status);
          //var json = JSON.parse(this.responseText);

          //console.log('Response text is ' + this.responseText);

          //if (!json || json.status !== true) {
          //  alert("ERROR: Could not add new case.  Contact Administrator");
          //}
          if (this.response == false) {
            alert("ERROR: Could not add new case.  Contact Administrator");
          }
          reload_this_page();
        }        
      }

      request.send(params);

    }

    // All a post/redirect/get pattern to prevent re-sending of data on refresh of window
    function reload_this_page() {

      var request = new XMLHttpRequest();
      request.open("GET", "reload.php?reload=main.php", true);
      request.onreadystatechange = function()
      {
        if (this.readyState == 4 && this.status == 200) {
            location.reload();
        }
      }
      request.send();

    }

    // Reload the case explorer table by simply calling for a refresh of this page
    function load_case_explorer_table() {
      document.location.reload();      
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

    // For each selected case, delete it
    function delete_selected_cases() {

      // Get the selected case ID's
      var i;
      var j;
      var table = document.getElementsByClassName('case_explorer_table');
      var tr;
      var td;
      var caseids = "";

      for (i=0; i<table[0].rows.length; i++) {
        
        // Get the cell items
        td = table[0].rows[i].getElementsByTagName('td');
        
        // If tag is not a 'td' tag then move on
        if (td.length == 0) {
          continue;
        }

        // Is this row checked?
        if (td[0].firstChild.checked == true) {
          caseids = caseids + td[td.length-1].innerHTML + ",";
        }

      }

      // If items were selected then continue to delete them
      if (caseids.length == 0) {
        return;
      }
      console.log("Case IDS :::: " . caseids);

      // Make sure the user is sure
      var txt;
      var rep = confirm("Are you sure you want to delete these cases?");
      if (rep == false) {
        alert("Delete Canceled!");
        return;
      }
      console.log("Case IDS :::: " . caseids);
      $.ajax({
        type: "post", 
        url: "./delete_cases.php",
        //data: {action: 'delete_cases', value: caseids},
        data: {action: caseids},
        error: function(response) {
          console.log(response);
          alert("ERROR:: " + response);
        },
        success: function(response) {
          console.log("Success!");
          console.log(response);
        }
      });

      window.location = window.location.href;

    }

</script>