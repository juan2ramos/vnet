/**
 * Funcion javascript para obtener mediante ajax contadores de mensajes y solicitudes de amistad
 * 
 * @param {object} settings configuraciones de la funcion: route ruta para envio de peticion
 */
var AlertBadge = (function(settings){
    settings = $.extend({
        route: undefined
    }, settings);
    
    $.getJSON(settings.route, function(json){
        if(json.mensajes_sin_leer != 0)
            $('.inbox-badge').html(json.mensajes_sin_leer);
        else
            $('.inbox-badge').html('');
        
        if(json.amistades_sin_aprobar != 0)
            $('.amistades-badge').html(json.amistades_sin_aprobar);
        else
            $('.amistades-badge').html('');
    });
});

refreshAlertBadge();


var showNotice = function(type, title, message){

    var img;

    if(type === 'success'){
        img = '/img/success.png';
    }
    else if(type === 'info'){
        img = '/img/info.png';
    }
    else if(type === 'warning'){
        img = '/img/warning.png';
    }
    else if(type === 'error'){
        img = '/img/delete.png';
    }        

    $.gritter.add({
        title: title,
        text: message,
        time: 5000,
        image: img
    });
};