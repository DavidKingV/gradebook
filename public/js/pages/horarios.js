import { loadingSpinner,errorAlert } from '../common/sweetAlert.js';
import { enviarPeticionAjax } from '../common/ajax.js';

let phpPath = "api/Schedules.php";

function utcToLocal(utcDate) {
    var localDate = new Date(utcDate);
    //resta 6 horas para ajustar a la hora de México
    localDate.setHours(localDate.getHours() - 6);
    return localDate.toLocaleString();
}

function openInNewWindow(url) {
    window.open(url, '_blank', 'width=800,height=600');
}

$(function() {

    loadingSpinner(true, '#section-sched');

    enviarPeticionAjax(phpPath, 'GET' , { action: 'getSchedules' })
        .done(function(data) {
            loadingSpinner(false, '#section-sched');
            if (data[0].success && data.length > 0 ) {
                data.forEach(event => {

                    var htmlCard = `<div class="container px-5 mb-5">                    
                    <div class="gx-6 justify-content-center">
                        <div class="card">
                            <div class="card-header">
                                `+ utcToLocal(event.start) + ` - ` + utcToLocal(event.end) + `
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">` +event.subject+ `</h5>
                                <p class="card-text"><i class="bi bi-geo-fill"></i> Reunión Online</p>
                                <a href="`+event.joinUrl+`" class="btn btn-primary">Link para unirse</a>
                            </div>
                        </div>
                    </div>
                </div> `;
                $('#section-sched').append(htmlCard);
                });
            } else if(data[0].message === "Token expirado") {
                errorAlert("Su sesión ha expirado, serás redirigido al inicio de sesión");
                setTimeout(() => {
                    openInNewWindow("api/MicrosoftLogin.php");
                }, 3000);
            }   
            else {
                errorAlert(data[0].message);

                let htmlCardEmpty = `<div class="container">
                        <div class="row">
                            <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                <h5>Lista de clases</h5>
                                </div>
                                <div class="card-body cart">
                                <div class="col-sm-12 empty-cart-cls text-center">
                                    <img
                                    src="https://esmefis.edu.mx/wp-content/uploads/2024/08/cartera.png"
                                    width="130"
                                    height="130"
                                    class="img-fluid mb-4 mr-3"
                                    alt="Empty"
                                    />
                                    <h3><strong>No tienes clases programadas próximamente</strong></h3>
                                    <h4>Si crees que hace falta algo, comunícate con la administración</h4>
                                    <a href="inicio.php" class="btn btn-primary cart-btn-transform m-3" data-abc="true">
                                    Volver al inicio
                                    </a>
                                </div>
                                </div>
                            </div>
                            </div>
                        </div>
                        </div>`;
                $('#section-sched').append(htmlCardEmpty);

            }
        })
        .fail(function(jqXHR, textStatus, errorThrown) {
            loadingSpinner(false, '#section-sched');
            errorAlert("Error en la petición AJAX");
        });


        window.addEventListener('message', function(event) {
            if (event.data.accessToken) {
                // Guardar el accessToken en la sesión o cookie si es necesario
                // Redirigir a inicio.php
                window.location.href = 'horarios.php';
            } else if (event.data.error) {
                // Manejar errores de autenticación
                errorAlert(event.data.error);
            } else{
                // Manejar otros mensajes
                errorAlert("Error desconocido, por favor intente de nuevo");
            }
        }, false);

});