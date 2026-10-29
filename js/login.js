$(document).ready(function() {

    $("#loginForm").on("submit", function(e) {
        e.preventDefault();

        var username = $("#username").val().trim();
        var password = $("#password").val();

        if (!username || !password) {
            showAlert("#alert", "danger", "Please enter your username and password.");
            $(!username ? "#username" : "#password").focus();
            return;
        }

        var $btn = $(this).find('button[type="submit"]');
        var origText = $btn.html();
        $btn.prop("disabled", true).html('<span class="spinner-border spinner-border-sm"></span> Signing in...');

        ajaxPost(BASE_URL + "/db/login_request.php", { username: username, password: password }, function(response) {
            if (response.status == "success") {
                window.location.href = response.redirect;
                return;
            }
            showAlert("#alert", "danger", response.message || "Sign in failed. Please try again.");
            $("#password").val("").focus();
            $btn.prop("disabled", false).html(origText);
        }, function(msg) {
            showAlert("#alert", "danger", msg);
            $btn.prop("disabled", false).html(origText);
        });
    });

});
