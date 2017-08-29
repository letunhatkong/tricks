//How to get current url in javasript and jquery
//http://www.test.com:8082/index.php#tab2?foo=123
//1. Javascript
window.location.host;                   //www.test.com:8082
window.location.hostname;          // www.test.com
window.location.port;           // 8082
window.location.protocol;         //  http
window.location.pathname;         //  index.php
window.location.href;         //  http://www.test.com:8082/index.php#tab2
window.location.hash;        //   #tab2
window.location.search;        //   ?foo=123

//2. Jquery
$(location).attr('host');            //  www.test.com:8082
$(location).attr('hostname');       //   www.test.com
$(location).attr('port');           //   8082
$(location).attr('protocol');       //   http
$(location).attr('pathname');       //   index.php
$(location).attr('href');           //   http://www.test.com:8082/index.php#tab2
$(location).attr('hash');          //    #tab2
$(location).attr('search');        //    ?foo=123

// Get value by Param in url
function getUrlParam(name){
    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
    if (!results) return false;
    return results[1] || 0;
}

// similar behavior as an HTTP redirect
window.location.replace("http://stackoverflow.com");
// similar behavior as clicking on a link
window.location.href = "http://stackoverflow.com";

// Toggle css menu
$('.itemMenuFt .titFt').click(function(){
    var subMenu = $(this).parent().find('.subMenuFt');
    subMenu.toggle();
    if (subMenu.is(":visible")) {
        $(this).addClass("open");
    } else {
        $(this).removeClass("open");
    }
});
if (selectCt.is(':visible')) {
    $(this).text("+");
    selectCt.slideUp('normal');
} else {
    $(this).text("-");
    selectCt.slideDown('normal');
}


// Click outside to hidden menu
$('html').click(function() {
  //Hide the menus if visible
});
$('#menucontainer').click(function(event){
    event.stopPropagation();
});


// Lam tron so
var iNum = 5.123456;
iNum.toPrecision(5);   // Returns 5.1235
iNum.toPrecision(2);   // Returns 5.1
iNum.toPrecision(1);   // Returns 5

Number(Math.round(1.005+'e2')+'e-2'); // 1.01
Number(Math.round(1.005 * 100) / 100); // 1.01 - 2 chu so thi tuong ung voi 100


// Convert Object to Array
array = $.map(dataObj, function (value, index) {
    value.key = index;
    return [value];
});


// Print pdf
var win = window.open("http://tradebanner.com/admin/opicmsppdfgenerator/order/printpdf/template_id/9/order_id/30/key/3b65c4548507c7575bb00a206c4d62481808fe942873f8cd410ef1a16f002913/"); window.print();
// win.document.write('Some string')
win.print();
win.close();


// Check object exists in array
function checkObjInArray(obj, list) {
    var i;
    for (i = 0; i < list.length; i++) {
        if ( JSON.stringify(list[i]) == JSON.stringify(obj) ) {
            return true;
        }
    }
    return false;
}

// Check when scroll to bottom of page
$(window).scroll(function (event) {
    isEndOfPage = ($(window).scrollTop() + $(window).height() == $(document).height());
});


// Convert img to svg
$('img.svg').each(function(){
    var $img = $(this);
    var imgID = $img.attr('id');
    var imgClass = $img.attr('class');
    var imgURL = $img.attr('src');
    $.get(imgURL, function(data) {
        // Get the SVG tag, ignore the rest
        var $svg = $(data).find('svg');
        // Add replaced image's ID to the new SVG
        if(typeof imgID !== 'undefined') {
            $svg = $svg.attr('id', imgID);
        }
        // Add replaced image's classes to the new SVG
        if(typeof imgClass !== 'undefined') {
            $svg = $svg.attr('class', imgClass+' replaced-svg');
        }
        // Remove any invalid XML tags as per http://validator.w3.org
        $svg = $svg.removeAttr('xmlns:a');
        // Check if the viewport is set, if the viewport is not set the SVG wont't scale.
        if(!$svg.attr('viewBox') && $svg.attr('height') && $svg.attr('width')) {
            $svg.attr('viewBox', '0 0 ' + $svg.attr('height') + ' ' + $svg.attr('width'));
        }
        // Replace image with new SVG
        $img.replaceWith($svg);
    }, 'xml');
});

// Validate email
function validateEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}