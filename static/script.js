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
    pocketButtonImg.attr("src", pocket_button_vars.icon_adding_to_pocket);

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
        pocketButtonImg.attr("src", pocket_button_vars.icon_added_to_pocket);

        if (response.status === 200) {
            // TODO: Add loca
            openNotification('Article <i>"' + response.response.title + '"</i> successfully added to Pocket!', 'pocket_button_good');
        } else {
            // TODO: Add loca
            openNotification('Failed to add article to pocket :(', 'pocket_button_bad');
        }
    }).fail(function(data) {
        let response = JSON.parse(data);

        pocketButtonImg.attr("src", pocket_button_vars.icon_add_to_pocket);

        // TODO: Add loca
        openNotification('Failed to add article to pocket :(', 'pocket_button_bad');
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