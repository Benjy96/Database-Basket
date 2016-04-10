<?php
  session_start();
  include ("dbConnect.php");
  
  if (!isset($_SESSION["currentUserID"])) 
     header("Location: login.php");

  $dbQuery=$db->prepare("delete from basket where userID=:userID");
  $dbParams = array('userID'=>$_SESSION["currentUserID"]);
  $dbQuery->execute($dbParams);

?>

<html>
<head>
  <title>mp3 Shop</title>    
</head>

<body>

<h1>mp3 Shop</h1>
<hr>

<a href="login.php">Logout <?php echo $_SESSION["currentUser"]; ?></a> | 
<a href="shopForTracks.php">Shop for tracks</a> 

<hr>

<h2>Thank you for your order</h2>

<p>You will receive an email with a link from which you can download your
purchased tracks</p>

<p>Please use one of the menu options above to log out or continue shopping</p>

</body>

</html>

