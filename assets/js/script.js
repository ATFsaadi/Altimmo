document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-confirm]').forEach(function (link) {
        link.addEventListener('click', function (event) {
            if (!window.confirm(link.getAttribute('data-confirm'))) {
                event.preventDefault();
            }
        });
    });
});

