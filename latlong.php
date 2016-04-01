<?php
  $add="hazira,surat";
  $Address = urlencode($add);
  $request_url = "http://maps.googleapis.com/maps/api/geocode/xml?address=".$Address."&sensor=true";
  $xml = simplexml_load_file($request_url) or die("url not loading");
  $status = $xml->status;
  if ($status=="OK") {
      $Lat = $xml->result->geometry->location->lat;
      $Lon = $xml->result->geometry->location->lng;
      $LatLng = "$Lat,$Lon";
      
      
      echo $LatLng;
  }
?>
