<?php
session_start();

require 'vendor/autoload.php';

# Creating a client for the s3 bucket
use Aws\Rds\RdsClient;
$client = new Aws\Rds\RdsClient([
 'version' => 'latest',
 'region'  => 'us-east-1'
]);

$result = $client->describeDBInstances([
    'DBInstanceIdentifier' => 'mp1-db',
]);

$endpoint = "";
$endpoint = $result['DBInstances'][0]['Endpoint']['Address'];

# Connecting to the database
$link = mysqli_connect($endpoint,"controller","letmein888","customerrecords") or die("Error " . mysqli_error($link));

/* Checking the database connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

$uploaddir = '/tmp/';

$backup = uniqid("dbname",false);

$bkFile=$uploaddir.$backup. '.' . 'sql';

echo $bkFile;

$dbusername="controller";
$dbpass="letmein888";

$dumpCommand="mysqldump --user=$dbusername --password=$dbpass --host=$endpoint customerrecords > $bkFile";
echo $dumpCommand;

exec($dumpCommand);

$s3 = new Aws\S3\S3Client([
    'version' => 'latest',
    'region'  => 'us-east-1'
]);

$bucket = uniqid("backup",false);

# AWS PHP SDK version 3 create bucket
$result = $s3->createBucket([
    'ACL' => 'public-read',
    'Bucket' => $bucket
]);

$s3->waitUntil('BucketExists', array( 'Bucket'=> $bucket));

# PHP version 3
$result = $s3->putObject([
    'ACL' => 'public-read',
    'Bucket' => $bucket,
    'Key' => $bkFile,
    'SourceFile' => $bkFile,
]);

echo "Successully backed up the database and stored in the S3 Bucket!";

?>




