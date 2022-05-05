function add_to_pocket(active) {
    if (active.length === 0) {
        return false;
    }

    var pocketButton = active.find("a.pocketButton");
    var url = pocketButton.attr("href");
    if (url === undefined) {
        return false;
    }

    var pocketButtonImg = pocketButton.find("img");
    pocketButtonImg.addClass("disabled");
    var loadingAnimation = pocketButton.find(".lds-dual-ring");
    loadingAnimation.removeClass("disabled");

    if (pending_entries[active.attr('id')]) {
        return false;
    }

    pending_entries[active.attr('id')] = true;

    $.ajax({
        type: 'POST',
        url: url,
        data: {
            ajax: true,
            _csrf: context.csrf,
        },
    }).done(function(data) {
        let response = JSON.parse(data);
        delete pending_entries[active.attr('id')];

        if (response.status === 200) {
            pocketButtonImg.attr("src", pocket_button_vars.icons.added_to_pocket);
            openNotification(pocket_button_vars.i18n.added_article_to_pocket.replace('%s', response.response.title), 'pocket_button_good');
        } else {
            pocketButtonImg.attr("src", pocket_button_vars.icons.add_to_pocket);
            if (response.status === 404) {
                openNotification(pocket_button_vars.i18n.article_not_found, 'pocket_button_bad');
            } else {
                openNotification(pocket_button_vars.i18n.failed_to_add_article_to_pocket.replace('%s', response.errorCode), 'pocket_button_bad');
            }
        }

        pocketButtonImg.removeClass("disabled");
        loadingAnimation.addClass("disabled");
    }).fail(function(data) {
        pocketButtonImg.attr("src", pocket_button_vars.icons.add_to_pocket);

        openNotification(pocket_button_vars.i18n.ajax_request_failed, 'pocket_button_bad');
        delete pending_entries[active.attr('id')];
    });
}

$(document).ready(function() {
    $('#stream .flux a.pocketButton').on('click', function() {
        var active = $(this).parents(".flux");
        add_to_pocket(active);
        return false;
    });

    if (pocket_button_vars.keyboard_shortcut) {
        $(this).keydown(function(e) {
            if (e.target.closest('input, textarea') || e.ctrlKey || e.metaKey || e.altKey || e.shiftKey) {
                return true;
            }

            if (e.key === pocket_button_vars.keyboard_shortcut) {
                var active = $('#stream .flux.active');
                add_to_pocket(active);
            }
        });
    }
});