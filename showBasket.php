<?php
  session_start();
  include ("dbConnect.php");
  
  if (!isset($_SESSION["currentUserID"])) 
     header("Location: login.php");
     
  if (isset($_POST["action"]) && $_POST["action"]=="deleteFromBasket") {
     $dbQuery=$db->prepare("delete from basket where id=:id");
     $dbParams = array('id'=>$_POST["purchaseID"]);
     $dbQuery->execute($dbParams);
  }
  
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
$dbQuery=$db->prepare("select tracks.title, albums.title, artists.name, basket.id ".
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

   <p>Click on the icon on the right of each row to remove the track from the basket</p>

   <table style="margin-left:15px">
   <tr><th>Title</th><th>Artist</th><th>Album</th><th>Price<th>&nbsp;</th></tr>

<?php
	//price of all tracks
	$price = 0.79;
	//counter to track how many tracks being bought
	$rowCounter = 0;
 
   while ($dbRow=$dbQuery->fetch(PDO::FETCH_NUM)) {
      echo "<tr><td>$dbRow[0]</td><td>$dbRow[2]</td><td>$dbRow[1]</td><td>$price</td>".
           "<td class=\"noborder\">".
           "<form class=\"inline\" method=\"post\" action=\"showBasket.php\">".
           "<input type=\"hidden\" name=\"action\" value=\"deleteFromBasket\">".
           "<input type=\"hidden\" name=\"purchaseID\" value=\"$dbRow[3]\">".	   
           "<input type=\"image\" src=\"delete.png\">".
			"</form></td></tr>";
			
			$rowCounter++;
   }
		
}
?>

</table>

<?php
//calculate the total cost of tracks
$totalSum = $rowCounter * 0.79; 
		
		//if more than 5 tracks apply a 15% discount to total price
		if($rowCounter > 5){
			$discount = 0.15 * $totalSum;
			$totalSum = $totalSum - $discount;
			echo "<h2>Your Discounted Price Total Comes To: ".$totalSum."<h2>";
		}else{
			echo "<h2>Your Total Price Comes To: ".$totalSum."<h2>";
		}
		
		?>
</body>

</html>

