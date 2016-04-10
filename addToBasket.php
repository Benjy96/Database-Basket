<?php
   session_start();
   //when user has been directed via login screen need to use session variable to get tracks
   if (isset($_SESSION["tracklist"])) {
	   //explode breaks a string into an array, we're using ^ as separator
	   //LIMIT SET TO 1
      $tracks=explode("^",$_SESSION["tracklist"], 1);
	  //clears session variable for next time - if you dont clear it, it will add track selected from last unlogged time
      unset($_SESSION["tracklist"]);
   } else {
	   //when user has previously logged in, tracks retrieved from post array
      $tracks=$_POST["tracks"];
   }

   //checks if logged in
   if (isset($_SESSION["currentUserID"])) {
      include ("dbConnect.php");
      $userID=$_SESSION["currentUserID"];
	  
	  //counter for check
		  $trackLimiter = 1;
		  
      foreach ($tracks as $thisTrackID) {		  
		  //check number of tracks being added when logged in
		  if($trackLimiter < 6){
          $dbQuery=$db->prepare("insert into basket values (null, :userID, :thisTrackID)");
          $dbParams = array('userID'=>$userID, 'thisTrackID'=>$thisTrackID);
          $dbQuery->execute($dbParams);
		  $trackLimiter++;
		  }
      }
      header("Location: shopForTracks.php"); 
	 //if you havent been logged in, stores tracklist as session variable
   } else {
      $_SESSION["tracklist"]="";
      foreach ($tracks as $thisTrackID) {
         $_SESSION["tracklist"].=$thisTrackID."^";
      }
	  //rtrim removes ^ from very end / format: rtrim(string $str, [string $character_mask]) - char mask = specified char
      $_SESSION["tracklist"]=rtrim($_SESSION["tracklist"],"^");
      header("Location: login.php");
   }   
?>