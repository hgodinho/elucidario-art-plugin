jQuery(document).ready(function ($) {

    $('#wiki-ema-form').submit(function () {

        data = {
            action: 'sincroniza_autor_obra',
        };

        $.post(ajaxurl, data, function (resposta) {
            alert(resposta);
        });

        return false;
    });
});