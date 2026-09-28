document.addEventListener('DOMContentLoaded', function () {
    var grupos = document.querySelectorAll('.sidebar-group');

    grupos.forEach(function (grupo) {
        grupo.addEventListener('toggle', function () {
            if (grupo.open) {
                grupos.forEach(function (outro) {
                    if (outro !== grupo) {
                        outro.open = false;
                    }
                });
            }
        });
    });
});
