var SystemSetting = function () {
    var elMainView = $("#main-view");
    var qrplaceholder = $("#qr-placeholder");
    var containerQR = elMainView.find(".wa-qrcode");
    var containerWA = elMainView.find(".wa-info");
    var containerLog = elMainView.find(".wa-logs");
    var imgQR = containerQR.find('img');
    var btnDisconnect = containerWA.find('.btn-disconnect-wa');

    var handleWASession = function () {
        var socket = io.connect(HOST);

        socket.on('connect', function () {
            updateWAInfo({ ready: false, message: "Checking ..." });
        });

        socket.on("qr", function (src) {
            imgQR.attr('src', src);
            updateWAInfo({ qr: true });
        });

        socket.on('ready', function (phoneNumber) {
            updateWAInfo({ ready: true, phone: phoneNumber });
        });

        socket.on('disconnect', function () {
            updateWAInfo({ ready: false, message: "Server Offline" });
        });

        socket.on('logout_success', function () {
            updateWAInfo({ ready: false, message: "Logged out" });
        });

        btnDisconnect.on('click', function () {
            console.log("Disconnect button clicked");
            socket.emit('logout');
        });
    };

    var updateWAInfo = function (options) {
        var setting = $.extend(true, {
            ready: false,
            qr: false,
            message: '',
            phone: ''
        }, options);

        elMainView.find('.wa-section').hide();

        if (setting.ready) {
            containerWA.find('.wa-number').text(setting.phone);
            qrplaceholder.hide();
            containerWA.show();
        } else if (setting.qr) {
            containerQR.show();
            qrplaceholder.hide();
        } else {
            containerLog.html(setting.message);
            containerLog.show();
        }
    }

    return {
        init: function () {
            handleWASession();
        },
    };
}();
