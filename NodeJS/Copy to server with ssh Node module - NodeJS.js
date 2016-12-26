function copyToServer(data2) {
    console.log("File name: " + data2.filename);
    console.log("url: " + data2.url);
    var fullPath = data2.filename;
    var shortPath = data2.url;
    //var test = '/home/vtv-dng-03/vtv/public/upload/a.jpg';
    //var subP = test.split("/")[2];
    var subP = fullPath.split("/")[2];
    console.log(subP);
    var hostT = "113.171.23.83";
    var userT = "vtv-dng-03";

    if (subP == 'vtv-dng-03') {
        hostT = '113.171.23.81';
        userT = 'vtv-dng-02';
    }
    console.log(hostT + " " + userT);

    // SSH2
    var conn = new ssh2();

    conn.on(
        'connect',
        function () {
            console.log("- connected");
        }
    );

    conn.on(
        'ready',
        function () {
            console.log("- ready");
            conn.sftp(
                function (err, sftp) {
                    if (err) {
                        console.log("Error, problem starting SFTP: %s", err);
                        process.exit(2);
                    }

                    console.log("- SFTP started");
                    console.log(data2.filename);

                    // upload file
                    var readStream = fs.createReadStream(data2.filename);
                    var writeStream = sftp.createWriteStream("/home/" + userT + "/vtv/public" + data2.url);

                    // what to do when transfer finishes
                    writeStream.on(
                        'close',
                        function () {
                            console.log("- file transferred");
                            sftp.end();
                            // process.exit( 1 );
                        }
                    );

                    // initiate transfer of file
                    readStream.pipe(writeStream);
                }
            );
        }
    );

    conn.connect(
        {
            "host": hostT,
            "port": 22,
            "username": userT,
            "password": "vtv@321!"
        }
    );
    // # SSH2
}