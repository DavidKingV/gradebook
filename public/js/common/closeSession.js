import { enviarPeticionAjax } from './ajax.js';
import { loadingAlert, successAlert, errorAlert, confirmCloseSession } from './sweetAlert.js';

var phpPath = "api/login.php";

function openInNewWindow(url) {
    const newWindow = window.open(url, '_blank', 'width=800,height=600');

    // Espera a que la nueva ventana se haya cargado completamente antes de enviar el mensaje
    newWindow.onload = function() {
        newWindow.postMessage({ success: true, message: "Sesión cerrada" }, "*");
        newWindow.close();
    };
}

$(function() {

$("#closeSession").on("click", function(){

    confirmCloseSession("¿Estás seguro de cerrar sesión?", "Cerrar sesión", "Cancelar", function(){
        loadingAlert();

        enviarPeticionAjax(phpPath, "POST", { action: "logout" })
            .done(function(data) {
                Swal.close();
                if (data.success) {
                    if(data.microsoftLogout){
                        openInNewWindow("https://login.microsoftonline.com/ff8c5e54-d300-4681-8870-a4805a435d2a/oauth2/v2.0/logout");

                        window.addEventListener('message', function(event) {
                            if (event.data.success) {
                                window.location.href = "login.php";
                                successAlert(event.data.message);
                            }
                        }, false);
                    }
                    window.location.href = "login.php";
                } else {
                    errorAlert(data.message);
                }
            })
            .fail(function(jqXHR, textStatus, errorThrown) {
                Swal.close();
                errorAlert("Error en la petición AJAX");
            });
        });
    });

});