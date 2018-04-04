(function(){

	var mapCanvas       = document.getElementById('gmap-canvas'),
		addedMarkers    = [],
		mapStyles,
		map;

	var mapElms = {

		ADDRESS_BTN: $('#get-map-address'),
		ADDRESS: $('#map_address'),
		TITLE: $('#map_heading'),
		MAP_LAT: $('#map_latitude'),
		MAP_LNG: $('#map_longitude'),
		MAP_ZOOM_LEVEL: $('#map_zoom_level'),
		MAP_MARKER_LAT: $('#map_marker_latitude'),
		MAP_MARKER_LNG: $('#map_marker_longitude'),
		MAP_STYLES: $('#map_styles')

	};

	function renderMap() {

		mapStyles = $.parseJSON(getElmVal('MAP_STYLES'));

		map = new google.maps.Map(mapCanvas, {
            center: {lat: parseFloat(getElmVal('MAP_LAT')), lng: parseFloat(getElmVal('MAP_LNG'))},
            zoom: parseInt(getElmVal('MAP_ZOOM_LEVEL')),
            styles:mapStyles,
            scrollwheel:false,
            draggable: true,
            mapTypeControl: false,
            zoomControlOptions: {
                position: google.maps.ControlPosition.LEFT_TOP
            },
            scaleControl: false,
            streetViewControl: false,
        });

	}


	function addMapMarker() {

		

	}

	function updateCenter() {

		

	}


	function updateZoomLvl() {

		

	}


	function refreshMap() {
		
	}

	function getElmVal(prop) {

		if( mapElms.hasOwnProperty(prop) ) {

			if( mapElms[prop].length == 1 && typeof mapElms[prop] === 'object' ) {

				return mapElms[prop].val();

			}

		}

		return '';

	}


	function init() {
		renderMap();
		addMapMarker();

	}




	if( mapCanvas ) {
		

		init();

	}

}());