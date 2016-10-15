function getDuration( fromId, toId, fromLat, fromLon, toLat, toLon) {

	if (GBrowserIsCompatible()) {
		var wp = new Array ();
		wp[0] = new GLatLng(fromLat, fromLon);
		wp[1] = new GLatLng(toLat, toLon);
		directions = new GDirections();
		directions.loadFromWaypoints(wp);  

		GEvent.addListener(directions, "load", function() {
			var duration = directions.getDuration();
			if(duration != null) {
				$('#result').append( duration.seconds );
			}
		});
	}
}