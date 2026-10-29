// Two-pane messaging (shared by clients and advisors). Configured by the global CHAT object.
$(document).ready(function() {

    var URL = BASE_URL + "/db/message_request.php";
    var current = null;
    var lastCount = -1;
    var pollTimer = null;

    $("#contactFilter").on("input", function() {
        var q = $(this).val().trim().toLowerCase();
        $(".contact-item").each(function() {
            $(this).toggle(String($(this).data("name")).toLowerCase().indexOf(q) !== -1);
        });
    });

    $(document).on("click", ".contact-item", function() {
        openConversation($(this));
    });

    // Open a conversation from ?with=ID
    if (CHAT.selected) {
        var $pre = $('.contact-item[data-id="' + CHAT.selected + '"]');
        if ($pre.length) openConversation($pre);
    }

    function openConversation($item) {
        $(".contact-item").removeClass("active");
        $item.addClass("active");
        current = { id: $item.data("id"), name: $item.data("name"), canMessage: $item.data("can-message") == 1 };
        lastCount = -1;

        $("#chatPlaceholder").hide();
        $("#chatHead").show();
        $("#chatAvatar").text(initials(current.name));
        $("#chatName").text(current.name);
        $("#chatSub").text($item.data("sub"));
        if (CHAT.profileLink) $("#chatProfileLink").attr("href", CHAT.profileLink + current.id);

        $("#chatBox").show().html('<div class="chat-placeholder"><div class="spinner-border spinner-border-sm text-success"></div></div>');
        if (current.canMessage) {
            $("#messageForm").css("display", "flex");
            $("#chatLocked").hide();
            $("#messageInput").val("").focus();
        } else {
            $("#messageForm").hide();
            $("#chatLockedText").text($item.data("sub") === "No longer connected"
                ? "You're no longer connected, so new messages can't be sent. Your past conversation is kept here."
                : CHAT.lockedText);
            $("#chatLocked").show();
        }

        // Clear this contact's unread badge
        $item.find(".unread").remove();
        if (history.replaceState) history.replaceState(null, "", "?with=" + current.id);

        loadConversation(true);
        clearInterval(pollTimer);
        pollTimer = setInterval(function() { loadConversation(false); }, 8000);
    }

    function loadConversation(forceScroll) {
        if (!current) return;
        var forId = current.id;
        ajaxPost(URL, { action: "load_messages", receiver_id: forId }, function(response) {
            if (!current || current.id !== forId) return;
            var rows = response.data || [];
            if (rows.length === lastCount && !forceScroll) return;
            lastCount = rows.length;
            render(rows);
            ajaxPost(URL, { action: "mark_read", sender_id: forId }, function() {});
        }, function(msg) {
            if (forceScroll) $("#chatBox").html('<div class="chat-placeholder text-danger">' + escapeHtml(msg) + '</div>');
        });
    }

    function render(rows) {
        var $box = $("#chatBox");
        if (!rows.length) {
            $box.html('<div class="chat-placeholder"><i class="bi bi-hand-thumbs-up fs-3 mb-2"></i><p class="small mb-0">No messages yet. Say hello!</p></div>');
            return;
        }
        var html = "", lastDay = "";
        rows.forEach(function(row) {
            var d = parseDate(row.created_at);
            var day = formatDate(row.created_at);
            if (day !== lastDay) {
                var today = formatDate(todayISO());
                html += '<div class="chat-day">' + (day === today ? 'Today' : escapeHtml(day)) + '</div>';
                lastDay = day;
            }
            var mine = row.sender_id == MY_ID;
            html += '<div class="bubble-row ' + (mine ? 'mine' : 'theirs') + '"><div class="bubble">' + escapeHtml(row.message) +
                '<span class="bubble-time">' + (d ? formatTime(d) : '') + '</span></div></div>';
        });
        $box.html(html);
        $box.scrollTop($box[0].scrollHeight);
    }

    // Enter sends, Shift+Enter adds a new line; textarea grows with content
    $("#messageInput").on("keydown", function(e) {
        if (e.which === 13 && !e.shiftKey) {
            e.preventDefault();
            $("#messageForm").trigger("submit");
        }
    }).on("input", function() {
        this.style.height = "auto";
        this.style.height = Math.min(this.scrollHeight, 120) + "px";
    });

    $("#messageForm").on("submit", function(e) {
        e.preventDefault();
        if (!current) return;
        var msg = $("#messageInput").val();
        if (msg.trim() === "") return;

        var $btn = $("#btnSend");
        setButtonLoading($btn, true);
        ajaxPost(URL, { action: "send_message", receiver_id: current.id, message: msg }, function(response) {
            setButtonLoading($btn, false);
            if (response.status == "success") {
                $("#messageInput").val("").css("height", "auto").focus();
                loadConversation(true);
            } else {
                toast("danger", response.message);
            }
        }, function(m) {
            setButtonLoading($btn, false);
            toast("danger", m);
        });
    });
});
