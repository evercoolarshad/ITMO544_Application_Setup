<?php
// Start the session
session_start();
ob_start();
// In PHP versions earlier than 4.1.0, $HTTP_POST_FILES should be used instead
// of $_FILES.
require 'vendor/autoload.php';
use Aws\S3\S3Client;
use Aws\Sns\SnsClient;
echo $_POST['email'];
echo $_POST['phone'];
$uploaddir = '/tmp/';
$uploadfile = $uploaddir . basename($_FILES['userfile']['name']);
echo '<pre>';
if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
    echo "File is valid, and was successfully uploaded.\n";

} else {
    echo "Possible file upload attack!\n";
}
echo 'Here is some more debugging info:';
print_r($_FILES);
print "</pre>";
$s3 = new Aws\S3\S3Client([
    'version' => 'latest',
    'region'  => 'us-east-1'
]);
$bucket = uniqid("php-ars-test-bucket-",false);
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
    'Key' => "Hello".$uploadfile,
    'ContentType' => $_FILES['userfile']['tmp_name'],
    'Body'   => fopen($uploadfile, 'r+')
]);  
$url = $result['ObjectURL'];
echo $url;



$result = $s3->getObject(array(
    'Bucket' => $bucket,
    'Key' => "Hello".$uploadfile,
    'ContentType' => $_FILES['userfile']['tmp_name'],
    'SaveAs' => '/tmp/originalimage.jpg'
));

$image= new Imagick(glob('/tmp/originalimage.jpg'));
$image-> thumbnailImage(50,0);//Distorts the image
$image->setImageFormat ("jpg");
$image-> writeImages('/tmp/modifiedimage.jpg',true);

$modifiedbucket = uniqid("modified-image-",false);

$result = $s3->createBucket([
'ACL' => 'public-read',
'Bucket' => $modifiedbucket,
]);
$resultrendered = $s3->putObject([
    'ACL' => 'public-read',
    'Bucket' => $modifiedbucket,
    'Key' => "Hello".$uploadfile,
    'SourceFile' => "/tmp/modifiedimage.jpg",
    'ContentType' => $_FILES['userfile']['tmp_name'],
    'Body'   => fopen("/tmp/modifiedimage.jpg", 'r+')
]);  
//Eliminate the variable s3rendered locally
unlink('/tmp/modifiedimage.jpg');
$finishedurl = $resultrendered['ObjectURL'];
echo $finishedurl;



$rds = new Aws\Rds\RdsClient([
    'version' => 'latest',
    'region'  => 'us-east-1'
]);
$result = $rds->describeDBInstances(['DBInstanceIdentifier' => 'mp1-db',]);
$endpoint = $result['DBInstances'][0]['Endpoint']['Address'];
 print "============\n". $endpoint . "================";
print_r($result);
# Database connection
$link = mysqli_connect($endpoint,"controller","letmein888","customerrecords",3306) or die("Error " . mysqli_error($link));
# Check database connection 
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
echo "Connection to database correct ";
# Inserting data int the database
/* Prepared statement, stage 1: prepare */
if (!($stmt = $link->prepare("INSERT INTO arshadsTable (ID, email,phone,filename,s3rawurl,s3finishedurl,state,date) VALUES (NULL,?,?,?,?,?,?,?)"))) {
    echo "Prepare failed: (" . $link->errno . ") " . $link->error;
}
$email = $_POST['email'];
$phone = $_POST['phone'];
$s3rawurl = $url; 
$filename = basename($_FILES['userfile']['name']);
$s3finishedurl = $finishedurl;
$status =0;
$date='2015-11-10 12:00:00';
$stmt->bind_param("sssssii",$email,$phone,$filename,$s3rawurl,$s3finishedurl,$state,$date);
if (!$stmt->execute()) {
    echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
}
printf("%d Row inserted.\n", $stmt->affected_rows);
/* explicit close recommended */
$stmt->close();
$link->real_query("SELECT * FROM arshadsTable");
$res = $link->use_result();
echo "Result set order...\n";
while ($row = $res->fetch_assoc()) {
    echo $row['ID'] . " " . $row['email']. " " . $row['phone'];
}
$sns = new Aws\Sns\SnsClient([
'version' => 'latest',
'region' => 'us-east-1'
]);

$result = $sns->createTopic([
'Name'=>'My-New-SNS-topic',
]);

$topicArn = $result['TopicArn'];
echo "Topic ARN is ::: $topicArn";

$result = $sns->setTopicAttributes([
'AttributeName'=>'DisplayName',
'AttributeValue'=>'MP2-SNS-TOPIC',
'TopicArn'=>$topicArn,
]);

$result = $sns->subscribe([
'Endpoint'=>$email,
'Protocol'=>'email',
'TopicArn'=>$topicArn,
]);

$result = $sns->publish([
'TopicArn' => $topicArn,
'Subject' => 'Image uploaded',
'Message' => 'Congratulations! Your image has been successfully uploaded',
]);


$link->close();

header('Location: gallery.php');
exit;

?>
