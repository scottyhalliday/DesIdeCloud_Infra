<!-- authenticate.php
     Handles the authorization of a user
-->

<html>
<head><link rel='stylesheet' href='main.css'></head>
<body>
<?php

    require_once "functions.php";

    // Get the username and password entered
    $username = sanitize_string($_POST['uname']);
    $password = sanitize_string($_POST['psw']);

    // Check if the credentials are correct
    $result = query_msql("SELECT * FROM users WHERE username='$username' AND password='$password'");

    if ($result->num_rows == 0) {
        echo "Invalid username or password";
    } else {
        echo "<h1>Welcome $username</h1>";
        $_SESSION['user']     = $username;
        $_SESSION['password'] = $password;

        echo "<br>";
        echo "Please wait while we spin up your very own DesIde Cloud Instance ....";
        echo "<br>";
        echo "<br>";

        // Manually increment the desired count for the auto scaling group.
        // This will trigger a new EC2 instance to be created
        $asg=new_analyzer();
    }

?>

<?php
    $analysis_dns=poll_EC2_analyserver();
    sleep(10);
?>

<script>
    var analysis_dns = "http://" + "<?php echo $analysis_dns ?>";
    window.location.href = analysis_dns;
</script>

</body>
</html>