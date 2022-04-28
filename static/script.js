function add_to_pocket(active) {
    if (active.length === 0) {
        return false;
    }

    var pocketButton = active.find("a.pocketButton");
    var url = pocketButton.attr("href");
    if (url === undefined) {
        return false;
    }
    pocketButton.text('.');

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
        pocketButton.text('✓');

        if (response.status === 200) {
            // TODO: Add loca
            openNotification("worked :)", 'good');
        } else {
            // TODO: Add loca
            openNotification("failed :(", 'bad');
        }
    }).fail(function(data) {
        let response = JSON.parse(data);

        // TODO: Add loca
        openNotification("failed :(", 'bad');
        delete pending_entries[active.attr('id')];
    });
}

$(document).ready(function() {
    $('#stream .flux a.pocketButton').on('click', function() {
        var active = $(this).parents(".flux");
        add_to_pocket(active);
        return false;
    });

    $(this).keydown(function(e) {
        if (e.target.closest('input, textarea') || e.ctrlKey || e.metaKey || e.altKey || e.shiftKey) {
            return true;
        }

        var active = $('#stream .flux.active');
        var shortcut = 'b'; // TODO: Fix hardcoded shortcut

        if (e.key === shortcut) {
            add_to_pocket(active);
        }
    });
});