<?php
  session_start();
  include ("dbConnect.php");
  
  if (!isset($_SESSION["currentUserID"])) 
     header("Location: login.php");

     
  if (isset($_POST["action"]) && $_POST["action"]=="storeDetails") {
     $name=$_POST["name"]; $email=$_POST["email"]; $street=$_POST["street"]; $town=$_POST["town"];
     $county=$_POST["county"]; $country=$_POST["country"]; $postcode=$_POST["postcode"];
     $cardType=$_POST["cardType"]; $cardNumber=$_POST["cardNumber"];
     $cardHolder=$_POST["cardHolder"]; 
     $validFrom=$_POST["validFromMonth"]."/".$_POST["validFromYear"];
     $validTo=$_POST["validToMonth"]."/".$_POST["validToYear"];
     $cv2=$_POST["cv2"]; $issueNumber=$_POST["issueNumber"];

     $dbQuery=$db->prepare("select id from paymentDetails where userID=:userID");
     $dbParams = array('userID'=>$_SESSION["currentUserID"]);  
     $dbQuery->execute($dbParams);
     if ($dbQuery->rowCount()==0) 
        $dbQuery=$db->prepare("insert into paymentDetails values (null,:userID ".
                 ",:name,:email,:street,:town,:county,:country,:postcode ".
                 ",:cardType,:cardNumber,:cardHolder,:validFrom,:validTo ".
                 ",:cv2,:issueNumber)");
     else
        $dbQuery=$db->prepare("update paymentDetails set name=:name,email=:email,street=:street ".
                 ",town=:town,county=:county,country=:country,postcode=:postcode".
                 ",cardType=:cardType,cardNumber=:cardNumber,cardHolder=:cardHolder".
                 ",validFrom=:validFrom,validTo=:validTo,cv2=:cv2,issueNumber=:issueNumber ".
                 "where userID=:userID");
     $dbParams = array('userID'=>$_SESSION["currentUserID"], 'name'=>$name, 'email'=>$email,
                       'street'=>$street, 'town'=>$town, 'county'=>$county, 'country'=>$country,
                       'postcode'=>$postcode, 'cardType'=>$cardType, 'cardNumber'=>$cardNumber,
                       'cardHolder'=>$cardHolder, 'validFrom'=>$validFrom, 'validTo'=>$validTo,
                       'cv2'=>$cv2, 'issueNumber'=>$issueNumber);            
     $dbQuery->execute($dbParams);
     header("Location: checkout.php"); 
  }
  
else {

?>
<html>
<head>
  <title>mp3 Shop</title>
  
  <style type="text/css">
     .spaceBelow { margin-bottom:15px; }
  </style>
    
</head>

<body>

<h1>mp3 Shop</h1>
<hr>

<a href="login.php">Logout <?php echo $_SESSION["currentUser"]; ?></a> | 
<a href="shopForTracks.php">Shop for tracks</a> |
<a href="checkout.php">Checkout</a>

<hr>

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
   $validFromMonth=substr($validFrom,0,2); $validFromYear=substr($validFrom,-4);
   $validToMonth=substr($validTo,0,2); $validToYear=substr($validTo,-4);
} else {
   $name=""; $email=""; $street=""; $town="";
   $county=""; $country=""; $postcode="";
   $cardType=""; $cardNumber="";
   $cardHolder=""; $validFrom=""; $validTo="";
   $cv2=""; $issueNumber="";   
   $validFromMonth=""; $validFromYear="";
   $validToMonth=""; $validToYear="";
}

?>

<form method="post" action="paymentAndShipping.php">

<span style="float:left; margin-left:15px;">

<h2>Delivery Address</h2>

<div class="spaceBelow">Name<br>
<input type="text" name="name" size="30" value="<?php echo $name; ?>"></div>

<div class="spaceBelow">Email<br>
<input type="text" name="email" size="30" value="<?php echo $email; ?>"></div>

<div class="spaceBelow">Street<br>
<input type="text" name="street" size="30" value="<?php echo $street; ?>"></div>

<div class="spaceBelow">Town<br>
<input type="text" name="town" size="30" value="<?php echo $town; ?>"></div>

<div class="spaceBelow">County<br>
<input type="text" name="county" size="30" value="<?php echo $county; ?>"></div>

<div class="spaceBelow">Country<br>
<input type="text" name="country" size="30" value="<?php echo $country; ?>"></div>

<div class="spaceBelow">Postcode<br>
<input type="text" name="postcode" size="30" value="<?php echo $postcode; ?>"></div>

</span>

<span style="float:left; margin-left:50px;">

<h2>Payment Information</h2>

<div class="spaceBelow">Card Type<br>
<input type="radio" name="cardType" value="Mastercard"
   <?php if ($cardType=="" || $cardType=="Mastercard") echo " checked"; ?>> Mastercard
<input type="radio" name="cardType" value="Visa"
   <?php if ($cardType=="Visa") echo " checked"; ?>> Visa
<input type="radio" name="cardType" value="Switch"
   <?php if ($cardType=="Switch") echo " checked"; ?>> Switch
<input type="radio" name="cardType" value="Solo"
   <?php if ($cardType=="Solo") echo " checked"; ?>> Solo
<input type="radio" name="cardType" value="Maestro"
   <?php if ($cardType=="Maestro") echo " checked"; ?>> Maestro</div>
   
<div class="spaceBelow">Card Number<br>
<input type="text" name="cardNumber" size="30" value="<?php echo $cardNumber; ?>"></div>

<div class="spaceBelow">Card Holder<br>
<input type="text" name="cardHolder" size="30" value="<?php echo $cardHolder; ?>"></div>

<div class="spaceBelow">Valid From<br>
<select name="validFromMonth">
<?php
   for ($i=1; $i<=12; $i++) {
     if ($i<10) $month="0$i"; else $month="$i";
     if ($validFromMonth==$month) $selectedStr="selected"; else $selectedStr="";
     echo "<option value=\"$month\" $selectedStr > $month </option>";
   }  
 ?>
</select>

<select name="validFromYear">
<?php
   for ($year=2010; $year<=2015; $year++) {
     if ($validFromYear==$year) $selectedStr="selected"; else $selectedStr="";
     echo "<option value=\"$year\" $selectedStr > $year </option>";
   }  
 ?>
</select>
</div>

<div class="spaceBelow">Valid To<br>
<select name="validToMonth">
<?php
   for ($i=1; $i<=12; $i++) {
     if ($i<10) $month="0$i"; else $month="$i";
     if ($validToMonth==$month) $selectedStr="selected"; else $selectedStr="";
     echo "<option value=\"$month\" $selectedStr > $month </option>";
   }  
 ?>
</select>

<select name="validToYear">
<?php
   for ($year=2015; $year<=2020; $year++) {
     if ($validToYear==$year) $selectedStr="selected"; else $selectedStr="";
     echo "<option value=\"$year\" $selectedStr > $year </option>";
   }  
 ?>
</select>
</div>

<div class="spaceBelow">CV2 (Security code)<br>
<input type="text" name="cv2" size="30" value="<?php echo $cv2; ?>"></div>

<div class="spaceBelow">Issue Number (Switch or Delta cards only)<br>
<input type="text" name="issueNumber" size="30" value="<?php echo $issueNumber; ?>"></div>

</span>

<div style="clear:left; margin-left:15px;">
<input type="hidden" name="action" value="storeDetails">
<input type="submit" value="Confirm details">
</div>

</form>

</body>

</html>

<?php
}
?>
