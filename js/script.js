$(document).ready(function() {

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $(".alert").fadeOut("slow");
    }, 5000);

    // Confirm delete actions
    $(document).on("click", ".btn-delete-confirm", function(e) {
        if (!confirm("Are you sure you want to delete this record?")) {
            e.preventDefault();
        }
    });

});

// Global XSS-safe helper
function escapeHtml(text) {
    if (text === null || text === undefined) return '';
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(String(text)));
    return div.innerHTML;
}

// Global formatCurrency helper
function formatCurrency(val) {
    var num = parseFloat(val) || 0;
    return '\u20B1' + num.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

// Show loading spinner in a container
function showLoading(container, message) {
    var msg = message || 'Loading...';
    $(container).html('<tr><td colspan="100" class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">' + escapeHtml(msg) + '</span></div><p class="mt-2 text-muted">' + escapeHtml(msg) + '</p></td></tr>');
}

// Show loading in card container
function showCardLoading(container, message) {
    var msg = message || 'Loading...';
    $(container).html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">' + escapeHtml(msg) + '</span></div><p class="mt-2 text-muted">' + escapeHtml(msg) + '</p></div>');
}

// Show alert message
function showAlert(container, type, message) {
    var icon = 'bi-exclamation-triangle';
    if (type === 'success') icon = 'bi-check-circle';
    if (type === 'danger') icon = 'bi-x-circle';
    if (type === 'warning') icon = 'bi-exclamation-triangle';
    if (type === 'info') icon = 'bi-info-circle';
    $(container).html('<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert"><i class="bi ' + icon + '"></i> ' + escapeHtml(message) + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>');
    setTimeout(function() { $(container + ' .alert').fadeOut('slow'); }, 5000);
}

// Disable/enable form buttons during AJAX
function setFormLoading(formSelector, loading, originalText) {
    var btn = $(formSelector).find('button[type="submit"]');
    if (loading) {
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status"></span> Processing...');
    } else {
        btn.prop('disabled', false).html(originalText || btn.data('original-text') || 'Submit');
    }
}

// Dropdown + Custom input helpers
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
        var custom = $('#' + customInputId).val().trim();
        return custom !== '' ? custom : '';
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

// Safe AJAX POST wrapper
function ajaxPost(url, data, onSuccess, onError) {
    $.ajax({
        url: url,
        type: "POST",
        data: data,
        dataType: "json",
        timeout: 30000,
        success: function(response) {
            if (onSuccess) onSuccess(response);
        },
        error: function(xhr, status, error) {
            var msg = 'An error occurred. Please try again.';
            if (status === 'timeout') msg = 'Request timed out. Please try again.';
            else if (xhr.status === 403) msg = 'You are not authorized to perform this action.';
            else if (xhr.status === 404) msg = 'The requested resource was not found.';
            else if (xhr.status === 500) msg = 'A server error occurred. Please try again later.';
            if (onError) onError(msg);
        }
    });
}
