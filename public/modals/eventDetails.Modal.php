<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Esmefis\Gradebook\DBConnection;
use Esmefis\Gradebook\getEnv;

getEnv::cargar();

$eventId = $_POST['eventId'];
?>

<div class="form-group">          

    <div class="mb-3">
        <label for="start">Hora de inicio: </label>
        <!-- Placeholder inicial -->
        <p class="placeholder-glow" id="startPlaceholder">
            <span class="placeholder col-12"></span>
        </p>
        <input class="form-control d-none" id="start" name="start" type="text" placeholder="" value="">      
    </div>

    <div class="mb-3">
        <label for="end">Hora de termino: </label>
        <!-- Placeholder inicial -->
        <p class="placeholder-glow" id="endPlaceholder">
            <span class="placeholder col-12"></span>
        </p>
        <input 
            class="form-control d-none" 
            id="end" 
            name="end" 
            type="text" 
            placeholder="" 
            value=""
        >      
    </div>   

    <div class="mb-3">
        <label for="end">Detalles: </label>
        <!-- Placeholder inicial -->
        <p class="placeholder-glow" id="detailsPlaceholder">
            <span class="placeholder col-12"></span>
        </p>
        <p 
            class="form-control d-none" 
            id="details" 
            name="details" 
            style="overflow-wrap: break-word;"
        ></p>
    </div>   

</div>

<script type="module">
    import { sendFetch } from '<?php echo $_ENV['BASE_URL']; ?>/public/js/common/fetchCall.js';
    import { errorAlert } from '<?php echo $_ENV['BASE_URL']; ?>/public/js/common/sweetAlert.js';

    let api = '<?php echo $_ENV['BASE_URL']; ?>/public/api/Schedules.php';
    let eventId = '<?php echo $eventId; ?>';

    $(function() {
        
        sendFetch(api, 'POST', { action: 'getEventDetails', eventId: eventId })
                .then(async response => {
                    if (!response.ok) {
                        throw new Error('Ocurrió un error al realizar la petición: ' + response.statusText);
                    }                    
                    return response.json();  // Asegúrate de que se está retornando la promesa con la conversión a JSON
                })
                .then(async data => {
                    if (data.success) {                                                                        
                        // Remover placeholders y mostrar inputs
                        $("#startPlaceholder, #endPlaceholder, #detailsPlaceholder").remove();
                        $("#start, #end, #details").removeClass("d-none");

                        $('#start').val(data.event.start);
                        $('#end').val(data.event.end);
                        const detailsText = data.event.description || '';
                        $('#details').html(convertirURLs(detailsText));
                    } else {
                        errorAlert(data.message);
                        $('#eventDetails').modal('hide');
                    }
                });

    }); 

    function convertirURLs(texto) {
        return texto.replace(
        /(https?:\/\/[^\s]+)/g,
        '<a href="$1" target="_blank">$1</a>'
        );
    }

</script>