<html>
<head><title>Gallery</title>
</head>
<h1>Welcome to Arshad's gallery</h1>
<!-- // reference -http://fotorama.io/set-up/ -->
<!-- 1. Link to jQuery (1.8 or later), -->
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script> <!-- 33 KB -->

<!-- fotorama.css & fotorama.js. -->
<link  href="http://cdnjs.cloudflare.com/ajax/libs/fotorama/4.6.4/fotorama.css" rel="stylesheet"> <!-- 3 KB -->
<script src="http://cdnjs.cloudflare.com/ajax/libs/fotorama/4.6.4/fotorama.js"></script> <!-- 16 KB -->

<!-- 2. Add images to <div class="fotorama"></div>. -->
<div class="fotorama">

<!-- 3. Enjoy! -->

<body>

 
<?php
session_start();
$email = $_POST["email"];
echo $email;
require 'vendor/autoload.php';
 
use Aws\Rds\RdsClient;
$client = new Aws\Rds\RdsClient([
'region'  => 'us-east-1',
'version'=>'latest',
]);
 
$result = $client->describeDBInstances(array(
    'DBInstanceIdentifier' => 'mp1-db',
));
 
$endpoint = $result['DBInstances'][0]['Endpoint']['Address'];;
 
//echo "begin database";
$link = mysqli_connect($endpoint,"controller","letmein888","customerrecords",3306) or die("Error " . mysqli_error($link));
 
/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
//below line is unsafe - $email is not checked for SQL injection -- don't do this in real life or use an ORM instead
//$link->real_query("SELECT * FROM arshadsTable WHERE email = '$email'");
$link->real_query("SELECT * FROM arshadsTable");
$res = $link->use_result();
echo "Result set order...\n";
while ($row = $res->fetch_assoc()) {
    echo "<img src =\" " . $row['s3rawurl'] . "\" /><img src =\"" .$row['s3finishedurl'] . "\"/>";
echo $row['id'] . "Email: " . $row['email'];
}

echo "successfully executed";
$link->close();
?>


</div>
</body>
</html>




