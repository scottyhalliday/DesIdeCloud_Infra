<?php
    
    require 'aws_php/vendor/autoload.php';

    use Aws\AutoScaling\AutoScalingClient;
    use Aws\Ec2\Ec2Client;
    use Aws\Rds\RdsClient;
    use Aws\S3\S3Client;

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
        //if (!$result) die($conn->error);
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

    // Get the user's S3 bucket and key information
    function get_s3_info() {
        $query_str1  = "SELECT s3bucket FROM users where username='" . $_SESSION['user'] . "';";
        $query_str2  = "SELECT s3key    FROM users where username='" . $_SESSION['user'] . "';";

        $s3bucket = query_msql($query_str1);
        $s3key    = query_msql($query_str2);

        if ($s3bucket->num_rows > 0) {
            $row = mysqli_fetch_array($s3bucket);
            $_SESSION['s3bucket'] = $row['s3bucket'];
            error_log("Using AWS S3 Bucket for case saving ::: " . $_SESSION['s3bucket']);
        }

        if ($s3key->num_rows > 0) {
            $row = mysqli_fetch_array($s3key);
            $_SESSION['s3key'] = $row['s3key'];
            error_log("Using AWS S3 Key for case saving ::: " . $_SESSION['s3key']);
        }
    }

    // Copy file from temporary directory to s3 bucket
    function cp_to_s3($s3bucket, $s3key, $sourcefile) {
        // s3bucket   : The S3 bucket the file will be placed
        // s3key      : The unique key for the file name to be placed in bucket
        // sourcefile : The location of the local file to copy
        
        global $aws_region;

        error_log('functions.php -- cp_tmp_to_s3()');

        try {
            $s3 = new S3Client([
                'region' => $aws_region,
                'version' => 'latest'
            ]);

            $result = $s3->putObject([
                'Bucket' => $s3bucket,
                'Key'    => $s3key,
                'SourceFile' => $sourcefile,
            ]);

        } catch (S3Exception $e) {
            error_log($e->getMessage());
        }
    }

    // Copy file from temporary directory to s3 bucket
    function delete_s3_object($s3bucket, $s3key) {
        // s3bucket   : The S3 bucket the object exists
        // s3key      : The unique key for the file name to be deleted
        
        global $aws_region;

        error_log('functions.php -- cp_tmp_to_s3()');

        try {
            $s3 = new S3Client([
                'region' => $aws_region,
                'version' => 'latest'
            ]);

            $result = $s3->deleteObject([
                'Bucket' => $s3bucket,
                'Key'    => $s3key,
            ]);

        } catch (S3Exception $e) {
            error_log($e->getMessage());
        }
    }

    // Download case file content from S3 bucket
    function read_s3_case_object($s3bucket, $s3key) {
        global $aws_region;

        error_log('functions.php -- cp_tmp_to_s3()');

        try {
            $s3 = new S3Client([
                'region' => $aws_region,
                'version' => 'latest'
            ]);

            $result = $s3->getObject([
                'Bucket' => $s3bucket,
                'Key'    => $s3key,
            ]);

            return $result['Body'];

        } catch (S3Exception $e) {
            error_log($e->getMessage());
        }
    }

?>