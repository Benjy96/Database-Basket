<?php
  session_start();
  include ("dbConnect.php");
  
  if (!isset($_SESSION["currentUserID"])) 
     header("Location: login.php");

?>

<html>
<head>
  <title>mp3 Shop</title>
  
  <style type="text/css">
     th, td { font-family: verdana; font-size:10pt; text-align:left }
     table { border-collapse:collapse }
     th { padding:5px }
     td { border:solid 1px #000; padding:5px;  }
     td.noborder { border:0px; font-weight:bold; text-align:right; padding:5px }
     .inline { display:inline; }
  </style>
    
</head>

<body>

<h1>mp3 Shop</h1>
<hr>

<a href="login.php">Logout <?php echo $_SESSION["currentUser"]; ?></a> | 
<a href="shopForTracks.php">Shop for tracks</a> |
<a href="showBasket.php">Show Basket</a>
<?php
   //set userID to session ID so SQL knows who is logged in for query 
		$userID=$_SESSION["currentUserID"];
	//query to count number of tracks in basket
		$dbQuery=$db->prepare(
				"select * from basket
				 where userID=:userID");
		 //next we create array for params of execute() function
		 //worth noting that you don't need a key in associative array beneath since we only row count
		$dbParams = array('userID'=>$userID);	
		//parameters in execute are an array of values as declared above
		$dbQuery->execute($dbParams);	
		echo "(".$dbQuery->rowCount().")";
	
   ?> |
<a href="checkout.php">Checkout</a>

<hr>


<?php

$dbQuery=$db->prepare("select tracks.title, albums.title, artists.name ".
                      "from basket,tracks,albums,artists ".
                      "where basket.userID=:userID ".
                      "and basket.trackID=tracks.id ".
                      "and albums.id=tracks.albumID ".
                      "and artists.id=tracks.artistID");
$dbParams = array('userID'=>$_SESSION["currentUserID"]);                      
$dbQuery->execute($dbParams);
$numTracks=$dbQuery->rowCount();

if ($numTracks==0)
   echo "<h3>Your basket is empty</h3>";
else {

?>

   <h2>Your Basket</h2>

   <table style="margin-left:15px;">
   <tr><th>Title</th><th>Artist</th><th>Album</th></tr>

<?php

   while ($dbRow=$dbQuery->fetch(PDO::FETCH_NUM)) {
      echo "<tr><td>$dbRow[0]</td><td>$dbRow[2]</td><td>$dbRow[1]</td></tr>";
   }

?>

   </table>

   <div style="margin:15px">
   <a href="showBasket.php">Click here to modify the contents of your basket</a>
   </div>

<?php 

   $dbQuery=$db->prepare("select * from paymentDetails where userID=:userID");
   $dbParams = array('userID'=>$_SESSION["currentUserID"]);
   $dbQuery->execute($dbParams);
   if ($dbRow=$dbQuery->fetch(PDO::FETCH_ASSOC)) {
      $name=$dbRow["name"]; $email=$dbRow["email"]; $street=$dbRow["street"]; $town=$dbRow["town"];
      $county=$dbRow["county"]; $country=$dbRow["country"]; $postcode=$dbRow["postcode"];
      $cardType=$dbRow["cardType"]; $cardNumber=$dbRow["cardNumber"];
      $cardHolder=$dbRow["cardHolder"]; $validFrom=$dbRow["validFrom"]; $validTo=$dbRow["validTo"];
      $cv2=$dbRow["cv2"]; $issueNumber=$dbRow["issueNumber"];

      echo "<h3>Shipping Address</h3>";
      echo "<div style=\"margin-left:15px\">".
           "$name, $street, $town, $county, $country, $postcode<br>".
           "Email: $email</div>";
        
      echo "<h3>Payment Information</h3>";
      echo "<div style=\"margin-left:15px\">".
           "$cardType, expires $validTo</div>";
           
      echo "<div style=\"margin-top:15px;\">".
           "<a href=\"paymentAndShipping.php\">Click here to change payment and shipping details</a>";
   
      echo "<form method=\"post\" action=\"processOrder.php\" style=\"margin-top:20px\">".
           "<input type=\"submit\" value=\"Click here to complete the order\">".
           "</form>";
   }
   else {
      echo "<div style=\"margin-top:15px; margin-left:15px\">".
           "No payment and shipping details available<br>".
           "<a href=\"paymentAndShipping.php\">Click here to provide information</a></div>";
   }
}

?>

</body>

</html>

