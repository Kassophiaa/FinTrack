$(document).ready(function() {

    if ($("#advisorSelect").length || $("#clientSelect").length) {
        initMessaging();
    }

    function initMessaging() {
        var selectId = "#advisorSelect";
        var label = "Advisor";
        if ($("#clientSelect").length) {
            selectId = "#clientSelect";
            label = "Client";
        }

        $(selectId).on("change", function() {
            var receiver_id = $(this).val();
            if (receiver_id != "") {
                $("#receiver_id").val(receiver_id);
                $("#messageInput").prop("disabled", false);
                $("#btnSend").prop("disabled", false);
                loadConversation(receiver_id);
            } else {
                $("#chatBox").html('<p class="text-muted text-center">Select a ' + label.toLowerCase() + ' to load conversation</p>');
                $("#messageInput").prop("disabled", true);
                $("#btnSend").prop("disabled", true);
            }
        });

        $("#btnLoad").on("click", function() {
            var receiver_id = $(selectId).val();
            if (receiver_id != "") {
                loadConversation(receiver_id);
            }
        });
    }

    function loadConversation(receiver_id) {
        showLoading("#chatBox", "Loading conversation...");
        $.ajax({
            url: BASE_URL + "/db/message_request.php",
            type: "POST",
            data: { action: "load_messages", receiver_id: receiver_id },
            dataType: "json",
            timeout: 30000,
            success: function(response) {
                var html = "";
                if (response.data && response.data.length > 0) {
                    $.each(response.data, function(i, row) {
                        if (row.sender_id == MY_ID) {
                            html += '<div class="text-end mb-2">';
                            html += '<div class="d-inline-block bg-primary text-white p-2 rounded" style="max-width: 70%;">';
                            html += '<small>You</small><br>';
                            html += escapeHtml(row.message);
                            html += '<br><small class="text-light">' + escapeHtml(row.created_at) + '</small>';
                            html += '</div></div>';
                        } else {
                            html += '<div class="text-start mb-2">';
                            html += '<div class="d-inline-block bg-white p-2 rounded border" style="max-width: 70%;">';
                            html += '<small>' + escapeHtml(row.sender_name) + '</small><br>';
                            html += escapeHtml(row.message);
                            html += '<br><small class="text-muted">' + escapeHtml(row.created_at) + '</small>';
                            html += '</div></div>';
                        }
                    });
                } else {
                    html = '<p class="text-muted text-center">No messages yet. Start the conversation!</p>';
                }
                $("#chatBox").html(html);
                if ($("#chatBox").length && $("#chatBox")[0].scrollHeight) {
                    $("#chatBox").scrollTop($("#chatBox")[0].scrollHeight);
                }

                $.ajax({
                    url: BASE_URL + "/db/message_request.php",
                    type: "POST",
                    data: { action: "mark_read", sender_id: receiver_id },
                    dataType: "json"
                });
            },
            error: function() {
                $("#chatBox").html('<p class="text-danger text-center">Failed to load conversation</p>');
            }
        });
    }

    $("#messageForm").on("submit", function(e) {
        e.preventDefault();
        var receiver_id = $("#receiver_id").val();
        var msg = $("#messageInput").val();
        if (msg.trim() == "") return;

        var $btn = $("#btnSend");
        var origText = $btn.html();
        $btn.prop("disabled", true).html('<span class="spinner-border spinner-border-sm"></span>');

        $.ajax({
            url: BASE_URL + "/db/message_request.php",
            type: "POST",
            data: {
                action: "send_message",
                receiver_id: receiver_id,
                message: msg
            },
            dataType: "json",
            timeout: 30000,
            success: function(response) {
                if (response.status == "success") {
                    $("#messageInput").val("");
                    loadConversation(receiver_id);
                } else {
                    showAlert("#alert", "danger", response.message);
                }
            },
            error: function() {
                showAlert("#alert", "danger", "Failed to send message");
            },
            complete: function() {
                $btn.prop("disabled", false).html(origText);
            }
        });
    });

});
