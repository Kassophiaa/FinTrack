$(document).ready(function() {

    if ($("#profileForm").length && $("#username").length && !$("#username").prop("readonly")) {
        loadProfile();
    }

    if ($("#passwordForm").length) {
        initPasswordChange();
    }

    function loadProfile() {
        showCardLoading("#alert", "Loading profile...");
        $.ajax({
            url: BASE_URL + "/db/profile_request.php",
            type: "POST",
            data: { action: "view_profile" },
            dataType: "json",
            timeout: 30000,
            success: function(response) {
                if (response.status == "success") {
                    var d = response.data;
                    $("#full_name").val(d.full_name);
                    $("#email").val(d.email);
                    $("#phone").val(d.phone);
                    $("#username").val(d.username);
                    $("#role").val(d.role.charAt(0).toUpperCase() + d.role.slice(1));
                    $("#alert").html("");
                } else {
                    showAlert("#alert", "danger", response.message || "Failed to load profile");
                }
            },
            error: function() {
                showAlert("#alert", "danger", "Failed to load profile. Please refresh the page.");
            }
        });
    }

    $("#profileForm").on("submit", function(e) {
        e.preventDefault();
        var $form = $(this);
        var $btn = $form.find('button[type="submit"]');
        var origText = $btn.html();
        $btn.prop("disabled", true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');

        $.ajax({
            url: BASE_URL + "/db/profile_request.php",
            type: "POST",
            data: {
                action: "update_profile",
                full_name: $("#full_name").val(),
                email: $("#email").val(),
                phone: $("#phone").val()
            },
            dataType: "json",
            timeout: 30000,
            success: function(response) {
                if (response.status == "success") {
                    showAlert("#alert", "success", response.message);
                    if (response.data && response.data.full_name) {
                        $("#full_name").val(response.data.full_name);
                    }
                } else {
                    showAlert("#alert", "danger", response.message);
                }
            },
            error: function() {
                showAlert("#alert", "danger", "An error occurred. Please try again.");
            },
            complete: function() {
                $btn.prop("disabled", false).html(origText);
            }
        });
    });

    function initPasswordChange() {
        $("#passwordForm").on("submit", function(e) {
            e.preventDefault();

            var new_password = $("#new_password").val();
            var confirm_password = $("#confirm_password").val();

            if (new_password !== confirm_password) {
                showAlert("#alert", "danger", "New passwords do not match.");
                return;
            }

            if (new_password.length < 6) {
                showAlert("#alert", "danger", "Password must be at least 6 characters.");
                return;
            }

            var $form = $(this);
            var $btn = $form.find('button[type="submit"]');
            var origText = $btn.html();
            $btn.prop("disabled", true).html('<span class="spinner-border spinner-border-sm"></span> Changing...');

            $.ajax({
                url: BASE_URL + "/db/profile_request.php",
                type: "POST",
                data: {
                    action: "change_password",
                    current_password: $("#current_password").val(),
                    new_password: new_password
                },
                dataType: "json",
                timeout: 30000,
                success: function(response) {
                    if (response.status == "success") {
                        showAlert("#alert", "success", response.message);
                        $form[0].reset();
                    } else {
                        showAlert("#alert", "danger", response.message);
                    }
                },
                error: function() {
                    showAlert("#alert", "danger", "An error occurred. Please try again.");
                },
                complete: function() {
                    $btn.prop("disabled", false).html(origText);
                }
            });
        });
    }

});
