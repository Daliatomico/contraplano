<?php
/**
 * Plugin Name: Resumen Automático de Noticias
 * Description: Genera un resumen inteligente de la noticia usando la API de OpenAI.
 * Version: 1.0
 * Author: Alan Navarrete - Elizabeth Lohse
 */

// Añadir scripts y estilos solo en entradas
function resumen_noticias_scripts() {
    // Verifica si estamos en una entrada individual (post único)
    if (is_single()) { // Solo en posts individuales
        // Encola el archivo JavaScript que manejará la funcionalidad del resumen
        // El tercer parámetro indica que jQuery debe estar disponible antes de ejecutar este script
        wp_enqueue_script('resumen-noticias', plugin_dir_url(__FILE__) . 'assets/script.js', array('jquery'), null, true);

        // Usamos wp_localize_script para pasar variables de PHP a JavaScript
        // 'openai_api' es el nombre de la variable JavaScript que contiene la URL y la API Key de OpenAI
        wp_localize_script('resumen-noticias', 'openai_api', array(
            'url' => 'https://api.openai.com/v1/chat/completions',  // URL de la API de OpenAI para resúmenes
            'api_key' => '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' // API key de OpenAI
        ));

        // Archivo CSS para el diseño del botón y el contenedor del resumen
        wp_enqueue_style('resumen-noticias-css', plugin_dir_url(__FILE__) . 'assets/estilos.css');
    }
}

// Añade la función 'resumen_noticias_scripts' al 'wp_enqueue_scripts', que es el momento en que WordPress agrega los scripts y estilos
add_action('wp_enqueue_scripts', 'resumen_noticias_scripts');

// Agregar botón de resumen automáticamente después del contenido de los posts
function agregar_boton_resumen($content) {
    // Verifica si estamos en una entrada individual
    if (is_single()) {
        // Elimina las imágenes del contenido para solo procesar el texto
        $content_solo_texto = preg_replace('/<img[^>]+>/', '', $content); // Eliminar imágenes

        // Generamos el HTML para el contenedor del botón de resumen
        // El contenido original se guarda en 'contenido-original', y el resumen se guardará en 'resumen-texto', pero inicialmente está oculto
        $boton_resumen = '
        <div class="resumen-container">
            <div id="contenido-original">' . $content_solo_texto . '</div> <!-- Aquí se coloca el contenido sin imágenes -->
            <p id="resumen-texto" style="display: none;"></p> <!-- El resumen está oculto al principio -->
            <button id="toggle-resumen" class="btn-resumen">Ver Resumen</button> <!-- El botón para alternar el resumen -->
        </div>';

        // Retorna el contenido reemplazado por el nuevo HTML que incluye el botón
        return $boton_resumen;
    }
    // Si no estamos en un post individual, se devuelve el contenido sin cambios
    return $content;
}

// Añade la función 'agregar_boton_resumen' al 'the_content', lo que permite modificar el contenido de la entrada antes de mostrarlo
add_filter('the_content', 'agregar_boton_resumen');

?>
