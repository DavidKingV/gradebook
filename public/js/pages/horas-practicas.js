import { fullCalendar } from '../common/fullCalendar.js';
import { successAlert, infoAlert, errorAlert } from '../common/sweetAlert.js';
import { enviarPeticionAjax } from '../common/ajax.js';

let calendarInstance;
var element = '#calendar';
var phpPath = 'api/Practicas.php';

function verifyToday(fecha){
    var today = new Date();
    today.setHours(0, 0, 0, 0);
    var date = new Date(fecha + "T00:00:00");
    if(today > date){
        return false;
    }else if(today = date){
        return true;
    }
    return true;
}


function compareTimes(start, end) {
    // Crear objetos Date con una fecha arbitraria pero usando las horas proporcionadas
    const startTime = new Date(`1970-01-01T${start}`);
    const endTime = new Date(`1970-01-01T${end}`);
    
    // Comparar los milisegundos de los objetos Date
    return startTime < endTime;
}

async function getHours(fecha) {
    try {
        const response = await fetch('https://bot.somefiaf.org/gradebook/check_date.php', { 
            method: 'POST', 
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ date: fecha })
        });
        const data = await response.json();
        
        if(data.disponibles.length > 0) {
            $('#timeStart').empty();

            data.disponibles.forEach(element => {
                $('#timeStart').append(`<option value="${element.start}">${element.start}</option>`);    
            });
            $("#practices").delegate("#timeEnd", "focusin", function(){
                $(this).timepicker({
                    container: '#practices',
                    timeFormat: 'H:mm',
                    minTime: data.disponibles[0].start,
                    maxTime: data.maxTime,
                    startTime: data.disponibles[0].start,
                    interval: 30,
                    dynamic: true,
                    dropdown: true,
                    change : function(){
                        var start = $('#timeStart').val();
                        var end = $('#timeEnd').val();
                        if(!compareTimes(start, end)){
                            infoAlert('La hora de salida no puede ser menor o igual a la hora de entrada');
                            $('#timeEnd').val('');
                        }
                    }
                });
            });
            $('#practices').modal('show');
        } else {
            infoAlert('No hay horarios disponibles para esta fecha');
        }
    } catch (error) {
        console.error('Error:', error);
    }
}


$(document).ready(function(){

    calendarInstance = fullCalendar(element, {
        googleCalendarApiKey: 'AIzaSyAe_BI44HkMi0RcG0vMekz0mmQAWaytaP4',

        events:{
            googleCalendarId: '0f5aa447aa1449099b33991265cd3ab6c5160d29754f6c20df46f6e2d061c21c@group.calendar.google.com',
            className: 'alert alert-primary',
        },

        themeSystem: 'bootstrap5',
        selectable: false,

        initialView: 'dayGridWeek',
        views:{
            dayGridWeek:{
                duration: { days: 8 },
            }
        },
        timeZone: 'local',
        locale: 'es',
        
        hiddenDays: [ 6 ],

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
            if(!verifyToday(info.dateStr)){
                infoAlert('No puedes agendar prácticas para fechas pasadas');
                return;
            }
            await getHours(info.dateStr);
            $('#dateStart').val(info.dateStr);
        },
    });

    $("#requestP").on('submit', function(e) {
        e.preventDefault();

        let datos = $(this).serialize();

        if($("#practiceCheck").is(":checked")){
            enviarPeticionAjax(phpPath, 'POST', {action: 'ReqPract', datos: datos})
            .done(function(data){
                if(data.success){
                    $('#practices').modal('hide');
                    successAlert('Prácticas agendadas correctamente');
                    calendarInstance.refetchEvents();
                }else{
                    errorAlert(data.message);
                }
            });
        }else{
            infoAlert('Debes aceptar los términos y condiciones');
        }
    });
    
});