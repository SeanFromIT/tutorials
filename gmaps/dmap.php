<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
	<meta name="viewport" content="initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0"/>
    <title>My Map</title>
    <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=YOUR_API_KEY_HERE"
      type="text/javascript"></script>
  </head>
  <body onunload="GUnload()">
    <div id="map" style="position:absolute;top:0px;bottom:0px;left:0;right:0;"></div>
	<script type="text/javascript">
    //<![CDATA[
	
	var map;
	var latlngbounds;
      if (GBrowserIsCompatible()) {
	  
		function createMarker(point, address) {
			var marker = new GMarker(point);
			var html = address;
			GEvent.addListener(marker, 'click', function() {
				marker.openInfoWindowHtml(html);
			});
			return marker;
		}
		
		function extendBounding(point) {
			latlngbounds.extend(point);
			var zoom = map.getBoundsZoomLevel(latlngbounds);
			//a zoom of 0 will break bounding.
			if (zoom < 10) {
				zoom = 12;
			}
			map.setCenter(latlngbounds.getCenter(), zoom);
		}
	  
        map = new GMap2(document.getElementById("map"));
        map.addControl(new GLargeMapControl3D());
        map.addControl(new GMapTypeControl());
        map.setCenter(new GLatLng(0, 0), 12);
		latlngbounds = new GLatLngBounds();
		
        GDownloadUrl("genxml.php", function(data) {
          var xml = GXml.parse(data);
          var markers = xml.documentElement.getElementsByTagName("marker");
          for (var i = 0; i < markers.length; i++) {
            var address = markers[i].getAttribute("address");
            var point = new GLatLng(parseFloat(markers[i].getAttribute("lat")),
                                    parseFloat(markers[i].getAttribute("lng")));
			var marker = createMarker(point, address);
            map.addOverlay(marker);
			extendBounding(point);
          }
        });
      }

    //]]>
    </script>
  </body>
</html>