var AgendaMentor = function(){
    
    var settings;
    
    var Calendar;
    
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
        var $submit = $(":submit", $form);
        var text_btn = $submit.html();
        
        // Bloquear submit
        $submit.html('Enviando, por favor espere...');
        $submit.attr('disabled', true);
        

        var xhr = $.ajax({
            type: "POST",
            url: href,
            data: $form.serialize(),
            dataType: "json"
        });


        xhr.done(function(response){
            
            if(response.status === 'success'){
                // Actualizar calendario
                Calendar.fullCalendar('refetchEvents');                    
            }

            // Mostrar notificacion
            showNotice(response.status, response.message.title, response.message.detail);     
            
            // Desbloquear submit
            $submit.html(text_btn);
            $submit.attr('disabled', false);
        });
    };
    
    /**
     * Funcion para controlar la eliminacion de mentorias
     * 
     * @param {event} e
     * @returns {undefined}
     */
    var deleteMentoriaHandler = function(e){
        e.preventDefault();
        e.stopPropagation();
        
        var $btn = $(this);
        var href = $btn.attr('href');
        var text_btn = $btn.html();
        
        // Bloquear boton
        $btn.html('Enviando, por favor espere...');
        $btn.attr('disabled', true);
        
        var xhr = $.ajax({
            type: "DELETE",
            url: href,
            dataType: "json"
        });
        
        xhr.done(function(response){
            
            if(response.status === 'success'){
                // Actualizar calendario
                Calendar.fullCalendar('refetchEvents');                    
            }
            
            // Mostrar notificacion
            showNotice(response.status, response.message.title, response.message.detail); 
            
            // Desbloquear submit
            $btn.html(text_btn);
            $btn.attr('disabled', false);
            
            // Cerrar modalbox
            $("#modal-mentoria").modal('hide');
        });
    };
    
    return {
        init: function(s){
            settings = s;
            
            Calendar = $('#calendar').fullCalendar({
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
            $('#modal-mentoria').on('click', '.btn-delete-mentoria', deleteMentoriaHandler);
            
        }
    };
    
}();


