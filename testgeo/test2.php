<?php

/* extra regeltje */


  /**
   * See http://www.tutorialized.com/tutorials/PHP/Redirection/1
   * 
   * Original code from svn://hostip.info/hostip/api/trunk. Optimized & enhanced by Quang Pham @ Saoma, 06.01.07.
   */   
  function isPrivateIP($ip) {
    list($a, $b, $c, $d) = sscanf($ip, "%d.%d.%d.%d");
    return  $a === null || $b === null || $c === null || $d === null ||
            $a == 10    ||
            $a == 239   ||
            $a == 0     ||
            $a == 127   ||
           ($a == 172 && $b >= 16 && $b <= 31) ||
           ($a == 192 && $b == 168);
  }   
   
  function getIP() {
    $default = false;
    
    if (isset($_SERVER)) {
      $default_ip = $_SERVER["REMOTE_ADDR"];      
      $xforwarded_ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
      $client_ip = $_SERVER["HTTP_CLIENT_IP"];    
    } else {
      $default_ip = getenv('REMOTE_ADDR');
      $xforwarded_ip = getenv('HTTP_X_FORWARDED_FOR');
      $client_ip = getenv('HTTP_CLIENT_IP');
    }
    
    if ($xforwarded_ip != "") {
      $result = $xforwarded_ip;
    } else if ($client_ip != "") {
      $result = $client_ip;
    } else {
      $default = true;
    }
    
    if (!$default) { // additional check for private ip numbers 
      $default = isPrivateIP($result);
    }
    
    if ($default) {
      $result = $default_ip;
    }
    
    return $result;
  }
  
  function getCountry() {
    // make a valid request to the hostip.info API  
    $url = "http://api.hostip.info/country.php?ip=".getIP();
  
    // fetch with curl
    $ch = curl_init();
  
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $country = curl_exec($ch);
  
    curl_close ($ch);
  
    $lwcountry = strtolower($country);
    
    return $lwcountry;
  }
?>


<html>
<head>
<style>
#infoframe {
	border: 1px solid black;
	/*width: 440px;*/
	width: 468px;
	height: 400px; 
}
</style>
<body>
	     <?php
	     	
	     	$c = getCountry();
	     	echo "<br>Country is: $c <br>";
	       if ($c=="nl" || $c=="es" || $c=="be" || $c=="pl" || $c=="de" || $c=="ch") 
	       	$country = $c;
	       else 
	        $country = "nl";
	      
	       $agenda = "../agenda/agenda_" . $country . ".html";
	      ?> 
<hr>
Including agenda for country <?php echo "$country" ?>
<hr>


 
<iframe name="infoframe" align="middle" scrolling="auto" id="infoframe"></iframe>

<script>
  document.getElementById("infoframe").src = "<?php echo $agenda ?>"
</script>
 
</body>
</html>
 
 
