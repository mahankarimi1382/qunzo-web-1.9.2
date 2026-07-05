(function () {
    var cookieAlert = document.querySelector(".cookiealert");
    var acceptCookies = document.querySelector(".acceptcookies");
    var declineCookies = document.querySelector(".declinecookies");

    if (!cookieAlert) return;

    // Show alert if no consent cookie found
    if (!getCookie("cookieConsent")) {
        cookieAlert.removeAttribute("hidden");
    }

    // Accept cookies
    acceptCookies?.addEventListener("click", function () {
        setCookie("cookieConsent", "accepted", 365);
        cookieAlert.setAttribute("hidden", true);
        window.dispatchEvent(new Event("cookieAlertAccept"));
    });

    // Decline cookies
    declineCookies?.addEventListener("click", function () {
        setCookie("cookieConsent", "declined", 365);
        cookieAlert.setAttribute("hidden", true);
        window.dispatchEvent(new Event("cookieAlertDecline"));
    });

    function setCookie(cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + exdays * 24 * 60 * 60 * 1000);
        var expires = "expires=" + d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }

    function getCookie(cname) {
        var name = cname + "=";
        var decodedCookie = decodeURIComponent(document.cookie);
        var ca = decodedCookie.split(";");
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i].trim();
            if (c.indexOf(name) === 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }
})();