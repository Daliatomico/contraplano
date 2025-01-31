jQuery(document).ready(function ($) {
    let originalContent = $('#resumen-noticias-contenido').html();
    let resumenGenerado = '';

    $('#resumen-noticias-boton').click(function () {
        let boton = $(this);
        
        if (boton.text() === 'Resumir Noticia') {
            // Verificar si ya tenemos un resumen guardado
            if (resumenGenerado) {
                $('#resumen-noticias-contenido').html(resumenGenerado);
                boton.text('Mostrar Noticia Completa');
            } else {
                boton.text('Generando resumen...');
                $.ajax({
                    type: 'POST',
                    url: resumenNoticiasAjax.ajax_url,
                    data: {
                        action: 'resumen_noticias',
                        contenido: originalContent
                    },
                    success: function (response) {
                        let data = JSON.parse(response);
                        if (data.resumen) {
                            resumenGenerado = data.resumen;
                            $('#resumen-noticias-contenido').html(resumenGenerado);
                            boton.text('Mostrar Noticia Completa');
                        } else {
                            alert('No se pudo generar el resumen.');
                            boton.text('Resumir Noticia');
                        }
                    },
                    error: function () {
                        alert('Error al generar el resumen.');
                        boton.text('Resumir Noticia');
                    }
                });
            }
        } else {
            $('#resumen-noticias-contenido').html(originalContent);
            boton.text('Resumir Noticia');
        }
    });
});
