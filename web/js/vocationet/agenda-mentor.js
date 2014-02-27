var AgendaMentor = function(){
    
    var settings;
    
    /**
     * Funcion que carga el detalle de una mentoria en el modalbox
     * 
     * @param {event} e
     * @returns {undefined}
     */
    var showMentoriaHandler = function(e){
        e.preventDefault();

        $(".modal-body").html('<div style="text-align: center"><img src="/img/ajax-loader.gif" /></div>');

        var url = $(this).attr('href');        
        $("#modal-mentoria").modal('show');

        $.get(url, function(response){
            $(".modal-body").html(response);
        });
    };
    
    /**
     * Funcion que envia el formulario de creacion de mentorias mediante ajax
     * 
     * @param {event} e
     * @returns {undefined}
     */
    var submitMentoriaHandler = function(e){
        e.preventDefault();
            e.stopPropagation();
            
            var $form = $(this);
            var href = $form.attr('action');
            
            
            var xhr = $.ajax({
                type: "POST",
                url: href,
                data: $form.serialize(),
                dataType: "json"
            });
            
            
            xhr.done(function(response){
                if(response.status === 'success'){
                    // Actualizar calendario
                    //...
                }
                
                // Mostrar notificacion
                showNotice(response.status, response.message);
                
            });
            
            
    };
    
    var showNotice = function(type, message){
        
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
            title: message.title,
            text: message.detail,
            time: 5000,
            image: img
        });
    };
    
    return {
        init: function(s){
            settings = s;
            
            $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,basicWeek,basicDay'
                },
                //defaultView: 'basicWeek',
                editable: false,
                droppable: false,
                events: settings.items_agenda_path
            });      
            
            $("#calendar").on('click', '.fc-event', showMentoriaHandler);
            $('#form-mentoria').on('submit', submitMentoriaHandler);
            
        }
    }
    
}();


