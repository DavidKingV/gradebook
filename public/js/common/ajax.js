export function enviarPeticionAjax(url, metodo, datos) {
    return $.ajax({
        url: url,
        type: metodo,
        data: datos
    });
}

export function enviarPeticionJson(url, metodo, datos) {
    return $.ajax({
        url: url,
        type: metodo,
        contentType: 'application/json',
        data: JSON.stringify(datos)
    });
}