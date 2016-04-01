<?php
	session_start();
	include("common.php");
	
	//~ $ip  = !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
	//~ $url = "http://freegeoip.net/json/$ip";
	//~ echo $url;
	//~ exit;
	
	
	
		
	$clat= $_POST["clat"];
	$clng= $_POST["clng"];
	
	$str="select id from rn_maincompany where name like 'Samsung'";
	$db->query($str);
	if($db->next_record())
	{
		$id=$db->f("id");
	}
	
	$i=0;
	$str="select * from rn_ticketuser where parentcmp=".$id . " limit 10";
	$db->query($str);
	
	//~ $str="select * from rn_ticketuser where parentcmp=1 limit 2";
	//~ $db->query($str);
	
	while($db->next_record())
	{		
		$order   = array("\r\n", "\n", "\r");
		$replace = " ";		
		$add=str_replace($order, $replace, $db->f("address"));
		$data_arr = geocode($add);
		
		if($data_arr)
		{
			$d = getDistance2($data_arr[0], $data_arr[1], $clat, $clng);			
			if(($d/1000) <= 600)
			{
				$arr[$i]["DisplayText"]=$db->f("cmpname");
				$arr[$i]["Address"]=$add;
				$arr[$i]["LatitudeLongitude"]= $data_arr[0] . ", " . $data_arr[1];
				$arr[$i]["MarkerId"]= "Company";
				$i++;			
			}	
		}
	}
	
	echo json_encode($arr);
	
	function getDistance2($f1, $f2, $t1, $t2)
	{	
		$url = "http://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $f1.",".$f2 . "&destinations=" . $t1.",".$t2;	
		$resp_json = file_get_contents($url);
	    $resp = json_decode($resp_json, true);
	    
	   	    
	    if($resp['status']=='OK')
	    {
	        $d = $resp['rows'][0]['elements'][0]['distance']['value'];
	    }	    
	    return $d;
	}

	function geocode($address)
	{	
	    // url encode the address
	  
	    $address = urlencode($address);
	    // google map geocode api url
	    $url = "http://maps.google.com/maps/api/geocode/json?address={$address}";	    
	    // get the json response
	    $resp_json = file_get_contents($url);
	    // decode the json
	    $resp = json_decode($resp_json, true);
	    // response status will be 'OK', if able to geocode given address 
	    if($resp['status']=='OK')
	    {
	        // get the important data
	        $lati = $resp['results'][0]['geometry']['location']['lat'];
	        $longi = $resp['results'][0]['geometry']['location']['lng'];
	        $formatted_address = $resp['results'][0]['formatted_address'];
	         
	        // verify if data is complete
	        if($lati && $longi && $formatted_address){
	         
	            // put the data in the array
	            $data_arr = array();            
	             
	            array_push(
	                $data_arr, 
	                    $lati, 
	                    $longi, 
	                    $formatted_address
	                );
	                
	                
	             	             
	            return $data_arr;
	             
	        }else
	        {
	            return false;
	        }
	    }
	    else
	    {
	        return false;
	    }
	}
    
    //~ function distance($lat1, $lon1, $lat2, $lon2, $unit) 
    //~ {
		//~ $theta = $lon1 - $lon2;
		//~ $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
		//~ $dist = acos($dist);
		//~ $dist = rad2deg($dist);
		//~ $miles = $dist * 60 * 1.1515;
		//~ $unit = strtoupper($unit);
//~ 
		//~ if ($unit == "K") 
		//~ {
			//~ return ($miles * 1.609344);
		//~ } 
		//~ else if ($unit == "N") 
		//~ {
			//~ return ($miles * 0.8684);
		//~ }
		//~ else 
		//~ {
				//~ return $miles;
		//~ }
	//~ }
	
	
	

?> 
