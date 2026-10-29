$(document).ready(function() {

    // Mobile sidebar
    $("#sidebarToggle").on("click", function() { $("body").addClass("sidebar-open"); });
    $("#sidebarBackdrop").on("click", function() { $("body").removeClass("sidebar-open"); });

    // Date inputs marked data-default-today start on today's date
    $("input[type=date][data-default-today]").each(function() {
        if (!$(this).val()) $(this).val(todayISO());
    });

    // Show / hide password
    $(document).on("click", ".toggle-password", function() {
        var $input = $(this).siblings("input");
        var show = $input.attr("type") === "password";
        $input.attr("type", show ? "text" : "password");
        $(this).find("i").toggleClass("bi-eye", !show).toggleClass("bi-eye-slash", show);
    });

    // Clear button inside search inputs
    $(document).on("input", ".search-input input", function() {
        $(this).closest(".search-input").toggleClass("has-value", $(this).val() !== "");
    });
    $(document).on("click", ".btn-clear-search", function() {
        var $input = $(this).siblings("input");
        $input.val("").trigger("input").focus();
    });

    // Close modals' forms cleanly
    $(document).on("hidden.bs.modal", ".modal", function() {
        $(this).find(".is-invalid").removeClass("is-invalid");
    });
});

// ---------------------------------------------------------------------------
// Formatting
// ---------------------------------------------------------------------------

function escapeHtml(text) {
    if (text === null || text === undefined) return '';
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(String(text)));
    return div.innerHTML;
}

function formatCurrency(val) {
    var num = parseFloat(val) || 0;
    var sign = num < 0 ? '-' : '';
    return sign + '₱' + Math.abs(num).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

function parseDate(str) {
    if (!str) return null;
    // MySQL "YYYY-MM-DD" or "YYYY-MM-DD HH:MM:SS" parsed as local time
    var m = String(str).match(/^(\d{4})-(\d{2})-(\d{2})(?:[ T](\d{2}):(\d{2})(?::(\d{2}))?)?/);
    if (!m) return null;
    return new Date(+m[1], +m[2] - 1, +m[3], +(m[4] || 0), +(m[5] || 0), +(m[6] || 0));
}

var MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

function formatDate(str) {
    var d = parseDate(str);
    if (!d) return '-';
    return MONTHS[d.getMonth()] + ' ' + d.getDate() + ', ' + d.getFullYear();
}

function formatTime(d) {
    var h = d.getHours(), m = d.getMinutes();
    var ampm = h >= 12 ? 'PM' : 'AM';
    h = h % 12 || 12;
    return h + ':' + (m < 10 ? '0' : '') + m + ' ' + ampm;
}

function formatDateTime(str) {
    var d = parseDate(str);
    if (!d) return '-';
    return formatDate(str) + ', ' + formatTime(d);
}

function timeAgo(str) {
    var d = parseDate(str);
    if (!d) return '';
    var secs = Math.floor((Date.now() - d.getTime()) / 1000);
    if (secs < 60) return 'Just now';
    if (secs < 3600) return Math.floor(secs / 60) + ' min ago';
    if (secs < 86400) return Math.floor(secs / 3600) + ' hr ago';
    if (secs < 604800) { var days = Math.floor(secs / 86400); return days + (days === 1 ? ' day ago' : ' days ago'); }
    return formatDate(str);
}

function todayISO() {
    var d = new Date();
    return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
}

function initials(name) {
    var parts = String(name || '').trim().split(/\s+/);
    var out = '';
    for (var i = 0; i < parts.length && out.length < 2; i++) {
        if (parts[i]) out += parts[i].charAt(0).toUpperCase();
    }
    return out || '?';
}

function renderStars(rating) {
    var r = Math.round(parseFloat(rating) || 0);
    var html = '<span class="stars" aria-label="' + r + ' out of 5 stars">';
    for (var s = 1; s <= 5; s++) {
        html += s <= r ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star"></i>';
    }
    return html + '</span>';
}

// ---------------------------------------------------------------------------
// Feedback: toasts, alerts, confirmations, loading states
// ---------------------------------------------------------------------------

function toast(type, message) {
    var icons = { success: 'bi-check-circle-fill', danger: 'bi-x-circle-fill', warning: 'bi-exclamation-triangle-fill', info: 'bi-info-circle-fill' };
    var $stack = $("#toastStack");
    if (!$stack.length) $stack = $('<div class="toast-stack" id="toastStack"></div>').appendTo("body");
    var $t = $('<div class="app-toast ' + type + '" role="status"><i class="bi ' + (icons[type] || icons.info) + '"></i><div class="toast-msg">' + escapeHtml(message) + '</div><button type="button" class="toast-close" aria-label="Dismiss"><i class="bi bi-x-lg"></i></button></div>');
    $stack.append($t);
    $t.find(".toast-close").on("click", function() { $t.remove(); });
    setTimeout(function() { $t.fadeOut(250, function() { $t.remove(); }); }, type === 'danger' ? 6000 : 4000);
}

// Shows feedback. Auth pages render it inline in the given container; app pages use a toast.
function showAlert(container, type, message) {
    var $c = $(container);
    if ($c.length && $c.closest(".auth-card").length) {
        var icon = type === 'success' ? 'bi-check-circle' : (type === 'danger' ? 'bi-x-circle' : 'bi-info-circle');
        $c.html('<div class="alert alert-' + type + '" role="alert"><i class="bi ' + icon + '"></i><span>' + escapeHtml(message) + '</span></div>');
        return;
    }
    toast(type, message);
}

// Styled replacement for window.confirm(). opts: {title, message, confirmText, confirmClass}
function confirmDialog(opts, onConfirm) {
    var $m = $("#confirmModal");
    if (!$m.length) { if (window.confirm(opts.message || opts.title)) onConfirm(); return; }
    $("#confirmTitle").text(opts.title || "Are you sure?");
    $("#confirmMessage").text(opts.message || "");
    var $ok = $("#confirmOk");
    $ok.text(opts.confirmText || "Delete").attr("class", "btn " + (opts.confirmClass || "btn-danger"));
    $ok.off("click").on("click", function() {
        bootstrap.Modal.getOrCreateInstance($m[0]).hide();
        onConfirm();
    });
    bootstrap.Modal.getOrCreateInstance($m[0]).show();
}

function showLoading(container, message) {
    $(container).html('<tr class="table-loading"><td colspan="100"><div class="spinner-border spinner-border-sm text-success me-2" role="status"></div>' + escapeHtml(message || 'Loading...') + '</td></tr>');
}

function showCardLoading(container, message) {
    $(container).html('<div class="empty-state"><div class="spinner-border text-success" role="status"></div><p class="mt-2 mb-0">' + escapeHtml(message || 'Loading...') + '</p></div>');
}

function emptyStateHtml(icon, title, text, actionHtml) {
    return '<div class="empty-state"><div class="empty-icon"><i class="bi ' + icon + '"></i></div><h6>' + escapeHtml(title) + '</h6>' +
        (text ? '<p>' + escapeHtml(text) + '</p>' : '') + (actionHtml || '') + '</div>';
}

function emptyRow(colspan, icon, title, text, actionHtml) {
    return '<tr><td colspan="' + colspan + '" class="p-0 border-0">' + emptyStateHtml(icon, title, text, actionHtml) + '</td></tr>';
}

function setFormLoading(formSelector, loading, originalText) {
    var btn = $(formSelector).find('button[type="submit"]');
    if (loading) {
        if (!btn.data('original-text')) btn.data('original-text', btn.html());
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status"></span> Saving...');
    } else {
        btn.prop('disabled', false).html(originalText || btn.data('original-text') || 'Submit');
    }
}

function setButtonLoading($btn, loading, text) {
    if (loading) {
        $btn.data('original-html', $btn.html());
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>' + (text ? ' ' + text : ''));
    } else {
        $btn.prop('disabled', false).html($btn.data('original-html'));
    }
}

// ---------------------------------------------------------------------------
// Inputs
// ---------------------------------------------------------------------------

// Dropdown with an "Other" option that reveals a free-text input
function initDropdownCustom(selectId, customInputId) {
    var $sel = $('#' + selectId);
    var $custom = $('#' + customInputId);
    $sel.on('change', function() {
        if ($(this).val() === 'Other') {
            $custom.show().focus();
        } else {
            $custom.hide().val('');
        }
    });
}

function getDropdownValue(selectId, customInputId) {
    var val = $('#' + selectId).val();
    if (val === 'Other') {
        return $('#' + customInputId).val().trim();
    }
    return val || '';
}

function setDropdownValue(selectId, customInputId, value) {
    var $sel = $('#' + selectId);
    var $custom = $('#' + customInputId);
    var options = $sel.find('option').map(function() { return $(this).val(); }).get();
    if (options.indexOf(value) !== -1) {
        $sel.val(value);
        $custom.hide().val('');
    } else if (value) {
        $sel.val('Other');
        $custom.val(value).show();
    } else {
        $sel.val('');
        $custom.hide().val('');
    }
}

function debounce(fn, wait) {
    var t;
    return function() {
        var args = arguments, ctx = this;
        clearTimeout(t);
        t = setTimeout(function() { fn.apply(ctx, args); }, wait);
    };
}

// Runs onSearch(keyword) as the user types (debounced) and on Enter
function bindSearch(inputSelector, onSearch) {
    var $input = $(inputSelector);
    var run = debounce(function() { onSearch($input.val().trim()); }, 300);
    $input.on("input", run);
    $input.on("keydown", function(e) {
        if (e.which === 13) { e.preventDefault(); onSearch($input.val().trim()); }
    });
}

// ---------------------------------------------------------------------------
// AJAX
// ---------------------------------------------------------------------------

function ajaxPost(url, data, onSuccess, onError) {
    return $.ajax({
        url: url,
        type: "POST",
        data: data,
        dataType: "json",
        timeout: 30000,
        success: function(response) {
            if (onSuccess) onSuccess(response);
        },
        error: function(xhr, status) {
            var msg = 'Something went wrong. Please try again.';
            if (status === 'timeout') msg = 'The request timed out. Please try again.';
            else if (xhr.status === 403) msg = 'You are not authorized to perform this action.';
            else if (xhr.status === 404) msg = 'The requested resource was not found.';
            else if (xhr.status >= 500) msg = 'A server error occurred. Please try again later.';
            if (onError) onError(msg); else toast('danger', msg);
        }
    });
}
