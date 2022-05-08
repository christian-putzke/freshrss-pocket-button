if (document.readyState && document.readyState !== 'loading') {
    documentReady();
} else {
    document.addEventListener('DOMContentLoaded', documentReady, false);
}

function documentReady() {
    var pocketButtons = document.querySelectorAll('#stream .flux a.pocketButton');
    for (var i = 0; i < pocketButtons.length; i++) {
        let pocketButton = pocketButtons[i];
        pocketButton.addEventListener('click', function(e) {
            if (!pocketButton) {
                return;
            }

            var active = pocketButton.closest(".flux");
            if (!active) {
                return;
            }

            e.preventDefault();
            e.stopPropagation();

            add_to_pocket(pocketButton, active);
        }, false);
    }

    if (pocket_button_vars.keyboard_shortcut) {
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey || e.metaKey || e.altKey || e.shiftKey || e.target.closest('input, textarea')) {
                return;
            }

            if (e.key === pocket_button_vars.keyboard_shortcut) {
                var active = document.querySelector("#stream .flux.active");
                if (!active) {
                    return;
                }

                var pocketButton = active.querySelector("a.pocketButton");
                if (!pocketButton) {
                    return;
                }

                add_to_pocket(pocketButton, active);
            }
        });
    }
}

function add_to_pocket(pocketButton, active) {
    var url = pocketButton.getAttribute("href");
    if (!url) {
        return;
    }

    let pocketButtonImg = pocketButton.querySelector("img");
    pocketButtonImg.classList.add("disabled");

    let loadingAnimation = pocketButton.querySelector(".lds-dual-ring");
    loadingAnimation.classList.remove("disabled");

    let activeId = active.getAttribute('id');

    if (pending_entries[activeId]) {
        return;
    }

    pending_entries[activeId] = true;

    let request = new XMLHttpRequest();

    request.open('POST', url, true);
    request.responseType = 'json';

    request.onload = function(e) {
        delete pending_entries[activeId];

        pocketButtonImg.classList.remove("disabled");
        loadingAnimation.classList.add("disabled");

        if (this.status != 200) {
            return request.onerror(e);
        }

        let response = xmlHttpRequestJson(this);
        if (!response) {
            return request.onerror(e);
        }

        if (response.status === 200) {
            pocketButtonImg.setAttribute("src", pocket_button_vars.icons.added_to_pocket);
            openNotification(pocket_button_vars.i18n.added_article_to_pocket.replace('%s', response.response.title), 'pocket_button_good');
        } else {
            if (response.status === 404) {
                openNotification(pocket_button_vars.i18n.article_not_found, 'pocket_button_bad');
            } else {
                openNotification(pocket_button_vars.i18n.failed_to_add_article_to_pocket.replace('%s', response.errorCode), 'pocket_button_bad');
            }
        }
    };

    request.onerror = function(e) {
        delete pending_entries[activeId];

        pocketButtonImg.classList.remove("disabled");
        loadingAnimation.classList.add("disabled");

        badAjax(this.status == 403);
    };

    request.setRequestHeader('Content-Type', 'application/json');
    request.send(JSON.stringify({
        ajax: true,
        _csrf: context.csrf
    }));
}