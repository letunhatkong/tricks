/**
 * Created by DELL on 5/13/2016.
 */

/**
 * Javascript for convert time (using moment JS)
 * @author UTC.KongLtn
 * Last Update: 4/12/2015
 */


var clientTimeId = {
    getArchiveClientTime: ".getArchiveClientTime"
};

/**
 * Client Time Object
 * @type {{init: Function, convertInArchive: Function, toArchiveTime: Function, getDiffTime: Function, getDateYYMMDD: Function}}
 */
var clientTime = {
    init: function(){
        this.convertInArchive();
    },
    /**
     * Convert to client time in archive page
     */
    convertInArchive : function(){
        var archiveArray = $(clientTimeId.getArchiveClientTime);
        $.each(archiveArray, function(index, val) {
            var createDateToUTCSeconds = moment.utc(val.textContent).unix();
            $(this).text(clientTime.getArchiveTime(createDateToUTCSeconds));
        })
    },
    /**
     * Get client Time in archive page
     * @param seconds
     * @returns string
     */
    getArchiveTime : function(seconds){
        var createDate = moment.utc(seconds * 1000);
        var nowDate = moment.utc();
        var diff = nowDate.diff(createDate, "minutes");
        var firstText = "";

        if (diff < 1440) {
            firstText = "Today ";
        } else if (diff < 2880) {
            firstText = "Yesterday ";
        } else if (diff >= 2880) {
            return (createDate.local().format("DD.MM.YYYY HH:mm"));
        }
        return firstText+ " "+ createDate.local().format("HH:mm");
    },
    /**
     * Get the different past time and current time
     * @param seconds
     * @returns string
     */
    getDiffTime: function(seconds) {
        var createDate = moment.utc(seconds * 1000);
        var nowDate = moment.utc();
        var diff = nowDate.diff(createDate, "seconds");
        diff = (diff == 0 || diff == 1) ? 2 : diff;
        if (2 <= diff && diff <= 45) {
            return diff + " seconds ago";
        } else if ( 86400 < diff ) {
            return createDate.local().format("DD.MM.YYYY HH:mm");
        } else {
            return createDate.fromNow();
        }
    },
    /**
     * Get date by format YY.MM.DD
     * @param seconds
     * @returns string
     */
    getDateYYMMDD : function(seconds) {
        return moment.utc(seconds*1000).local().format("YY.MM.DD");
    }

};

$(document).ready(function(){
    'use strict';
    if (isGuestId > 0) {
        //console.log("start");
        clientTime.init();
    }
});