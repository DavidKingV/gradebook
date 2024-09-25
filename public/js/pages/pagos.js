import { loadingSpinner,errorAlert, errorAlertTimer } from '../common/sweetAlert.js';
import { enviarPeticionAjax } from '../common/ajax.js';
import { initializeDataTable } from "../common/datatables.js"
import { RenderCheckout } from '../common/MercadoLibre/index.js';

let phpPath = "api/Payments.php";

const ChoosePaymentMethod = () => {
    $("#chooseMethod").on("click", function() {
        let method = $("#methodSelect").val();
        if(method == "2"){
            if(window.preferencesId){
                RenderCheckout(window.preferencesId);
            }else{
                errorAlert("No se tienen datos de tus pagos, por favor realiza el pago en plantel");
            }
        }else if(method == "1"){
           $("#MlModal").modal("hide");
           $('#MlModal').on('hidden.bs.modal', function () {
            // Abre el siguiente modal
            $("#stripeModal").modal("show");
        });
        }
    });   
}

$(function() {
    //RenderCheckout(window.preferencesId);
    ChoosePaymentMethod();

    initializeDataTable('#paymentsTable', phpPath, { action: 'getPaymentsList' }, [
        { data: 'payment_id', 'className': 'text-center' },
        { data: 'concept', 'className': 'text-center' },
        { data: null, 'className': 'text-center', render: function (data) {
            if(data.extra != '0.00'){
                return '<span class="badge rounded-pill text-bg-danger">$ '+data.extra+'</span>';
            }else{
                return '<span class="badge rounded-pill text-bg-success">Sin recargos</span>'
            }
        }},
        { data: 'total', 'className': 'text-center', render: function (data) {
            return '$ '+data;
        }
        },
    ]);

});