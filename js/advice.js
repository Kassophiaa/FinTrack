// Advisor advice page: send, view, edit and delete advice
$(document).ready(function() {

    var URL = BASE_URL + "/db/advisor_request.php";
    var rowsById = {};
    var editModal = new bootstrap.Modal(document.getElementById("editModal"));
    var viewModal = new bootstrap.Modal(document.getElementById("viewModal"));

    load();
    bindSearch("#searchKeyword", load);

    $("#content").on("input", function() { $("#contentCount").text($(this).val().length); });

    function load(keyword) {
        showLoading("#advisorAdviceTable", "Loading advice...");
        ajaxPost(URL, { action: keyword ? "search_advice" : "display_advice", keyword: keyword || "" }, function(response) {
            var rows = response.data || [];
            rowsById = {};
            if (!rows.length) {
                $("#advisorAdviceTable").html(keyword
                    ? emptyRow(4, "bi-search", "No matching advice", "Try a different keyword.")
                    : emptyRow(4, "bi-journal-text", "No advice sent yet", "Advice you write will be listed here."));
                return;
            }
            var html = "";
            rows.forEach(function(row) {
                rowsById[row.advice_id] = row;
                html += '<tr>';
                html += '<td style="max-width: 340px;"><div class="cell-title text-truncate">' + escapeHtml(row.title) + '</div><div class="cell-sub text-truncate">' + escapeHtml(row.content) + '</div></td>';
                html += '<td><a href="client_profile.php?user_id=' + parseInt(row.user_id, 10) + '">' + escapeHtml(row.client_name || '—') + '</a></td>';
                html += '<td style="white-space:nowrap;">' + escapeHtml(formatDate(row.created_at)) + '</td>';
                html += '<td class="text-end"><div class="row-actions">';
                html += '<button class="btn-icon btnView" data-id="' + row.advice_id + '" title="View" aria-label="View"><i class="bi bi-eye"></i></button>';
                html += '<button class="btn-icon btnEdit" data-id="' + row.advice_id + '" title="Edit" aria-label="Edit"><i class="bi bi-pencil"></i></button>';
                html += '<button class="btn-icon danger btnDelete" data-id="' + row.advice_id + '" title="Delete" aria-label="Delete"><i class="bi bi-trash"></i></button>';
                html += '</div></td></tr>';
            });
            $("#advisorAdviceTable").html(html);
        }, function(msg) {
            $("#advisorAdviceTable").html(emptyRow(4, "bi-wifi-off", "Couldn't load advice", msg));
        });
    }

    $("#adviceForm").on("submit", function(e) {
        e.preventDefault();
        $(".is-invalid").removeClass("is-invalid");
        var data = { action: "add_advice", user_id: $("#user_id").val(), title: $("#title").val().trim(), content: $("#content").val().trim() };
        var invalid = [];
        if (!data.user_id) invalid.push("#user_id");
        if (!data.title) invalid.push("#title");
        if (!data.content) invalid.push("#content");
        if (invalid.length) {
            $(invalid.join(",")).addClass("is-invalid");
            $(invalid[0]).focus();
            toast("danger", "Please choose a client and fill in the title and advice.");
            return;
        }
        var $form = $(this);
        setFormLoading($form, true);
        ajaxPost(URL, data, function(response) {
            setFormLoading($form, false);
            if (response.status == "success") {
                toast("success", "Advice sent. The client has been notified.");
                $("#title, #content").val("");
                $("#contentCount").text(0);
                load();
            } else {
                toast("danger", response.message);
            }
        }, function(msg) {
            setFormLoading($form, false);
            toast("danger", msg);
        });
    });

    $(document).on("click", ".btnView", function() {
        var row = rowsById[$(this).data("id")];
        $("#viewTitle").text(row.title);
        $("#viewClient").text(row.client_name || "—");
        $("#viewDate").text(formatDateTime(row.created_at));
        $("#viewContent").text(row.content);
        viewModal.show();
    });

    $(document).on("click", ".btnEdit", function() {
        var row = rowsById[$(this).data("id")];
        $("#edit_advice_id").val(row.advice_id);
        $("#edit_title").val(row.title);
        $("#edit_content").val(row.content);
        editModal.show();
    });

    $("#editForm").on("submit", function(e) {
        e.preventDefault();
        var data = { action: "edit_advice", advice_id: $("#edit_advice_id").val(), title: $("#edit_title").val().trim(), content: $("#edit_content").val().trim() };
        if (!data.title || !data.content) {
            toast("danger", "Title and advice can't be empty.");
            return;
        }
        var $form = $(this);
        setFormLoading($form, true);
        ajaxPost(URL, data, function(response) {
            setFormLoading($form, false);
            if (response.status == "success") {
                editModal.hide();
                toast("success", response.message);
                load($("#searchKeyword").val().trim());
            } else {
                toast("danger", response.message);
            }
        }, function(msg) {
            setFormLoading($form, false);
            toast("danger", msg);
        });
    });

    $(document).on("click", ".btnDelete", function() {
        var row = rowsById[$(this).data("id")];
        confirmDialog({ title: "Delete this advice?", message: "\"" + row.title + "\" will be removed for you and " + (row.client_name || "the client") + "." }, function() {
            ajaxPost(URL, { action: "delete_advice", advice_id: row.advice_id }, function(response) {
                toast(response.status == "success" ? "success" : "danger", response.message);
                if (response.status == "success") load($("#searchKeyword").val().trim());
            });
        });
    });
});
