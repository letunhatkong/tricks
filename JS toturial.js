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