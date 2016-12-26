
function init_map(){
    var myOptions = {
        zoom:16,
        center:new google.maps.LatLng(34.061563, -118.300718),
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };
    map = new google.maps.Map(document.getElementById("gmap_canvas"), myOptions);
    marker = new google.maps.Marker({
        map: map,
        position: new google.maps.LatLng(34.061563, -118.300718)
    });
    infowindow = new google.maps.InfoWindow({
        content:"<b>Tee &amp; Choi</b><br />123 sss dd,<br/> Ste. 321, <br/> Los Angeles, CA 123321"
    });
    google.maps.event.addListener(marker, "click", function(){
        infowindow.open(map,marker);
    });
    infowindow.open(map,marker);
}
google.maps.event.addDomListener(window, 'load', init_map);
