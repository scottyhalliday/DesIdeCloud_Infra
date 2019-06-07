<?php

    require 'aws_php/vendor/autoload.php';

    use Aws\AutoScaling\AutoScalingClient;
    use Aws\Ec2\Ec2Client;

    // AWS Region
    $aws_region = "us-east-1";

    // Database login info.  
    $dbuser     = "deside_admin";
    $dbpass     = "reset_this_password";
    $dbserver   = "deside-cloud-mysql-db.cubk8axrpfzg.us-east-1.rds.amazonaws.com";
    $dbname     = "DesideCloud";

    // Login to the database
    $conn = new mysqli($dbserver, $dbuser, $dbpass, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
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

    // Spin up an analyzer instance
    function new_analyzer() {
        
        global $aws_region;

        // Get the autoscaling client
        $asg = new AutoScalingClient([
            'region' => $aws_region,
            'version' => 'latest'
        ]);
        
        // Get the analyzer auto-scaling group
        $result = $asg->describeAutoScalingGroups([]);

        // Get the number of autoscaling groups 
        $asg_count = count($result['AutoScalingGroups']);

        // Loop through each ASG and see if it is the analyserver ASG
        for ($i=0; $i<$asg_count; $i++) {
            $asg_name = $result['AutoScalingGroups'][$i]['AutoScalingGroupName'];
            
            // Check if this autoscaling group is associated with analyserver
            if (strpos($asg_name, "analyserver") !== false) {
                echo "Autoscaling Group <b>" . $asg_name . "</b> has been found ...<br>";
            } else {
                continue;
            }

            // Get the desired capacity
            $desired_cap = $result['AutoScalingGroups'][$i]['DesiredCapacity'];
            echo "Current ASG desired capacity is <b>" . $desired_cap . "</b>.  New capacity will be <b>" . $desired_cap+1 ."</b><br>";

            // Increment the desired capacity
            $result = $asg->setDesiredCapacity([
                'AutoScalingGroupName' => $asg_name,
                'DesiredCapacity' => $desired_cap+1
            ]);

        }

        // Get the current desired count
        //var_dump($result);

        return $asg;
    }

    // Poll EC2 instances for newly created Analyserver from ASG
    function poll_EC2_analyserver() {

        global $aws_region;

        // Need the EC2 client
        $ec2 = new Ec2Client([
            'region' => $aws_region,
            'version' => 'latest'
        ]);

        // Continue polling AWS for a new EC2 instance (2 minutes is the max)
        $found_instance = false;
        $ec2_instance_dns = "none";
        for ($i=0; $i<20; $i++) {

            // Sleep for 5 seconds
            sleep(5);

            $result = $ec2->describeInstances([
                'Filters' => [
                    [
                        'Name'   => 'tag:Name',
                        'Values' => ['*-asg-analyserver'],
                    ],
                    [
                        'Name'   => 'instance-state-name',
                        'Values' => ['running'],
                    ]
                ]
            ]);

            // Check if we have a valid instance
            for ($j=0; $j<count($result['Reservations']); $j++) {

                // Get the instance
                $ec2_instance = $result['Reservations'][$j]['Instances'][$j];

                // Get the tags
                $ec2_tags = $ec2_instance['Tags'];

                // Check the tags to see if this instance is owned
                $instance_owned=false;
                for ($k=0; $k<count($ec2_tags); $k++) {

                    // If this tag is set then this instance is already owned
                    if ($ec2_tags[$k]['Key'] == 'analyserver-owner') {
                        $instance_owned = true;
                        break;
                    }
                }

                if ($instance_owned == false) {
                    $ec2_instance_dns = $ec2_instance['PublicDnsName'];
                    return $ec2_instance_dns;
                }

            }

        }

        return $ec2_instance_dns;
    }


?>

