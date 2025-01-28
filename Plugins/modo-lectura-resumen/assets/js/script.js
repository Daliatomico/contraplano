jQuery(document).ready(function($) {
    // Al hacer clic en el botón, alternar entre mostrar el resumen y el contenido completo
    $('.mlr-toggle-button').on('click', function() {
        var button = $(this);
        var summary = button.siblings('.mlr-summary');
        var fullContent = button.siblings('.mlr-full-content');

        // Alternar la visibilidad del resumen y el contenido completo
        if (summary.is(':visible')) {
            summary.hide();
            fullContent.show();
            button.text('Ver Resumen');
        } else {
            fullContent.hide();
            summary.show();
            button.text('Ver Artículo Completo');
        }
    });
});
