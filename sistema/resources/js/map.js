
$(document).ready(function(){
		
		var myLatlng = new google.maps.LatLng(-34.9427238,-57.9465829);
		var mapOptions = {
		  center: myLatlng,
		  zoom: 15,
		  mapTypeId: google.maps.MapTypeId.ROADMAP,
		  styles: [{featureType:"landscape",stylers:[{saturation:-100},{lightness:65},{visibility:"on"}]},{featureType:"poi",stylers:[{saturation:-100},{lightness:51},{visibility:"simplified"}]},{featureType:"road.highway",stylers:[{saturation:-100},
				{visibility:"simplified"}]},{featureType:"road.arterial",stylers:[{saturation:-100},{lightness:30},{visibility:"on"}]},{featureType:"road.local",stylers:[{saturation:-100},{lightness:40},{visibility:"on"}]},{featureType:"transit",stylers:[{saturation:-100},
				{visibility:"simplified"}]},{featureType:"administrative.province",stylers:[{visibility:"off"}]/**/},{featureType:"administrative.locality",stylers:[{visibility:"off"}]},{featureType:"administrative.neighborhood",stylers:[{visibility:"on"}
				]/**/},{featureType:"water",elementType:"labels",stylers:[{visibility:"on"},{lightness:-25},{saturation:-100}]},{featureType:"water",elementType:"geometry",stylers:[{hue:"#ffff00"},{lightness:-25},{saturation:-97}]}]
			};
		var map = new google.maps.Map(document.getElementById("mapa"),mapOptions);
		var marker = new google.maps.Marker({
			position: myLatlng,
			map: map,
			icon: 'images/map_place.png',
		});
		$(window).resize(function(){
			var center = map.getCenter();
			google.maps.event.trigger(map, "resize");
			map.setCenter(center); 
	    });
	});