export function loadingAlert() {
    Swal.fire({
        title: "Cargando",
        html: "Espera un momento...",
        timerProgressBar: true,
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
          },
    })
}

export function loadingSpinner(show, element) {
    if (show) {
        $(element).append('<div class="d-flex justify-content-center py-5"><div class="spinner-border" style="width: 3rem; height: 3rem;" role="status"><span class="visually-hidden">Loading...</span></div></div>');
    } else {
        $(element).empty();
    }
}

export function successAlert(message) {
    return Swal.fire({
        icon: 'success',
        title: 'Completado',
        text: message,
    })
}

export function errorAlert(message) {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: message,
    })
}

export function errorAlertTimer(message) {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: message,
        showConfirmButton: false
    })
}

export function confirmAlert(message, confirmButtonText, cancelButtonText, confirmCallback) {
    Swal.fire({
        title: '¿Estás seguro de realizar esta acción?',
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: confirmButtonText,
        cancelButtonText: cancelButtonText,
    }).then((result) => {
        if (result.isConfirmed) {
            confirmCallback();
        }
    });
}

export function confirmCloseSession(message, confirmButtonText, cancelButtonText, confirmCallback) {
    Swal.fire({
        title: 'Cerrar sesión',
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: confirmButtonText,
        cancelButtonText: cancelButtonText,
    }).then((result) => {
        if (result.isConfirmed) {
            confirmCallback();
        }
    });
}