<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
    <script src="http://code.jquery.com/jquery-1.10.2.min.js" type="text/javascript"></script>
    <script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyA7IZt-36CgqSGDFK8pChUdQXFyKIhpMBY&sensor=true" type="text/javascript"></script>
    <script type="text/javascript">

        var map;
        var geocoder;
        var marker;
        var people = new Array();
        var latlng;
        var infowindow;
        var flag=false;
        var currentlat;
        var currentlng;
        var flag2;
        
        $(document).ready(function() 
        {
						
			if (navigator.geolocation)
			{
				navigator.geolocation.getCurrentPosition(function(position)
				{
					var pos = 
					{
						lat: position.coords.latitude,
						lng: position.coords.longitude
					};
					
					currentlat=pos.lat;
					currentlng=pos.lng;
					flag2=true;
					alert(pos.lat);
					alert(pos.lng);
							
				}, function() 
				{
					handleLocationError(true, infoWindow, map.getCenter());
				});
			}
			else 
			{
				// Browser doesn't support Geolocation
				handleLocationError(false, infoWindow, map.getCenter());
			}
			
			function handleLocationError(browserHasGeolocation, infoWindow, pos) 
			{
                alert ('Error: The Geolocation service failed.')
			}
			
			setTimeout(getService, 4000);	
			
			function getService()
			{
				if(flag2==true)
				{
					document.getElementById("map-canvas").innerHTML="Loading.....";
					
					$.ajax
					({
						url: "ws_address.php",
						type:"post",
						async: false,
						data : "clat="+ currentlat + "&clng=" + currentlng,
						success: function (data) 
						{
							 alert(data);
							 people = JSON.parse(data); 
							 //alert(people.length);
							flag=true;
							//~ alert(flag);
						},
						error: function()
						{
								alert('here');	
						}
					})
					
					if(flag==true)
					{
						ViewCustInGoogleMap();
					}
					else
					{
						document.getElementById("map-canvas").innerHTML="Loading.....";
					}
				}
				else
				{
					alert("wait");
				}
			}
        });

        function ViewCustInGoogleMap() {
			
            var mapOptions = {
                center: new google.maps.LatLng(currentlat, currentlng),   // Coimbatore = (11.0168445, 76.9558321)
                zoom: 4,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };
            
            map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);
            
			var infoWindow = new google.maps.InfoWindow({map: map});
			
			
			geocoder = new google.maps.Geocoder();
            infowindow = new google.maps.InfoWindow();
            
			latlng = new google.maps.LatLng(currentlat, currentlng);
                marker = new google.maps.Marker({
                    position: latlng,
                    map: map,
                    draggable: false,               // cant drag it
                    html: "current location",    // Content display on marker click
                    icon : 'http://maps.google.com/mapfiles/ms/icons/green-dot.png'                        
                 
                });
                
                google.maps.event.addListener(marker, 'click', function(event) {
                    infowindow.setContent(this.html);
                    infowindow.setPosition(event.latLng);
                    infowindow.open(map, this);
                });
			
			
			
			

			/* for current location 
			// Try HTML5 geolocation.
			if (navigator.geolocation)
			{
				navigator.geolocation.getCurrentPosition(function(position)
				{
					var pos = 
					{
						lat: position.coords.latitude,
						lng: position.coords.longitude
					};
					
					//~ alert(pos.lat);
					//~ alert(pos.lng);
					infoWindow.setPosition(pos);
					infoWindow.setContent('Location found.');
					map.setCenter(pos);
				}, function() 
				{
					handleLocationError(true, infoWindow, map.getCenter());
				});
			}
			else {
			// Browser doesn't support Geolocation
			handleLocationError(false, infoWindow, map.getCenter());
			}

			function handleLocationError(browserHasGeolocation, infoWindow, pos) 
			{
				infoWindow.setPosition(pos);
				infoWindow.setContent(browserHasGeolocation ?
                          'Error: The Geolocation service failed.' :
                          'Error: Your browser doesn\'t support geolocation.');
			}
			 end for current location  */
			
           // Get data from database. It should be like below format or you can alter it.       
           // var data = '[{ "DisplayText": "Anjali", "ADDRESS": "Adajan, Surat", "LatitudeLongitude": "21.1941,72.7981", "MarkerId": "Customer" },{ "DisplayText": "Dinal", "ADDRESS": "Navsari, Gujarat", "LatitudeLongitude": "20.9519, 72.9215", "MarkerId": "Customer"}]';
           if(people!= null)
           {
				for (var i = 0; i < people.length; i++) 
				{
					setMarker(people[i]);
				}
			}
        }
      
        function setMarker(people) {
            geocoder = new google.maps.Geocoder();
            infowindow = new google.maps.InfoWindow();
            if 
            ((people["LatitudeLongitude"] == null) || (people["LatitudeLongitude"] == 'null') || (people["LatitudeLongitude"] == '')) {
                geocoder.geocode({ 'address': people["Address"] }, function(results, status) 
                {
                    if (status == google.maps.GeocoderStatus.OK) {
                        latlng = new google.maps.LatLng(results[0].geometry.location.lat(), results[0].geometry.location.lng());
                        marker = new google.maps.Marker({
                            position: latlng,
                            map: map,
                            draggable: false,
                            html: people["DisplayText"],
                            icon : 'http://maps.google.com/mapfiles/ms/icons/red-dot.png'                       
                        });
                        google.maps.event.addListener(marker, 'click', function(event) {
                            infowindow.setContent(this.html);
                            infowindow.setPosition(event.latLng);
                            infowindow.open(map, this);
                        });
                    }
                    else 
                    {
                        alert(people["DisplayText"] + " -- " + people["Address"] + ". This address couldn't be found");
                    }
                });
            }
            else 
            {
                var latlngStr = people["LatitudeLongitude"].split(",");
                var lat = parseFloat(latlngStr[0]);
                var lng = parseFloat(latlngStr[1]);
                latlng = new google.maps.LatLng(lat, lng);
                marker = new google.maps.Marker({
                    position: latlng,
                    map: map,
                    draggable: false,               // cant drag it
                    html: people["DisplayText"],    // Content display on marker click
                    icon : 'http://maps.google.com/mapfiles/ms/icons/red-dot.png'                       
                 
                });
                
                google.maps.event.addListener(marker, 'click', function(event) {
                    infowindow.setContent(this.html);
                    infowindow.setPosition(event.latLng);
                    infowindow.open(map, this);
                });
            }
        }

    </script>
</head>
<body>
    <div id="map-canvas" style="width: 800px; height: 600px;">
    </div>
</body>
</html>
