<?php session_start(); ?>
<html>
<head><title>Arshad's MP1 Project</title>
<style type="text/css">
body{
	background:url('http://orig13.deviantart.net/8755/f/2008/106/9/3/matrix_wallpaper_by_awenare.jpg');
	margin:0;
	padding:0;
	}
#formWrapper{
	width:550px;
	background:#AFC8DE;
	margin:2em auto 0 auto;
	padding: 2em 0 2em 0;
	border: solid 5px #F1F1F1; 
	}
form{
	width: 500px;
	margin: 0 auto 0 auto;
	}
label{display: block;
	margin: 0.3em 0 0.3em 2em;
	font-family:Gotham, "Helvetica Neue", Helvetica, Arial, sans-serif;
	color:#FC1216;
	font-size:1em;
}
input{
	width:150px;
	
	}
textarea{
	width: 150px;
	
	}
fieldset{border: none;
background:#F1F1F1;
padding: 0 0 2em 0;
 }	
 fieldset.first{
	background: #F1F1F1; 
	 }
.labelOne{margin-top:1em}
form h4{
	margin :1em 0 1.5em 6em;
	font-size:1.3em;
	font-family:Gotham, "Helvetica Neue", Helvetica, Arial, sans-serif;
	color: #9F282A;
	font-weight:bold;
	
	}
</style>
</head>
<body>

<!-- The data encoding type, enctype, MUST be specified as below -->
<div id="formWrapper">
<form enctype="multipart/form-data" action="result.php" method="POST">
	<fieldset class="first">
    <h4>Arshad's Web Application</h4>
     
     <!-- MAX_FILE_SIZE must precede the file input field -->
    <input type="hidden" name="MAX_FILE_SIZE" value="3000000" />
    <!-- Name of input element determines name in $_FILES array -->
   <label for="userfile"> Upload the file: <input name="userfile" type="file" /></label><br />
<label class="labelOne" for="email">
Enter Email of user: <input type="email" name="email"></label><br />
<label for="phone">
Enter Phone of user (1-XXX-XXX-XXXX): <input type="phone" name="phone"></label>
	</fieldset>

<input type="submit" value="Send File" />
</form>
<hr />
<!-- The data encoding type, enctype, MUST be specified as below -->
<form enctype="multipart/form-data" action="gallery.php" method="POST">
<label for="email">    
Enter Email of user for gallery to browse: <input type="email" name="email"></label>
<input type="submit" value="Load Gallery" />
</form>
</div>


</body>
</html>














