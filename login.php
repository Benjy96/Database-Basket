<?php
   session_start();
   
   unset($_SESSION["currentUser"]);
   unset($_SESSION["currentUserID"]);

   if (isset($_POST["action"]) && $_POST["action"]=="login") {

      $formUser=$_POST["username"];
      $formPass=$_POST["password"];

      include("dbConnect.php");
      $dbQuery=$db->prepare("select * from users where username=:formUser"); 
      $dbParams = array('formUser'=>$formUser);
      $dbQuery->execute($dbParams);
      $dbRow=$dbQuery->fetch(PDO::FETCH_ASSOC);
      if ($dbRow["username"]==$formUser) {       
         if ($dbRow["password"]==$formPass) {
            $_SESSION["currentUser"]=$formUser;
            $_SESSION["currentUserID"]=$dbRow["id"];
			
			//checks if tracklist exists (tracklist declared in addToBasket)
            if (isset($_SESSION["tracklist"])) 
                 header("Location: addToBasket.php");
            else header("Location: shopForTracks.php");    
         }
         else {
            header("Location: login.php?failCode=2");
         }
      } else {
            header("Location: login.php?failCode=1");
      }

   } else {

?>
<html>

<head>
<title>mp3 Shop</title>
</head>
<body>

<h1>mp3 Shop</h1>
<hr>

<h2>Login</h2>

<?php
   if (isset($_GET["failCode"])) {
      if ($_GET["failCode"]==1)
         echo "<h3>Bad username entered</h3>";
      if ($_GET["failCode"]==2)
         echo "<h3>Bad password entered</h3>";
   }      
?>         

<form name="login" method="post" action="login.php">
  <div>Username<br>
  <input type="text" name="username"></div>

  <div>Password<br>
  <input type="text" name="password"></div>

  <div>
  <input type="hidden" name="action" value="login">
  <input type="submit" value="Login"></div>
  
</form>

<div style="margin-top:20px;">
<a href="shopForTracks.php">Browse the collection of tracks without logging in</a>
</div>

</body>

</html>

<?php
}
?>
