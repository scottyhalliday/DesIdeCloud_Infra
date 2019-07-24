<html>
<head>
  <link   rel='stylesheet' href='main.css'>
  <link   rel='stylesheet' href='modal.css'>
  <script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
</head>
<body>

<!-- Create the top header bar showing user and give user logout ability -->
<?php
    if (!isset($_SESSION)) {
      session_start();
    }

    require_once "functions.php";
    get_user();
    get_login_redirect();
    get_s3_info();

    // Build the header bar above menu
    echo "<table class=titlebar>";
    echo "    <tr>";
    echo "        <th align='left'>DesIde Cloud - " . $_SESSION['user'] . "</th>";
    echo "        <th align='right'><button class='logout_btn' onclick='logout()'>Logout</button></th>";
    echo "        </tr>";
    echo "</table>";
?>

<!-- Build the tab bar -->
<div class="tabbar">
    <button class="tablinks" id="case_explorer_tab" onclick="go_to_tab('case_explorer', 'case_explorer_tab')" autofocus>Case Explorer</button>
    <button class="tablinks" id="methods_tab" onclick="go_to_tab('methods_ballad', 'methods_tab')" autofocus>Case Example</button>

</div>

<!-- Case Explorer Tab Content -->
<div id="case_explorer" class="tabcontent">

    <input type='image' class='ce_img' id='ce_new'     src='images/new_case.png'  title='Create a new case' onclick='create_new_case_form(event)'/>
    <input type='image' class='ce_img' id='ce_open'    src='images/open_case.png' title='Open selected cases' onclick='open_cases()'/>
    <input type='image' class='ce_img' id='ce_delete'  src='images/delete.png'    title='Delete selected cases' onclick='delete_selected_cases()'/>    
    <input type='image' class='ce_img' id='ce_refresh' src='images/refresh.svg'   title='Refresh the table', onclick='reload_this_page()'/>    

    <table class='case_explorer_table'>
    <?php
        require_once "case_explorer.php";
        build_case_explorer_table();
    ?>
    </table>

    <script>
      // Make sure the tab is set to active
      //var btn_obj = document.getElementById("case_explorer_tab");
      //btn_obj.className = "active";
      document.getElementById("case_explorer_tab").style.backgroundColor="#ffffff";

    </script>
</div>

<!-- Case Explorer Tab Content -->
<div id="methods_ballad" class="tabcontent">

  <p>You have reached the end of this demo.  Since this demo is about cloud infrastructure automation
     and not specifically on building a web application I will wrap up here with the web application portion.
     Implementation of Case tabs becomes a bit burdonsome with handling the management of user data and the 
     user interface itself.
  </p>
  <p>I list some of the things you could do in the case tab to give a better idea.</p>
  <ul>
    <li>Allow users to add multiple methods which handle specific tasks (i.e. Method X calculates the area of a circle, Method Y calculates the area of a triangle, etc.)</li>
    <li>Allow user to enter their input for each method and run the calculation</li>
    <li>Share results of methods to other methods to build a calculation chain</li>
    <li>Offer advanced UI tools to aid better user experience (i.e. diff viewers for output versions)</li>
    <li>Offer advanced error handling of input to offer better user experience</li>
    <li>And so much more....</li>
  </ul>

  <p>Hopefully that gets the point accross and you get an idea of how you could use the infrastructure.  I also made a sketch of what I described to better illustrate it.
  </p>

  <img src="images/deside_cloud_sample.png" alt="deside_cloud_sample.png" width="50%" height="50%">

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

    // Check or uncheck all checkboxes if top most checkbox is clicked
    $('input[name=caseselectall]').click( function(){
      var i;
      var table = document.getElementsByClassName('case_explorer_table');
      var tr;
      var td;

      for (i=0; i<table[0].rows.length; i++) {
        // Get the cell items
        td = table[0].rows[i].getElementsByTagName('td');
      
        // If tag is not a 'td' tag then move on
        if (td.length == 0) {
          continue;
        }

        // Toggle the checkbox
        td[0].firstChild.checked = $(this).prop("checked");

      }

    });

    // Emulate tab selection
    function go_to_tab(blockid, tabid) {
      var i, tabcontent, tablinks, tabbar;

      tabcontent = document.getElementsByClassName("tabcontent");

      // Disable all tabcontents
      for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
      }      

      // Enable the selected tab
      document.getElementById(blockid).style.display = "block";

      // Deactivate the tabs
      tabbar = document.getElementsByClassName("tablinks");
      for (i = 0; i < tabbar.length; i++) {
        tabbar[i].style.backgroundColor = "";
      }      

      // Activate clicked one
      document.getElementById(tabid).style.backgroundColor = "#ffffff";
    }

    // Calls new_case.php to create new case for entered case name and description
    function create_new_case() {
      
      // Debugging
      console.log("main.php -- create_new_case()");

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
        if (this.readyState == 4 && this.status == 200) {

          // If the case was not created then issue an alert
          if (this.response === false) {
            alert("ERROR: Could not add new case.  Contact Administrator");
          }
          reload_this_page();
        }        
      }

      request.send(params);

    }

    // Allow a post/redirect/get pattern to prevent re-sending of data on refresh of window
    function reload_this_page() {

      console.log("main.php -- reload_this_page()");

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

    // Create modal form for entering new case information
    function create_new_case_form(event) {

        // Modal new case window handling
        var modal = document.getElementById("modal");
        modal.style.display = "block";

    }

    // Close the modal form
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
      var caseids   = "";
      var casenames = "";

      console.log("main.php -- delete_selected_cases()");

      for (i=0; i<table[0].rows.length; i++) {
        // Get the cell items
        td = table[0].rows[i].getElementsByTagName('td');
      
        // If tag is not a 'td' tag then move on
        if (td.length == 0) {
          continue;
        }

        // Is this row checked?
        if (td[0].firstChild.checked == true) {
          caseids   = caseids + td[td.length-1].innerHTML + ",";
          casenames = casenames + td[1].innerHTML + ",";
        }

      }

      // If items were selected then continue to delete them
      if (caseids.length == 0) {
        return;
      }

      // Make sure the user is sure
      var txt;
      var rep = confirm("Are you sure you want to delete these cases?");
      if (rep == false) {
        alert("Delete Canceled!");
        return;
      }

      // Create the AJAX request
      var params  = "action=delete_cases&caseids=" + caseids + "&casenames=" + casenames;
      var request = new XMLHttpRequest();

      request.open("POST", "case_explorer.php", true);
      request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      request.setRequestHeader("Content-length", params.length);
      request.setRequestHeader("Connection", "close");

      // Callback function
      request.onreadystatechange = function()
      {

        if (this.readyState == 4 && this.status == 200) {

          // If server was unable to delete case(s) tell the user
          if (this.response === false) {
            alert("ERROR: Could not delete case(s).  Contact Administrator");
          }

          // Reload the page to reflect changes 
          reload_this_page();
        }        
      }

      request.send(params);
    }

    // Open selected cases
    function open_cases() {
    
      console.log("main.php -- open_cases()");

      var i;
      var j;
      var table = document.getElementsByClassName('case_explorer_table');
      var tr;
      var td;
      var params    = "";
      var caseids   = "";
      var casenames = "";
      
      // Assemble the case names and case id's to open
      for (i=0; i<table[0].rows.length; i++) {
        // Get the cell items
        td = table[0].rows[i].getElementsByTagName('td');
      
        // If tag is not a 'td' tag then move on
        if (td.length == 0) {
          continue;
        }
        
        // Is this row checked?
        if (td[0].firstChild.checked == true) {
          caseids   = td[td.length-1].innerHTML;
          casenames = td[1].innerHTML;
          break;
        }
      }
      
      // If items were selected then continue to delete them
      if (caseids.length == 0) {
        return;
      }

      // Otherwise since I did not implement the case/method functionality just send user to example case
      document.getElementById("methods_tab").click();
      document.getElementById("methods_tab").innerHTML = casenames;
      document.getElementById("methods_tab").style.display = "block";
    
    }

    // Logout from analysis session.  Here we warn the user and if they
    // want to log out, we tell the auto-scaling group to detach this
    // instance and then we will kill it
    function logout() {

      // Make sure the user is sure
      var txt;
      var rep = confirm("Are you sure you want to end your session?");
      if (rep == false) {
        alert("Ok.  Just checking, please continue :)");
        return;
      }

      // Redirect back to the login screen
      window.location.href = "http://" + "<?php echo $_SESSION['redirect_url'] ?>";

      // Send an AJAX request to server to handle the destruction of this instance
      var request = new XMLHttpRequest();

      request.open("GET", "logout.php?logout", true);
      request.send();

    }

    // By default set the case explorer as default screen
    document.getElementById("methods_tab").style.display = "none";
    document.getElementById("case_explorer_tab").click();

</script>