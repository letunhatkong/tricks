/**
 * Notification - NodeJS server
 * Created by UTC.KongLtn on 11/5/2015.
 * @author UTC.KongLtn
 */


var app = require("express")();
var mysql = require("mysql");
var http = require("http").Server(app);
var io = require("socket.io")(http);
var async = require("async");


/**
 * Object stores all socket.
 * @type {{sockets: {}, addSocket: Function, removeSocket: Function, getSocketByName: Function}}
 */
var allSockets = {
    /**
     * A storage object to hold the sockets
     */
    sockets: {},

    /**
     * Adds a socket to the storage object so it can be located by name
     * @param socket
     * @param name
     */
    addSocket: function (socket, name) {
        if (this.sockets[name] === undefined) {
            console.log("new array ");
            this.sockets[name] = [];
        }
        console.log("push socket " + name + " - total: " + this.sockets[name].length);
        this.sockets[name].push(socket);
    },
    /**
     * Removes a socket from the storage object based on its name
     * @param name
     */
    removeSocket: function (name) {
        if (this.sockets[name] !== undefined) {
            this.sockets[name] = null;
            delete this.sockets[name];
        }
    },
    /**
     * Throws an exception if the name is not valid
     * @param name
     * @returns {*} Returns a socket from the storage object based on its name
     */
    getSocketByName: function (name) {
        if (this.sockets[name] !== undefined) {
            return this.sockets[name];
        } else {
            throw new Error("A socket with the name '" + name + "' does not exist");
        }
    },
    /**
     * Check exists of Socket by id
     * @param name
     * @returns {boolean}
     */
    existsSocket: function (name) {
        return (this.sockets[name] !== undefined);
    }
};

/**
 * Creating POOL MySQL connection.
 */
var pool = mysql.createPool({
    connectionLimit: 100,
    host: '192.168.2.135',
    user: 'root',
    password: 'root',
    database: 'mentor'
});

/**
 * Default Namespace
 */
app.get("/", function (req, res) {
    res.sendFile(__dirname + '/index.html');
});

/**
 *  This is auto initiated event when Client connects to Your server.
 */
io.on('connection', function (socket) {
    // When user login, f5, ...
    socket.on('getSocketUser', function (data) {
        allSockets.addSocket(socket.id, data.userId);
        countMessage(data.userId, function (data2) {
            socket.emit("countMessages", data2);
        });
    });

    socket.on('userCreateNewMessage', function (data) {
        console.log('userCreateNewMessage');
        console.log(data);
        if (data.relatedIds !== undefined && data.relatedIds.length > 0) {
            data.relatedIds.forEach(function (userId) {
                countMessForSocketsByUserId(userId, function (cb) {
                    if (cb) console.log("done => " + userId);
                });
                pushMessage(userId, data.idMessage);
            })
        }

    });

    // When user exit
    socket.on('disconnect', function () {
        console.log("id of socket: " + socket.id);
        console.log("disconnected - count clients " + io.engine.clientsCount);
        for (var uId in allSockets.sockets) {
            if (allSockets.sockets.hasOwnProperty(uId) && allSockets.existsSocket(uId)) {
                var socketIndex = allSockets.sockets[uId].indexOf(socket.id);
                allSockets.sockets[uId].splice(socketIndex, 1);
            }
        }
        countClients();
    });
    countClients();
});

/**
 * Push message to related clients ny userId
 * @param userId
 * @param idMessage
 */
function pushMessage(userId, idMessage) {
    if (allSockets.sockets.hasOwnProperty(userId) && allSockets.existsSocket(userId)) {
        var sockets = allSockets.getSocketByName(userId);
        sockets.forEach(function (socId) {
            console.log("socket cua userId  " + socId);
            if (io.sockets.connected[socId] !== undefined) {
                io.sockets.connected[socId].emit("pushMessages", idMessage);
            }
        });
    }
}

/**
 * Count clients
 */
function countClients() {
    console.log("Clients " + io.engine.clientsCount);
}

/**
 * Count message and emit to all socket by user id
 * @param uId
 * @param cb - finish pointer
 */
function countMessForSocketsByUserId(uId, cb) {
    if (allSockets.sockets.hasOwnProperty(uId) && allSockets.existsSocket(uId)) {
        var sockets = allSockets.getSocketByName(uId);

        countMessage(uId, function (count) {
            console.log('dem truoc khi go ' + sockets.length);
            sockets.forEach(function (socId) {
                console.log('go ' + socId);
                if (io.sockets.connected[socId] !== undefined) {
                    io.sockets.connected[socId].emit("countMessages", count);
                }
            });
            cb(true);
        });
    }
}

/**
 * Test store notifications to Database
 * @param userId
 * @param callback
 * @return undefined
 */
var countMessage = function (userId, callback) {
    pool.getConnection(function (err, connection) {
        if (err) {
            connection.release();
            callback(false);
            return;
        }
        var queryCountMessage = 'select count(*) as numMess from notify_user nu ' +
            ' inner join users u on nu.userId= u.idUser ' +
            ' inner join ( ' +
            ' select n.*,u2.firstName as createFirstName,u2.lastName as createLastName ' +
            ' from notify n ' +
            ' inner join users u2 on n.createUserId= u2.idUser ' +
            ') temp on nu.notifyId = temp.notifyId ' +
            ' where nu.userId = ' + userId + ' and temp.typeNotify = "MESS" and nu.isRead =0;';

        connection.query(queryCountMessage, function (err, rows) {
            connection.release();
            if (!err) {
                callback(JSON.stringify(rows));
            }
        });
        //connection.on('error', function(err) {
        //    callback(false);
        //    if (err) console.log(err);
        //});
    });
};

/**
 * Sever is running on port 3000
 */
http.listen(3000, function () {
    console.log("Listening on 3000");
});