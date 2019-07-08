<?php
    
    session_start();

    echo "<html><head><link rel='stylesheet' href='main.css'></head>";

    require_once "functions.php";

    // Is user already logged in?
    if (isset($_SESSION['user'])) {
        echo "TODO: Direct user to the analysis webserver";
    } else {

        echo "<body>";

        echo "<ul class='topbar'>";
        echo "    <li class='topbar_item1'>Welcome to DesIde Cloud!</li>";
        echo "    <li class='topbar_item2'><a href=#getstarted>Get Started</a></li>";
        echo "</ul>";

        echo "<img src='DesideCloud.png' alt='DesIde Cloud Image' width='500' height='500' class='center'>";

        echo "<form action='authenticate.php' class='login_form' method='post'>";
        echo "    <label for='uname'><b>Username</b></label>";
        echo "    <input type='text' placeholder='Enter Username' name='uname' required>";

        echo "    <label for='psw'><b>Password</b></label>";
        echo "    <input type='password' placeholder='Enter Password' name='psw' required>";

        echo "    <button type='submit'>Login</button>";
        echo "</form>";

        echo "</div>";
        echo "</body>";
        echo "</html>";

    }

?>

