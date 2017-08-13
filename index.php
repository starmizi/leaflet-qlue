<?php header('Access-Control-Allow-Origin: *'); ?>
<html>
<head>	
	<title>Leaflet Qlue</title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="icon" href="img/qlue-favicon.png" sizes="32x32" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.2.0/dist/leaflet.css" integrity="sha512-M2wvCLH6DSRazYeZRIm1JnYyh22purTM+FDB5CsyxtQJYeKq83arPe5wgbNmcFXGqiSH2XR8dT/fJISVA1r/zQ==" crossorigin=""/>
    <script src="vendor/jquery/jquery-3.2.1.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.2.0/dist/leaflet.js" integrity="sha512-lInM/apFSqyy1o6s89K4iQUKg6ppXEgsVxT35HbzUupEVRh2Eu9Wdl4tHj7dZO0s1uvplcYGmt3498TtHq+log==" crossorigin=""></script>

	<style>
        html, body, #map {
            height: 100%;
        }
        body {
            padding: 0;
            margin: 0;
        }
	</style>

</head>

<body>
<div id='map'></div>

<script> 
	var mbAttr = 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, ' +
			     '<a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
			     'Imagery © <a href="http://mapbox.com">Mapbox</a>',
		mbUrl = 'https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw';

	var grayscale = L.tileLayer(mbUrl, {id: 'mapbox.light', attribution: mbAttr}),
		streets = L.tileLayer(mbUrl, {id: 'mapbox.streets',   attribution: mbAttr});

    var iconNameList = ['default', 'green', 'blue', 'black', 'blackyellow', 'blackred'];
    var qlueIcon = [];
    for (var i = 0; i < iconNameList.length; i++) {
        var iconUrl = 'img/icon-marker_' + iconNameList[i] + '.png'
        var qlueIconIndex = L.icon({
        iconUrl: iconUrl,
        iconSize: [32, 37],
        iconAnchor: [16, 37],
        popupAnchor: [0, -28]
        });
        qlueIcon.push(qlueIconIndex);
    }
/*
	var qlueIcon = L.icon({
		iconUrl: 'img/icon-marker.png',
		iconSize: [32, 37],
		iconAnchor: [16, 37],
		popupAnchor: [0, -28]
	});
    var qlueIcon0 = L.icon({
        iconUrl: 'img/icon-marker (0).png',
        iconSize: [32, 37],
        iconAnchor: [16, 37],
        popupAnchor: [0, -28]
    });
    var qlueIcon1 = L.icon({
        iconUrl: 'img/icon-marker (1).png',
        iconSize: [32, 37],
        iconAnchor: [16, 37],
        popupAnchor: [0, -28]
    });
    var qlueIcon2 = L.icon({
        iconUrl: 'img/icon-marker (2).png',
        iconSize: [32, 37],
        iconAnchor: [16, 37],
        popupAnchor: [0, -28]
    });
    var qlueIcon3 = L.icon({
        iconUrl: 'img/icon-marker (3).png',
        iconSize: [32, 37],
        iconAnchor: [16, 37],
        popupAnchor: [0, -28]
    });
    var qlueIcon4 = L.icon({
        iconUrl: 'img/icon-marker (4).png',
        iconSize: [32, 37],
        iconAnchor: [16, 37],
        popupAnchor: [0, -28]
    });
*/

    var jsonURL1 = "data/getDataExample.php";
	var jsonURL2 = "data/waze.json";

    var terminalBus = L.layerGroup();
    $.getJSON(jsonURL1, function(data) {
        $.each( data, function( key, val) {
    		var lat = val.lat;
    		var lng = val.lng;
            var marker = L.marker([lat, lng], {icon: qlueIcon[0]});

            var popup_content = L.popup()
              .setContent(
                  "<h3>" + val.name + "</h3>" +
                  "<p>LOKASI: " + val.address + "</p>"
              );
            marker.bindPopup(popup_content, {'className' : 'custom'});
            marker.on('mouseover', function (e) {
                this.openPopup();
            });
            marker.on('mouseout', function (e) {
                this.closePopup();
            });
            terminalBus.addLayer(marker);
        });
    });

    var waze = L.layerGroup();
    $.getJSON(jsonURL2, function(data) {
    	$.each( data, function(key, val) {
    		if (key == 'alerts') {
    			for (i=0; i < val.length; i++){
    		        var lat = val[i].location.y;
    		        var lng = val[i].location.x;
                    if (val[i].type == 'WEATHERHAZARD') {
                        var marker = L.marker([lat, lng], {icon: qlueIcon[2]});
                    } else if (val[i].type == 'JAM') {
                        var marker = L.marker([lat, lng], {icon: qlueIcon[3]});
                    } else if (val[i].type == 'ROAD_CLOSED') {
                        var marker = L.marker([lat, lng], {icon: qlueIcon[4]});
                    } else if (val[i].type == 'ACCIDENT') {
                        var marker = L.marker([lat, lng], {icon: qlueIcon[5]});
                    } else {
                        var marker = L.marker([lat, lng], {icon: qlueIcon[1]});
                    }
                    var popup_content = L.popup()
                      .setContent(
                          "<h3>" + val[i].subtype + "</h3>" +
                          "<p>LOKASI: " + val[i].street + "</p>"
                      );
                    marker.bindPopup(popup_content, {'className' : 'custom'});
                    marker.on('click', function (e) {
                        this.openPopup();
                    });
                    marker.on('mouseout', function (e) {
                        this.closePopup();
                    });
                    waze.addLayer(marker);
                }
            }
        })
    });

    var map = L.map('map', {
		center: [-6.2, 106.9],
		zoom: 11,
		layers: [streets, waze],
		zoomControl: true,
        attributionControl: false,
        fullscreenControl: true,
        defaultExtentControl: true
	});

	var baseLayers = {
		"Grayscale": grayscale,
		"Streets": streets
	};
	var overlays = {
		"Terminal Bus": terminalBus,
		"Laporan Waze": waze
	};
	L.control.layers(baseLayers, overlays).addTo(map);
</script>

</body>
</html>