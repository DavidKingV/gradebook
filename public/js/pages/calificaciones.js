import { initializeDataTable } from '../common/datatables.js';

function getCuatrimestre(timestamp) {
    const date = new Date(timestamp);
    const year = date.getFullYear();
    const month = date.getMonth() + 1; // Los meses en JavaScript son de 0 a 11, por eso sumamos 1
    let cuatrimestre;

    if (month <= 4) {
        cuatrimestre = 1;
    } else if (month <= 8) {
        cuatrimestre = 2;
    } else {
        cuatrimestre = 3;
    }

    return `${cuatrimestre.toString().padStart(2, '0')}-${year}`;
}

function generatePDF(data) {
    const { jsPDF } = window.jspdf;
    var doc = new jsPDF();
    var cuatrimestre = getCuatrimestre(data[0].update_at);

    var img = new Image();
    img.src = 'assets/esmefis.png';

    doc.text('Calificaciones del periodo ' + cuatrimestre, 10, 10);
    doc.addImage(img, 'PNG', 170, 5, 20, 20);
    doc.setFontSize(12);
    doc.text('Fecha de generación: ' + new Date().toLocaleDateString(), 10, 20);
    doc.setFontSize(10);
    doc.text('Este documento fue generado automáticamente por el sistema y no tiene validez oficial.', 10, 30);
    doc.autoTable({
      head: [['ID', 'Materia', 'Nota Continua', 'Nota Examen', 'Nota Final']],
      body: data.map(function(row) {
        return [row.grade_id, row.subject_name, row.continuous_grade, row.exam_grade, row.final_grade];
      }),
      startY: 40
    });
    doc.save('calificaciones-' + cuatrimestre + '.pdf');
}

//crea una funcion dinamica para detectar si hay algun checkbox seleccionado, entonces habilita el boton de generar pdf
function checkSelected() {
    $('#subjects').on('change', 'input[type="checkbox"]', function() {
        var table = $('#subjects').DataTable();
        var selectedRows = table.rows({ 'search': 'applied' }).nodes();
        var selectedCheckboxes = $('input[type="checkbox"]:checked', selectedRows);
        var generateButton = $('#generate');

        if (selectedCheckboxes.length > 0) {
            generateButton.prop('disabled', false);
        } else {
            generateButton.prop('disabled', true);
        }
    });
}


$(function() {
    initializeDataTable('#subjects', 'api/Grades.php', { action: 'getGrades' }, [
        { data: null, render: function(data, type, row) { return ' <input class="form-check-input row-checkbox" type="checkbox" value="">' }, 'className': 'text-center' }, 
        { data: 'grade_id', 'className': 'text-center' },
        { data: null, render: function(data, type, row) { 
            if (row.subject_child_name == null){  
                return '<h6>'+row.subject_name+'</h6>';
            }
            else {
                return '<h6>'+row.subject_name+'</h6><p>'+row.subject_child_name+'</p>';
            }
        }, 'className': 'text-center' },
        { data: 'continuous_grade', 'className': 'text-center' },
        { data: 'exam_grade', 'className': 'text-center' },
        { data: 'final_grade', 'className': 'text-center' },
        { data: null, render: function(data, type, row) { return getCuatrimestre(row.update_at) }, 'className': 'text-center' },
    ]);

    $('#generate').prop('disabled', true);

    checkSelected();

    $('#selectAll').on('click', function() {
        var table = $('#subjects').DataTable();
        var rows = table.rows({ 'search': 'applied' }).nodes();
        $('input[type="checkbox"]', rows).prop('checked', this.checked);
    });

    $('#generate').on('click', function() {
        let table = $('#subjects').DataTable(); 
        var selectedData = [];
        table.$('input[type="checkbox"]:checked').each(function() {
          var row = $(this).closest('tr');
          var rowData = table.row(row).data();
          selectedData.push(rowData);
        });
  
        generatePDF(selectedData);
      });

});