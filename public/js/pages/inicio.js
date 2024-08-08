import { infoAlert } from '../common/sweetAlert.js';
import { enviarPeticionAjax } from '../common/ajax.js';

let phpPath = 'api/Inicio.php';

function consultarTyC() {
    enviarPeticionAjax(phpPath, 'POST', {action: 'consultarTyC'})
        .done(function (data) {
            if (!data.success) {
                $("#TyCModal").modal('show');
            }
        });
}

function aceptarTyC() {
    enviarPeticionAjax(phpPath, 'POST', {action: 'aceptarTyC'})
        .done(function (data) {
            if (data.success) {
                $("#TyCModal").modal('hide');
                infoAlert('Haz leido el reglameto y lo has aceptado');
            }
        });
}

function enableScrollButton() {
    let button = $("#acceptTyC");
    let modalBody = $("#TyCModal .modal-body");

    modalBody.scroll(function () {
        let scrollHeight = modalBody[0].scrollHeight;
        let scrollTop = modalBody.scrollTop();
        let clientHeight = modalBody[0].clientHeight;

        if (scrollTop + clientHeight >= scrollHeight) {
            button.prop('disabled', false);
        }
    });
}
$(function () {
    consultarTyC();
    enableScrollButton();

    $('#acceptTyC').on('click', function () {
        aceptarTyC();
    });
});

