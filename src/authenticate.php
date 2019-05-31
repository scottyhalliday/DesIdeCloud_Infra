<!-- authenticate.php
     Handles the authorization of a user
-->

<html>
<body>
<?php

    require_once "functions.php";

    // Get the username and password entered
    $username = sanitize_string($_POST[uname]);
    $password = sanitize_string($_POST[psw]);

    // Check if the credentials are correct
    $result = query_msql("SELECT * FROM users WHERE username='$username' AND password='$password'");

    if ($result->num_rows == 0) {
        echo "Invalid username or password";
    } else {
        echo "Welcome $username";
        $_SESSION['user']     = $username;
        $_SESSION['password'] = $password;
    }

?>
</body>
</html>