jQuery(document).ready(function ($) {

    $('#wiki-ema-sync-form-admin').submit(function () {
        $('#spinner_sync').show();
        $('#sync_button').attr('disabled', true);
        data = {
            action: 'wiki_ema_sincroniza_autor_obra',
            wiki_ema_sync_nonce: wiki_ema_sync_vars.wiki_ema_sync_nonce
        };

        $.post(ajaxurl , data, function(resposta){
            $('#sync-log').html(resposta);
            $('#spinner_sync').hide();
            $('#sync_button').attr('disabled', false);
        });

        return false;
    });

    $('#wiki-ema-update-form-admin').submit(function () {
        $('#spinner_update').show();
        $('#update_button').attr('disabled', true);
        data = {
            action: 'wiki_ema_atualiza_todos_cpts',
            wiki_ema_update_nonce: wiki_ema_update_vars.wiki_ema_update_nonce
        };

        $.post(ajaxurl , data, function(resposta){
            $('#update-log').html(resposta);
            $('#spinner_update').hide();
            $('#update_button').attr('disabled', false);
        });

        return false;
    });

    
});