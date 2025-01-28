<?php
/**
 * Plugin Name: Modo Lectura Resumen
 * Description: Un plugin que permite alternar entre el resumen inteligente y el contenido completo de una entrada.
 * Version: 1.0
 * Author: Tu Nombre
 * License: GPL2
 */

// Evitar el acceso directo
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Función para generar el resumen inteligente del contenido
function mlr_generate_summary( $content ) {
    // Eliminar las etiquetas HTML del contenido
    $clean_content = strip_tags( $content );

    // Usar la clave API de OpenAI desde wp-config.php
    $api_key = defined('OPENAI_API_KEY') ? OPENAI_API_KEY : ''; 

    if (!$api_key) {
        return 'Clave API no definida.';
    }

    // Llamada a la API de OpenAI para generar el resumen
    $response = wp_remote_post('https://api.openai.com/v1/completions', [
        'method'    => 'POST',
        'headers'   => [
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type' => 'application/json',
        ],
        'body'      => json_encode([
            'model' => 'text-davinci-003',  // Modelo de OpenAI
            'prompt' => 'Resumir el siguiente texto de manera coherente y concisa: ' . $clean_content,
            'max_tokens' => 150,  // Limitar el resumen a 150 tokens
        ]),
    ]);

    $body = wp_remote_retrieve_body( $response );
    $data = json_decode( $body );
    
    // Verificar si la API ha devuelto un resumen válido
    if ( isset($data->choices[0]->text) ) {
        $summary = $data->choices[0]->text;
    } else {
        $summary = 'No se pudo generar el resumen'; // Si no hay resumen, mostrar mensaje por defecto
    }

    return $summary . '...'; // Agregar puntos suspensivos al final del resumen
}

// Función para mostrar el contenido completo y el resumen
function mlr_display_summary( $content ) {
    if ( is_single() ) {
        // Generar el resumen inteligente
        $summary = mlr_generate_summary( $content );

        // Obtener el título y el primer párrafo
        preg_match( '/<h1.*?>(.*?)<\/h1>/', $content, $title_matches );
        preg_match( '/<p.*?>(.*?)<\/p>/', $content, $first_paragraph_matches );

        $title = isset($title_matches[1]) ? $title_matches[1] : '';
        $first_paragraph = isset($first_paragraph_matches[1]) ? $first_paragraph_matches[1] : '';

        // Crear el HTML para el contenido completo y el resumen
        $full_content_html = '<div class="mlr-full-content" style="display: block;">' . $content . '</div>';
        $summary_html = '<div class="mlr-summary" style="display: none;">' . $summary . '</div>';

        // Crear el botón para alternar entre el resumen y el contenido completo
        $toggle_button = '<button class="mlr-toggle-button" data-toggle="summary">Ver Resumen</button>';

        // Insertamos el título, el primer párrafo, el botón, el resumen y el contenido completo
        $content_with_button = '<h1>' . $title . '</h1>';
        $content_with_button .= '<p>' . $first_paragraph . '</p>';
        $content_with_button .= $toggle_button . $summary_html . $full_content_html;

        return $content_with_button;
    }

    return $content;
}
add_filter( 'the_content', 'mlr_display_summary' );

// Encolar los scripts y estilos del plugin
function mlr_enqueue_scripts() {
    wp_enqueue_style( 'mlr-style', plugin_dir_url( __FILE__ ) . 'assets/css/estilo.css' );
    wp_enqueue_script( 'mlr-script', plugin_dir_url( __FILE__ ) . 'assets/js/script.js', array('jquery'), false, true );
}
add_action( 'wp_enqueue_scripts', 'mlr_enqueue_scripts' );
