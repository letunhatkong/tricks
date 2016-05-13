/**
 * Socket IO client
 * @author UTC.KongLtn
 * Created by UTC.KongLtn on 11/11/2015.
 */


/**
 * Variables of socket Event
 * @type {{wrapId: string, createMessagesButton: string, messageMenu: string}}
 */
var socketEventId = {
    wrapId: "#notify_wrap",
    createMessagesButton: "#createMessagesButton",
    messageMenu: "#messageMenu",
    createMessagesForm: "#createMessagesForm"
};
var socket = null;
/**
 * Socket Event
 * @type {{init: Function, connectSocket: Function}}
 */
var socketEvent = {
    init: function () {
        var isGuest = $('#isGuest').val();
        if (isGuest !== "0") {
            this.connectSocket(isGuest);
        }
    },
    /**
     * Create socket connect to nodeJS server
     * @param userId
     */
    connectSocket: function (userId) {
        // Create a connection to NodeJS server port 3000
        socket = io.connect('http://127.0.0.1:3000');

        /**
         * Post userId to NodeJS server
         * @return undefined
         */
        socket.on('connect', function (data) {
            socket.emit('getSocketUser', {userId: userId});
        });

        /**
         * Show message total
         * @param result
         * @return undefined
         */
        socket.on('countMessages', function (result) {
            var data = JSON.parse(result);
            var count = data[0].numMess;
            socketEvent.showMessageTotal(count);
        });

        socket.on('pushMessages', function (idMessage) {
            infoMessagesFormEvent.pushContentMessage(idMessage);
        });

        /**
         * Event create message on current socket.
         * @return undefined
         */
    },
    socketEmitCountMess: function (idArray, idMessage) {
        console.log(idMessage);
        console.log(idArray);
        socket.emit('userCreateNewMessage', {idMessage: idMessage, relatedIds: idArray});
    },
    /**
     * Show Message Total
     * @param count
     * @return undefined
     */
    showMessageTotal: function (count) {
        $(socketEventId.messageMenu + ' > span').remove();
        if (count !== undefined && count > 0) {
            $(socketEventId.messageMenu).append("<span>" + count + "</span>");
        }
    }
};


$(document).ready(function () {
    'use strict';
    socketEvent.init();

});