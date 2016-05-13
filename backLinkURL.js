/**
 * Created by kongltn on 12/25/2015.
 */

$(document).ready(function () {
    $("#backButtonInHeaderBar").click(function () {
        if (sessionStorage.backURL !== undefined) {
            sessionStorage.backIsClick = 1;
            var curArray = JSON.parse(sessionStorage.backURL);
            if (curArray.length > 1) curArray.pop();
            sessionStorage.backURL = JSON.stringify(curArray);
            window.location.replace(curArray[curArray.length - 1]);
        }
    });

    if (!sessionStorage.backURL || sessionStorage.backURL === undefined) {
        sessionStorage.backURL = JSON.stringify([window.location.origin]);
    }

    var curUrlHref = window.location.href;
    if (sessionStorage.backIsClick && sessionStorage.backIsClick === "1") {
        sessionStorage.backIsClick = 0;
    } else {
        var urlArray = JSON.parse(sessionStorage.backURL);
        if (curUrlHref != urlArray[urlArray.length - 1]) {
            urlArray.push(curUrlHref);
        }
        sessionStorage.backURL = JSON.stringify(urlArray);
    }

    //console.log(sessionStorage.backURL);
    //console.log(sessionStorage.backIsClick);
});