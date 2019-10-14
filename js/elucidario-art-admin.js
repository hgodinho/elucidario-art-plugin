jQuery(document).ready(function ($) {

    $('#elucidario-art-sync-form-admin').submit(function () {
        $('#spinner_sync').show();
        $('#sync_button').attr('disabled', true);
        data = {
            action: 'elucidario_art_sincroniza_autor_obra',
            elucidario_art_sync_nonce: elucidario_art_sync_vars.elucidario_art_sync_nonce
        };

        $.post(ajaxurl , data, function(resposta){
            $('#sync-log').html(resposta);
            $('#spinner_sync').hide();
            $('#sync_button').attr('disabled', false);
        });

        return false;
    });

    $('#elucidario-art-update-form-admin').submit(function () {
        $('#spinner_update').show();
        $('#update_button').attr('disabled', true);
        data = {
            action: 'elucidario_art_atualiza_todos_cpts',
            elucidario_art_update_nonce: elucidario_art_update_vars.elucidario_art_update_nonce
        };

        $.post(ajaxurl , data, function(resposta){
            $('#update-log').html(resposta);
            $('#spinner_update').hide();
            $('#update_button').attr('disabled', false);
        });

        return false;
    });

    
});