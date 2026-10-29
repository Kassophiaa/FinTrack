$(document).ready(function() {

    $("#loginForm").on("submit", function(e) {
        e.preventDefault();

        var username = $("#username").val();
        var password = $("#password").val();
        var $btn = $(this).find('button[type="submit"]');
        var origText = $btn.html();
        $btn.prop("disabled", true).html('<span class="spinner-border spinner-border-sm"></span> Signing in...');

        function showError(message) {
            try {
                showAlert("#alert", "danger", message);
            } catch (err) {
                $("#alert").html('<div class="alert alert-danger alert-dismissible fade show" role="alert">' + escapeHtml(message) + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>');
            }
        }

        $.ajax({
            url: BASE_URL + "/db/login_request.php",
            type: "POST",
            data: {
                username: username,
                password: password
            },
            dataType: "json",
            timeout: 30000,
            success: function(response) {
                if (response.status == "success") {
                    window.location.href = response.redirect;
                } else {
                    showError(response.message || "Login failed. Please try again.");
                }
            },
            error: function() {
                showError("Connection error. Please try again.");
            },
            complete: function() {
                $btn.prop("disabled", false).html(origText);
            }
        });
    });

});
