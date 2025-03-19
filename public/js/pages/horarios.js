import { loadingSpinner,errorAlert, errorAlertTimer } from '../common/sweetAlert.js';
import { enviarPeticionAjax } from '../common/ajax.js';
import { initializeDataTable } from '../common/datatables.js';

let phpPath = "api/Schedules.php";
let groupPath = "api/Group.php";

const calendarEl = $("#calendar")[0];

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
                errorAlertTimer("Su sesión ha expirado, serás redirigido al inicio de sesión");
                setTimeout(() => {
                    openInNewWindow("api/MicrosoftLogin.php");
                }, 3000);
            }   else if(data[0].message === "Cuenta local") {

                window.calendar = new FullCalendar.Calendar(calendarEl, {
                    
                    events: {
                        url: 'api/Schedules.php?action=getEvents',
                        method: 'GET',
                        failure: function() {
                            errorAlert('Error al cargar eventos');
                        },
                        success: function(response) {
                            if(response.success){
                                return response.events;
                            }else{
                                return [];
                            }
                        },
                        color: '#f9cb7d',
                        display: 'block',
                        textColor: '#0951f5',
                    },
                
                    themeSystem: 'bootstrap5',
                    selectable: false,
                
                    height: 'auto',
                    allDaySlot: false,
                    initialView: 'timeGridFourDay',
                    views:{
                        timeGridFourDay:{
                            type: 'timeGrid',
                            duration: { days: 8 },
                            slotMinTime: '09:00:00',
                            slotMaxTime: '18:00:00'
                        }
                    },
                    timeZone: 'local',
                    locale: 'es',
                        
                    hiddenDays: [ 6 ],
                
                    displayEventEnd: true,
                
                    businessHours: [ // specify an array instead
                        {
                            daysOfWeek: [ 1, 2, 3, 4, 5 ], 
                            startTime: '09:00', 
                            endTime: '17:00' 
                        },
                        {
                            daysOfWeek: [ 7 ], 
                            startTime: '08:00', 
                            endTime: '14:00' 
                        }
                    ],
                    headerToolbar: {
                        start: 'title', // will normally be on the left. if RTL, will be on the right
                        center: '',
                        end: '' // will normally be on the right. if RTL, will be on the left
                    },
                    dateClick: async  function(info) {
                        /*$("#addEventModalLabel").html('Agregar alumno para el '+info.dateStr+'');
                        $("#addEventModal").modal('show');
                        $.post('../modals/addEvent.Modal.php', { date: info.dateStr }, function (data) {
                            $('#addEventModalBody').html(data);
                        });*/
                    },
                
                    eventClick: async function(info) {
                        info.jsEvent.preventDefault();
                        $("#eventDetailsBody").html('');
                        loadingSpinner(true, '#eventDetailsBody');
                        $('#eventDetails').modal('show');
                        
                        $("#eventDetailsLabel").html(info.event.title);
                        await $.post('modals/eventDetails.Modal.php', { eventId: info.event._def.publicId, eventData: info.event._instance }, function (data) {                
                            $('#eventDetailsBody').html(data);
                        });
                    },
                });

                window.calendar.render();
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



        initializeDataTable('#groupMaterialTable', groupPath, { action: 'getGroupMaterial' }, [
            { data: 'name', 'className': 'text-center' },
            {
                data: 'url',
                className: 'text-center',
                render: function(data, type, row) {
                    // Define la longitud máxima que quieres mostrar
                    const maxLength = 30;
                    let displayText = data;
                    if (data.length > maxLength) {
                        displayText = data.substring(0, maxLength) + '...';
                    }
                    return `<a href="${data}" target="_blank">${displayText}</a>`;
                }
            },
        ]);

});