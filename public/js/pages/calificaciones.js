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

$(function() {
    initializeDataTable('#subjects', 'api/Grades.php', { action: 'getGrades' }, [
        { data: null, render: function(data, type, row) { return ' <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">' }, 'className': 'text-center' }, 
        { data: 'grade_id' },
        { data: null, render: function(data, type, row) { 
            if (row.subject_child_name == null){  
                return '<h6>'+row.subject_name+'</h6>';
            }
            else {
                return '<h6>'+row.subject_name+'</h6><p>'+row.subject_child_name+'</p>';
            }
        }, 'className': 'text-center' },
        { data: 'continuous_grade' },
        { data: 'exam_grade' },
        { data: 'final_grade' },
        { data: null, render: function(data, type, row) { return getCuatrimestre(row.update_at) }, 'className': 'text-center' },
    ]);
});