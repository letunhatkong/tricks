var ssh2 = require('ssh2');
// Delete video file in server 2 (81)
function del (cb) {
    var conn = new ssh2();
    conn.on('connect', function () {
        console.log("- connected");
    });

    conn.on('ready', function () {
        console.log("- ready");
        conn.sftp(function (err, sftp) {
            if (err) {
                console.log("Error, problem starting SFTP: %s", err);
                // process.exit( 2 );
            }
            sftp.unlink(srcU2, function (err) {
                if (err) {
                    console.log("Error: " + err);
                    cb(null, 's2: File delete failed ');
                }
                else cb(null, 's2: File delete Success ');
            });
        });
    });

    conn.connect({
        "host": '113.',
        "port": 22,
        "username": 'vtv',
        "password": "vtv"
    });
    // Delete video file in server 2 (81)
}