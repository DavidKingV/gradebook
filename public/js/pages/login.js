import { enviarPeticionAjax } from '../common/ajax.js';
import { loadingAlert, successAlert, errorAlert } from '../common/sweetAlert.js';

var phpPath = "api/login.php";

function openInNewWindow(url) {
    window.open(url, '_blank', 'width=800,height=600');
}

$(function() {
    $('[data-bs-toggle="tooltip"]').tooltip();

    $("#openInNewWindow").on("click", function(event) {
        event.preventDefault();
        openInNewWindow("api/microsoftLogin.php");
    });

    $("#loginForm").on("submit", function(event) {
        event.preventDefault();

        let loginData = $(this).serialize();

        loadingAlert();

        enviarPeticionAjax(phpPath, "POST", { action: "login", loginData: loginData })
            .done(function(data) {
                Swal.close();
                if (data.success) {
                    successAlert(data.message)
                    .then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "inicio.php";
                        }
                    });
                } else {
                    errorAlert(data.message);
                }
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                Swal.close();
                errorAlert("Error en la petición AJAX");
            });
    });

    window.addEventListener('message', function(event) {
        if (event.data.accessToken) {
            // Guardar el accessToken en la sesión o cookie si es necesario
            // Redirigir a inicio.php
            window.location.href = 'inicio.php?accessToken=' + event.data.accessToken;
        } else if (event.data.error) {
            // Manejar errores de autenticación
            alert('Authentication failed');
        } else{
            // Manejar otros mensajes
            alert('Unknown message');
        }
    }, false);

});