<script type="text/javascript">
    var map = null;
    var markers = [];
    var restaurants = [];
    var directionsDisplay;
    var directionsService;
    var lat;
    var lng;

    function initialize() {
        let url = "{{route('api.get.geolocation')}}";
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'JSON',
            success: function (response) {
                lat = response.items.lat;
                lng = response.items.lng;
                initMap();
            },
            error: function (response) {
                alert(response.msg);
                return;
            }
        });
    }

    function initMap() {
        // 載入路線服務與路線顯示圖層
        directionsService = new google.maps.DirectionsService();
        directionsDisplay = new google.maps.DirectionsRenderer();

        var options = {
            zoom: 14,
            center: {
                lat: lat,
                lng: lng
            }
        };

        map = new google.maps.Map(document.getElementById('map'), options);
        directionsDisplay.setMap(map);
        directionsDisplay.setPanel(document.getElementById('direction-panel'));
        getRestaurants();
    }

    function getRestaurants(keyword) {
        $('#query-title').html('');
        $('#cache-name').val('');
        let url = "{{route('api.get.restaurants')}}";
        $.ajax({
            url: url,
            type: 'GET',
            data: {keyword: keyword},
            dataType: 'JSON',
            success: function (response) {
                $('#query-title').html(response.query);
                $('#cache-name').val(response.cache_name);
                loadMarker(response);
                loadInfoToPanel(response);
                setRestaurants(response);
            },
            error: function (response) {
                alert(response.msg);
                return;
            }
        });
    }

    function setRestaurantMarker(index) {
        let result = restaurants[index];
        setMapOnAll(null);
        addMarker(result, true, index);
    }

    function loadMarker(e) {
        clearMarkers();
        markers = [];

        $.each(e.items, function (i, result) {
            addMarker(result, false, i);
        });
    }

    function loadInfoToPanel(e) {
        loadlist(e, '#panelContent');
    }

    function clearMarkers() {
        setMapOnAll(null);
        markers = [];
    }

    function setMapOnAll(map) {
        for (var i = 0; i < markers.length; i++) {
            markers[i].setMap(map);
        }
    }

    function addMarker(e, isOpen = false, index = 0) {
        let marker = new google.maps.Marker({
            position: e.geometry.location,
            map: map
        });

        let address_lat = e.geometry.location.lat;
        let address_lng = e.geometry.location.lng;

        let infoWindow = new google.maps.InfoWindow({
            content: '<div class="infoWindow"><b>' + e.name + '</b><br />' +
                e.formatted_address +
                '<br /><a href="#" onclick="calculateAndDisplayRoute(' + address_lat + ',' + address_lng + ')">規劃路線</a>' +
                '</div>'
        });

        marker.addListener('click', function () {
            infoWindow.open(map, marker);
        });

        markers.push(marker);

        if (isOpen == true) {
            infoWindow.open(map, marker);
        }
    }

    function loadlist(e, id) {
        let info = '';
        let index = 0;
        $(id).html('');
        $.each(e.items, function (i, result) {
            let address_lat = result.geometry.location.lat;
            let address_lng = result.geometry.location.lng;
            let place_id = result.place_id;
            let open_now;
            if(typeof result.opening_hours === 'undefined'){
                open_now = false;
            }else{
                open_now = result.opening_hours.open_now;
            }
            info = info + '<div id="panelContentDetail"><a href="#" onclick="setRestaurantMarker(' + index + ')"><b>' + result.name;
            if(open_now){
                info = info + '&nbsp;&nbsp;<small><a class="btn btn-danger btn-xs disabled" role="button">營業中</a></small>';
            }else{
                info = info + '&nbsp;&nbsp;<small><a class="btn btn-warning btn-xs disabled" role="button">非營業中</a></small>';
            }

            info = info +
                "</b></a><br />" + result.formatted_address +
                '<br /><a href="#" onclick="calculateAndDisplayRoute(' + address_lat + ',' + address_lng + ',' + index + ')"><small>規劃路線</small></a>' +
                '&nbsp;|&nbsp;<small><a href="#" onclick="showReview(\'' + place_id + '\')">查看統計</a></small>' +
                '&nbsp;|&nbsp;<small><a href="#" onclick="showDetail(\'' + place_id + '\')">更多相關資訊...</a></small>' +
                "</div>";
            index++;
        });
        $(id).html(info);
    }

    function setRestaurants(e) {
        restaurants = [];
        $.each(e.items, function (i, result) {
            restaurants.push(result);
        });
    }

    function showDetail(place_id) {
        $('#DetailWindow').dialog({
            width: 800,
            height: 500
        });

        $('#restaurantInfo').html('');
        let url = "{{route('api.get.restaurant.detail')}}";
        $.ajax({
            url: url,
            type: 'GET',
            data: {place_id: place_id},
            dataType: 'JSON',
            success: function (response) {
                let detail;
                let name = response.items.name;
                let address = response.items.formatted_address;
                let phone_number = response.items.formatted_phone_number;
                let rating = response.items.rating;
                detail = `<b>${name}</b><br><small>${address}</small>`;
                detail = detail + `<br><small>電話 : ${phone_number} </small>`;
                detail = detail + `<br><small>評分 : ${rating} </small>`;
                $('#restaurantInfo').html(detail);
            },
            error: function (response) {
                alert(response.msg);
                return;
            }
        });

        $('#restaurantWorkDay').html('');
        url = "{{route('api.get.restaurant.work.days')}}";
        $.ajax({
            url: url,
            type: 'GET',
            data: {place_id: place_id},
            dataType: 'JSON',
            success: function (response) {
                let detail;
                let day0 = response.items.day0;
                let day1 = response.items.day1;
                let day2 = response.items.day2;
                let day3 = response.items.day3;
                let day4 = response.items.day4;
                let day5 = response.items.day5;
                let day6 = response.items.day6;
                detail = `<small>營業時間 : </small>`;
                detail = detail + `<br><ul>`;
                detail = detail + `<small><li> 星期日 : ${day0} </li></small>`;
                detail = detail + `<small><li> 星期一 : ${day1} </li></small>`;
                detail = detail + `<small><li> 星期二 : ${day2} </li></small>`;
                detail = detail + `<small><li> 星期三 : ${day3} </li></small>`;
                detail = detail + `<small><li> 星期四 : ${day4} </li></small>`;
                detail = detail + `<small><li> 星期五 : ${day5} </li></small>`;
                detail = detail + `<small><li> 星期六 : ${day6} </li></small>`;
                detail = detail + `</ul>`;
                $('#restaurantWorkDay').html(detail);
            },
            error: function (response) {
                alert(response.msg);
                return;
            }
        });
    }

    function showReview(place_id) {
        $('#ReviewWindow').dialog({
            width: 800,
            height: 500
        });

        $('#restaurantReview').html('');
        let url = "{{route('api.get.restaurant.reviews')}}";
        $.ajax({
            url: url,
            type: 'GET',
            data: {place_id: place_id},
            dataType: 'JSON',
            success: function (response) {
                let reviews;
                for (i = 0; i < response.items.length; i++) {
                    let author_name = response.items[i].author_name;
                    let profile_photo_url = response.items[i].profile_photo_url;
                    let rating = response.items[i].rating;
                    let text = response.items[i].text;

                    let review_info = `<div id="panelContentDetail">
                        <table cellspacing="10" cellpadding='10px' width="100%">
                            <tr>
                                <td valign="top" width="25%" align="center"><img src="${profile_photo_url}"></td>
                                <td  valign="top">
                                    <table cellspacing="10">
                                        <tr>
                                            <td><b>${author_name}</b></td>
                                        </tr>
                                        <tr>
                                            <td><small>評分</small> : <b>${rating}</b></td>
                                        </tr>
                                        <tr>
                                            <td><small>${text}</small></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </div>`;
                    reviews = reviews + review_info;
                }
                $('#restaurantReview').html(reviews);
            },
            error: function (response) {
                alert(response.msg);
                return;
            }
        });
    }

    function showPanel() {
        $('#panelContent').hide();
        $('#specialContent').show();
    }

    function specialClose() {
        $('#specialContent').hide();
        $('#panelContent').show();
    }

    function specialSearch() {
        let keyword = $('#searchFood').val();
        getRestaurants(keyword);
        specialClose();
    }

    function sort(sort) {
        let cache_name = $('#cache-name').val();
        $('#panelContent').html('');
        let url = "{{route('api.get.restaurants.by.sort')}}";
        $.ajax({
            url: url,
            type: 'GET',
            data: {sort: sort, cache_name: cache_name},
            dataType: 'JSON',
            success: function (response) {
                let info = '';
                let index = 0;
                $.each(response.items, function (i, result) {
                    let address_lat = result.lat;
                    let address_lng = result.lng;
                    let place_id = result.place_id;

                    info = info + '<div id="panelContentDetail"><a href="#" onclick="setRestaurantMarker(' + index + ')"><b>' + result.name + "</b></a><br />" + result.formatted_address +
                        '<br /><a href="#" onclick="calculateAndDisplayRoute(' + address_lat + ',' + address_lng + ')"><small>獲取方向</small></a>' +
                        '&nbsp;|&nbsp;<small><a href="#" onclick="showReview(\'' + place_id + '\')">查看統計</a></small>' +
                        '&nbsp;|&nbsp;<small><a href="#" onclick="showDetail(\'' + place_id + '\')">更多相關資訊...</a></small>' +
                        "</div>";
                    index++;
                });
                $('#panelContent').html(info);
            },
            error: function (response) {
                alert(response.msg);
                return;
            }
        });
    }
    
    function calculateAndDisplayRoute(end_lat, end_lng) {

        // 初始化地圖
        var options = {
            zoom: 14,
            center: {
                lat: lat,
                lng: lng
            }
        };

        map = new google.maps.Map(document.getElementById('map'), options);

        // 放置路線圖層
        directionsDisplay.setMap(map);

        let markerA = new google.maps.Marker({
            position: {lat: lat, lng: lng},
            title: "point A",
            label: "A",
            map: map
        });
        let markerB = new google.maps.Marker({
            position: {lat: end_lat, lng: end_lng},
            title: "point B",
            label: "B",
            map: map
        });

        // 路線相關設定
        var request = {
            origin: {lat: lat, lng: lng},
            destination: {lat: end_lat, lng: end_lng},
            travelMode: 'DRIVING'
        };

        // 繪製路線
        directionsService.route(request, function (result, status) {
            console.log(status);
            if (status == 'OK') {
                // 回傳路線上每個步驟的細節
                console.log(result.routes[0].legs[0].steps);
                directionsDisplay.setDirections(result);
            } else {
                console.log(status);
            }
        });
    }
</script>
