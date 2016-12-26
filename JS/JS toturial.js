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


