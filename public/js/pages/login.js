import { enviarPeticionAjax } from '../common/ajax.js';
import { loadingAlert, successAlert, errorAlert } from '../common/sweetAlert.js';

var phpPath = "api/login.php";

$(function() {

    $("#loginForm").submit(function(event) {
        event.preventDefault();

        let loginData = $(this).serialize();

        loadingAlert();

        enviarPeticionAjax(phpPath, "POST", { action: "login", loginData: loginData })
            .done(function(data) {
                Swal.close();
                if (data.success) {
                    successAlert(data.message)
                    .then((result) => {
                        window.location.href = "resume.html";
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

});