<?php
   header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
   header('Cache-Control: no-store, no-cache, must-revalidate');
   header('Cache-Control: post-check=0, pre-check=0', FALSE);
   header('Prama: no-cache');
   // the above headers will prevent the page output from being cached

   include ("dbConnect.php");
   $albumID=$_GET["id"];
   $dbQuery=$db->prepare("select id,title from tracks where albumID=:albumID order by trackNumber asc");
   $dbParams = array('albumID'=>$albumID);
   $dbQuery->execute($dbParams);
   echo $dbQuery->rowCount()."\n";
   while ($dbRow=$dbQuery->fetch(PDO::FETCH_ASSOC)) {
      echo $dbRow["id"]."_".$dbRow["title"]."\n";
   }  
?>