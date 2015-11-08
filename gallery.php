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
# Selecting everything that the database with the name jgldata contains
#$link->real_query("SELECT * FROM arshadsTable WHERE email = '$email'");
$link->real_query("SELECT * FROM arshadsTable");
$res = $link->use_result();
?>


<!DOCTYPE html>
<html lang="de">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<title>Gallery</title>
	<link href='http://fonts.googleapis.com/css?family=Slabo+27px' rel='stylesheet' type='text/css'>
	<link href='../dist/simplelightbox.min.css' rel='stylesheet' type='text/css'>
	<link href='demo.css' rel='stylesheet' type='text/css'>
</head>
<body>
	<div class="container">
		<h1 class="align-center">Arshad's Gallery</h1>
		<h2 class="align-center"> 
		<?php
		$email = $_POST["email"];
		echo $email;
		?>
		</h2>


		<div class="gallery">	
			<?php
               
               		while ($row = $res->fetch_assoc()) 
               		{
               		echo '<a href="'. $row['s3rawurl'] .'" title="'. $row['filename'] .'" data-gallery ><img src="' . $row['s3rawurl'] . '" width="100" height="100" ></a>';  
                  	}
               		$link->close();
                         
              		?>			
			
									
		</div>
	
		<br><br>
			
	
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script type="text/javascript" src="../dist/simple-lightbox.js"></script>
<script>
	$(function(){
		var gallery = $('.gallery a').simpleLightbox();
	});
</script>
</body>
</html>	