<?php
  session_start();
  include("dbConnect.php");
  
?>

<html>

<head>
  <title>mp3 Shop</title>
    
  <style type="text/css">
  
      #artists,
      #albums,
      #tracks { padding:30px; float:left; }
  
      .bold { font-weight:bold }
      
      ul { list-style-type: none;
           padding-left: 0; margin-left: 0; }
           
  </style>
    
  <script type="text/javascript">
  
     var ajaxObject = getXmlHttpRequestObject();

     function getXmlHttpRequestObject() {
       if (window.XMLHttpRequest)
          return new XMLHttpRequest();
       else if (window.ActiveXObject) 
            return new ActiveXObject("Microsoft.XMLHTTP");
       else alert ("XMLHttp not supported by browser")
     }
       
     function listArtists(letter) {
       if (ajaxObject.readyState==4 || ajaxObject.readyState==0) {
          ajaxObject.open("GET","getArtistsByLetter.php?letter="+letter, true);
          ajaxObject.onreadystatechange=displayArtists;
          ajaxObject.send(null);
       }
     }

     function listAlbums(artistID) {
       if (ajaxObject.readyState==4 || ajaxObject.readyState==0) {
          ajaxObject.open("GET","getAlbumsByArtist.php?id="+artistID, true);
          ajaxObject.onreadystatechange=displayAlbums;
          ajaxObject.send(null);
       }
     }

     function listTracks(albumID) {
       if (ajaxObject.readyState==4 || ajaxObject.readyState==0) {
          ajaxObject.open("GET","getTracksByAlbum.php?id="+albumID, true);
          ajaxObject.onreadystatechange=displayTracks;
          ajaxObject.send(null);
       }
     }
     
     function displayArtists() { 
       if (ajaxObject.readyState==4) {
          var artistArray=ajaxObject.responseText.split("\n");
          var numArtists=artistArray[0];
          var htmlStr="<ul>";
          for(var i=1; i<=numArtists; i++) {
            artistDetails=artistArray[i].split("_");
            htmlStr+="<li><a href=\"#\" onclick=\"listAlbums("+
                     artistDetails[0]+")\">"+artistDetails[1]+"</a></li>";
          }
          htmlStr+="</ul>"; 
          document.getElementById("tracks").innerHTML="";
          document.getElementById("albums").innerHTML="";
          
          document.getElementById("artists").innerHTML=htmlStr;
          
       }
     }

     function displayAlbums() { 
       if (ajaxObject.readyState==4) {
          var albumArray=ajaxObject.responseText.split("\n");
          var numAlbums=albumArray[0];
          var htmlStr="<ul>";
          for(var i=1; i<=numAlbums; i++) {
            albumDetails=albumArray[i].split("_");
            htmlStr+="<li><a href=\"#\" onclick=\"listTracks("+
                     albumDetails[0]+")\">"+albumDetails[1]+"</a></li>";
          }
          htmlStr+="</ul>"; 
          document.getElementById("tracks").innerHTML="";
          document.getElementById("albums").innerHTML=htmlStr;
          
       }
     }

     function displayTracks() { 
       if (ajaxObject.readyState==4) { 
          var tracksArray=ajaxObject.responseText.split("\n");
          var numTracks=tracksArray[0];
          var htmlStr="<form method=\"post\" action=\"addToBasket.php\">";
          htmlStr+="<ul>";
                      
          for(var i=1; i<=numTracks; i++) { 
            trackDetails=tracksArray[i].split("_"); 
            htmlStr+="<li><input type=\"checkbox\" name=\"tracks[]\" value=\""+trackDetails[0]+"\"> "+trackDetails[1]+"</li>";
          }
          htmlStr+="</ul>";
          htmlStr+="<input type=\"submit\" value=\"Add selected tracks to basket\">";
          htmlStr+="</form>"; 
          document.getElementById("tracks").innerHTML=htmlStr;
       }
     }
     
  </script>
 
</head>

<body>

<h1>mp3 Shop</h1>
<hr>


<?php if (isset($_SESSION["currentUserID"])) { ?>
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
<?php } else { ?>
   <a href="login.php">Login</a>
<?php } ?>

<hr>

<?php
   include ("dbConnect.php");
   $letters="abcdefghijklmnopqrstuvwxyz0123456789";
   echo "<div class=\"bigMargin\">";
   echo "<span class=\"bold\">Choose artist: </span>";
   for ($i=0; $i<=35; $i++) {
      $letter=substr($letters,$i,1);
      $initial="$letter"."%";
      $dbQuery=$db->prepare("select * from artists where name like :initial order by name asc");
      $dbParams = array('initial'=>$initial);
      $dbQuery->execute($dbParams);
      if ($dbQuery->rowCount()>0)
         echo "<a href=\"#\" onclick=\"listArtists('$letter')\">$letter</a> ";
      else echo "$letter ";   
   }  
   echo "</div>";

?>
   <div id="artists"></div>
   
   <div id="albums"></div>
   
   <div id="tracks"></div>

</body>

</html>

