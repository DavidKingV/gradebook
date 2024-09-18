import { loadingSpinner,errorAlert, errorAlertTimer } from '../common/sweetAlert.js';
import { enviarPeticionAjax } from '../common/ajax.js';
import { initializeDataTable } from "../common/datatables.js"

let phpPath = "api/Payments.php";

$(function() {

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