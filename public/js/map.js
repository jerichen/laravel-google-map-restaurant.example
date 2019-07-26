var map = null;
var markers = [];
var circles = [];
var restaurants = [];
var places = [];
var directionsDisplay;
var directionsService;
var currentIndex = 0;

var banqiao = {
    lat: 25.0130994,
    lng: 121.4605689
};
var myCurrentLocation;

function initMap() {
    directionsDisplay = new google.maps.DirectionsRenderer;
    directionsService = new google.maps.DirectionsService;
    var options = {
        zoom: 14,
        center: banqiao
    };
    map = new google.maps.Map(document.getElementById('map'), options);
    directionsDisplay.setMap(map);
    directionsDisplay.setPanel(document.getElementById('direction-panel'));
    getRestaurants();

    map.addListener('zoom_changed', function() {

        if(map.zoomDoNotSearch==true)
        {
            map.zoomDoNotSearch = false;
            return false;
        }

        let zoomLevel = map.getZoom();
        let center = map.getCenter();
        let lat = center.lat();
        let lng = center.lng();
        let latLng = {
            lat: lat,
            lng: lng
        };

        let radius = 2;
        if(zoomLevel == 15) radius = 1;
        else if(zoomLevel == 16) radius = 0.8;
        else if(zoomLevel == 17) radius = 0.6;
        else if(zoomLevel == 18) radius = 0.4;
        else if(zoomLevel <= 14) radius = 2;
        else radius = 0.2;

        radius = (radius / 6378.1) * 6378100;
        clearCircles(null);
        searchByRadius(lat,lng,radius);

    });

    function getRestaurants() {
        $.getJSON("{{route('get.restaurants')}}", function (e) {

            loadMarker(e);
            loadInfoToPanel(e);
            setRestaurants(e);
        });
    }
}
