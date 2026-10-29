$(document).ready(function() {

    var URL = BASE_URL + "/db/profile_request.php";

    function submitForm($form, data, onSuccess) {
        setFormLoading($form, true);
        ajaxPost(URL, data, function(response) {
            setFormLoading($form, false);
            if (response.status == "success") {
                toast("success", response.message);
                if (onSuccess) onSuccess();
            } else {
                toast("danger", response.message);
            }
        }, function(msg) {
            setFormLoading($form, false);
            toast("danger", msg);
        });
    }

    $("#profileForm").on("submit", function(e) {
        e.preventDefault();
        $(".is-invalid").removeClass("is-invalid");
        var data = { action: "update_profile", full_name: $("#full_name").val().trim(), email: $("#email").val().trim(), phone: $("#phone").val().trim() };
        if (!data.full_name) { $("#full_name").addClass("is-invalid").focus(); toast("danger", "Please enter your full name."); return; }
        if (!/^\S+@\S+\.\S+$/.test(data.email)) { $("#email").addClass("is-invalid").focus(); toast("danger", "Please enter a valid email address."); return; }

        submitForm($(this), data, function() {
            // Keep the name/email shown elsewhere on the page in sync
            $("#profileName, .user-name, .user-name-text").text(data.full_name);
            $("#profileAvatar, .sidebar-user .avatar, .topbar-user .avatar").text(initials(data.full_name));
            $("#profileEmail").text(data.email);
            $("#profilePhone").html(data.phone ? escapeHtml(data.phone) : '<span class="text-muted">Not set</span>');
        });
    });

    function bindCounter(input, counter) {
        var update = function() { $(counter).text($(input).val().length); };
        $(input).on("input", update);
        update();
    }
    if ($("#bioForm").length) {
        bindCounter("#bio", "#bioCount");
        bindCounter("#experience", "#expCount");
    }

    $("#bioForm").on("submit", function(e) {
        e.preventDefault();
        submitForm($(this), { action: "update_bio_experience", bio: $("#bio").val().trim(), experience: $("#experience").val().trim() });
    });

    $("#passwordForm").on("submit", function(e) {
        e.preventDefault();
        $(".is-invalid").removeClass("is-invalid");
        var current = $("#current_password").val();
        var next = $("#new_password").val();
        var confirm = $("#confirm_password").val();

        if (!current) { $("#current_password").addClass("is-invalid").focus(); toast("danger", "Please enter your current password."); return; }
        if (next.length < 6) { $("#new_password").addClass("is-invalid").focus(); toast("danger", "New password must be at least 6 characters."); return; }
        if (next !== confirm) { $("#confirm_password").addClass("is-invalid").focus(); toast("danger", "New passwords don't match."); return; }

        var $form = $(this);
        submitForm($form, { action: "change_password", current_password: current, new_password: next }, function() {
            $form[0].reset();
        });
    });
});
