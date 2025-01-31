<?php
/**
 * Plugin Name: Resumen Inteligente de Noticias
 * Plugin URI: https://tuweb.com
 * Description: Un plugin que usa NLP Cloud para resumir artículos noticiosos en WordPress.
 * Version: 1.0
 * Author: Alan Navarrete
 * Author URI: https://tuweb.com
 * License: GPL2
 */

if (!defined('ABSPATH')) {
    exit; // Salir si se accede directamente
}

// Agregar los scripts y estilos css
function resumen_noticias_enqueue_scripts() {
    wp_enqueue_style('resumen-noticias-css', plugin_dir_url(__FILE__) . 'assets/css/estilos.css');
    wp_enqueue_script('resumen-noticias-js', plugin_dir_url(__FILE__) . 'assets/js/script.js', array('jquery'), null, true);
    wp_localize_script('resumen-noticias-js', 'resumenNoticiasAjax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nlp_api_key' => 'TU_CLAVE_DE_API' // Reemplaza con tu clave real de NLP Cloud
    ));
}
add_action('wp_enqueue_scripts', 'resumen_noticias_enqueue_scripts');

// Agregar el botón después del contenido
function resumen_noticias_agregar_boton($content) {
    if (is_single()) {
        $boton = '<button id="resumen-noticias-boton">Resumir Noticia</button>';
        $contenedor = '<div id="resumen-noticias-contenido">' . $content . '</div>';
        return $boton . $contenedor;
    }
    return $content;
}
add_filter('the_content', 'resumen_noticias_agregar_boton');

// Función AJAX para generar el resumen
function resumen_noticias_generar_resumen() {
    $api_key = 'TU_CLAVE_DE_API'; // Reemplaza con tu clave real
    $texto_original = $_POST['contenido'];

    // Hacer la solicitud a NLP Cloud
    $response = wp_remote_post("https://api.nlpcloud.io/v1/gpt-j/summarization", array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type' => 'application/json'
        ),
        'body' => json_encode(array('text' => $texto_original))
    ));

    if (is_wp_error($response)) {
        echo json_encode(array('error' => 'Error al conectar con NLP Cloud'));
    } else {
        $body = json_decode(wp_remote_retrieve_body($response), true);
        echo json_encode(array('resumen' => $body['summary'] ?? 'No se pudo generar el resumen'));
    }

    wp_die();
}
add_action('wp_ajax_resumen_noticias', 'resumen_noticias_generar_resumen');
add_action('wp_ajax_nopriv_resumen_noticias', 'resumen_noticias_generar_resumen');

