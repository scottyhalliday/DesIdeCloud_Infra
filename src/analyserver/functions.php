<?php
    
    require 'aws_php/vendor/autoload.php';

    use Aws\AutoScaling\AutoScalingClient;
    use Aws\Ec2\Ec2Client;
    use Aws\Rds\RdsClient;

    // AWS Region
    $aws_region = "us-east-1";

    // Database login info.  
    $dbuser     = "deside_admin";
    $dbpass     = "reset_this_password";
    //$dbserver   = "deside-cloud-mysql-db.cubk8axrpfzg.us-east-1.rds.amazonaws.com";
    $dbname     = "DesideCloud";

    // Get the RDS endpoint
    $rds = new RdsClient([
        'region' => $aws_region,
        'version' => 'latest'
    ]);

    $result = $rds->describeDBInstances([
        'DBClusterIdentifier' => 'deside-cloud-mysql-db'
    ]);

    $dbserver =  $result['DBInstances'][0]['Endpoint']['Address'];

    // Login to the database
    $conn = new mysqli($dbserver, $dbuser, $dbpass, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Close the connection to the database.  This is usually done when
    // a new analyserver webserver is created and user is redirected
    function close_db_connection() {
        global $conn;
        return $conn->close();
    }

    // Make sure that we prevent SQL injection
    function sanitize_string($var) {
        global $conn;
        $var = strip_tags($var);
        $var = htmlentities($var);
        $var = stripslashes($var);
        return $conn->real_escape_string($var);
    }

    // Submit a query to database and return result
    function query_msql($query) {
        global $conn;
        $result = $conn->query($query);
        if (!$result) die($conn->error);
        return $result;
    }

    // Submit a query to database which will insert results
    function insert_msql($query) {
        global $conn;
        $result = $conn->query($query);
        if (!$result) die($conn->error);
        $conn->commit();
        return $result;

    }

    // Get the user for this instance.  Recall the user is attached to the EC2 instance tag
    function get_user() {
        global $aws_region;

        // Get the instance id for this by using instance metadata
        $instance_id = file_get_contents('http://169.254.169.254/latest/meta-data/instance-id/');

        // Need an Ec2Client
        $ec2 = new Ec2Client([
            'region' => $aws_region,
            'version' => 'latest'
        ]);

        // Get the tags for this instance id
        $result = $ec2->describeTags([
            'Filters' => [
                [
                    'Name'   => 'resource-id',
                    'Values' => [$instance_id, ],
                ],
            ],
        ]);

        // Username is stored in the tags with key -- 'analyserver-owner'
        for ($i=0; $i<count($result); $i++) {
            if ($result['Tags'][$i]['Key'] == 'analyserver-owner') {
                $_SESSION['user'] = $result['Tags'][$i]['Value'];
                break;
            }
        }

    }
?>