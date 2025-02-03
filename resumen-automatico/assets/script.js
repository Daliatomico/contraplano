jQuery(document).ready(function ($) {
    // Guarda el contenido completo del post (sin ningún cambio)
    let contenidoOriginal = $("#contenido-original").html(); // Guarda el contenido completo del artículo

    // Esta variable almacena el resumen generado por la API de OpenAI
    let resumenGenerado = ""; // Inicialmente no hay resumen generado

    // Al hacer clic en el botón con id "toggle-resumen"
    $("#toggle-resumen").click(function () {
        let boton = $(this); // Referencia al botón que ha sido presionado

        // Verifica si el texto del botón es igual "Ver Resumen"
        if (boton.text() === "Ver Resumen") {
            // Si el resumen no ha sido generado aún
            if (!resumenGenerado) {
                // Tomamos el texto original del contenido, quitando las imágenes y otros elementos del HTML
                let textoOriginal = $("#contenido-original").text().trim(); // Solo el texto del artículo (sin las imágenes)

                // Verifica si el contenido es demasiado corto para ser resumido
                if (textoOriginal.length < 200) {
                    alert("El contenido es demasiado corto para resumir."); // Alerta si el texto es demasiado corto
                    return; // Sale de la función si el texto es demasiado corto
                }

                boton.text("Generando..."); // Se cambia el texto del botón a "Generando..." mientras se espera el resumen

                // Función para realizar la solicitud AJAX a la API de OpenAI para generar el resumen
                function generarResumen() {
                    $.ajax({
                        url: 'https://api.openai.com/v1/chat/completions',  // URL de la API
                        type: "POST",
                        headers: {
                            'Authorization': 'Bearer ' + openai_api.api_key
                        },
                        contentType: "application/json",
                        data: JSON.stringify({
                            model: "gpt-3.5-turbo",  // Usar el modelo adecuado
                            messages: [{
                                role: "user",
                                content: "Resumir el siguiente texto:\n\n" + textoOriginal
                            }],
                            max_tokens: 150,
                            temperature: 0.7
                        }),
                        success: function (response) {
                            resumenGenerado = response.choices[0].message.content.trim();  // Cambié la forma de obtener el resumen
                            $("#resumen-texto").html(resumenGenerado).show();
                            $("#contenido-original").hide();
                            boton.text("Ver Completo");
                        },
                        error: function (xhr) {
                            if (xhr.status === 429) {
                                // Si el error es 429 (Too Many Requests), espera 10 segundos y vuelve a intentar
                                setTimeout(function() {
                                    generarResumen(); // Reintentar la solicitud
                                }, 10000); // 10 segundos de espera antes de reintentar
                            } else {
                                alert("Error al generar el resumen. Intenta nuevamente.");
                                boton.text("Ver Resumen");
                            }
                        }
                    });
                }

                // Llamamos a la función para generar el resumen
                generarResumen();
                
            } else {
                // Si ya se generó el resumen, simplemente alternamos entre el contenido original y el resumen
                $("#resumen-texto").show(); // Mostrar el resumen
                $("#contenido-original").hide(); // Ocultar el contenido original
                boton.text("Ver Completo"); // Se cambia el texto del botón a "Ver Completo"
            }
        } else { // Si el texto del botón es "Ver Completo"
            // Ocultamos el resumen y mostramos el contenido original
            $("#resumen-texto").hide(); // Se Oculta el resumen
            $("#contenido-original").show(); // Se Muestra el contenido original
            boton.text("Ver Resumen"); // Se cambia el texto del botón a "Ver Resumen"
        }
    });
});
