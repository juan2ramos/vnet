var PruebasUsuario = function(){
    
    var settings;
    var AlterEstudiante = false;
    var MentoriasEstudiante = false;


    var cargarAlterEstudianteHandler = function(){
        if(AlterEstudiante === false){
            var xhr = $.get(settings.routes.alter_estudiante);

            xhr.done(function(response){
                AlterEstudiante = JSON.parse(response);

                cargarAlternativasEstudiante();
            });
        }
    };

    var cargarMentoriasEstudianteHandler = function(){
        if(MentoriasEstudiante === false){
            var xhr = $.get(settings.routes.mentorias_estudiante);

            xhr.done(function(response){

                MentoriasEstudiante = JSON.parse(response);
                cargarMentoriasEstudiante();
            });
        }
    };


    var cargarAlternativasEstudiante = function(){            
        var $lista = $("<ul></ul>");

        if(AlterEstudiante.length !== 0){
            AlterEstudiante.forEach(function(carrera){
                $lista.append('<li>'+carrera.nombre+'</li>');
            });
            var label = '<p>'+settings.trans.label_alter_estudiante+'</p>'
        }
        else{
            AlterEstudiante = false;
            var label = '<p>'+settings.trans.label_no_alter_estudiante+'</p>'                
        }

        $("#modal-body-mlab").html(label);
        $("#modal-body-mlab").append($lista);

    };

    var cargarMentoriasEstudiante = function(){
        var $lista = $("<ul></ul>");

        if(MentoriasEstudiante.length !== 0){

            MentoriasEstudiante.forEach(function(mentoria){
                if(mentoria.mentoriaEstado)
                {
                    var estado = settings.trans.finalizada;
                }
                else{
                    var estado = settings.trans.no_finalizada;
                }

                $lista.append('<li>'+mentoria.mentoriaInicio.date+' - '+ mentoria.usuarioNombre +' ' + mentoria.usuarioApellido + ' (' + estado + ')</li>');
            });

            var label = '<p>'+settings.trans.estado_mentorias+'</p>'
        }
        else{
            MentoriasEstudiante = false;
            var label = '<p>'+settings.trans.no_mentorias+'</p>'                
        }

        $("#modal-body-rment").html(label);
        $("#modal-body-rment").append($lista);
    };

    var cargarModalReporteHandler = function(){
        var $btn = $(this);
        var $input = $("#txt-form-id");
        $input.val($btn.data('id'));
    };
    
    
    var aprobarPruebaHandler = function(){
        var $btn = $(this);
        var form_id = $btn.data('id');

        $btn.html('Cargando...');
        $btn.attr('disabled', true);
        var xhr = $.post(settings.routes.aprobar_prueba, {form_id: form_id});

        xhr.done(function(response){
            response = JSON.parse(response);

            if(response.status === 'success'){
                // Actualizar icono de aprobado
                var $icon_container = $btn.parent().siblings('.aprob-icon');
                $icon_container.html(settings.html_icon_ok);

                // Eliminar boton
                $btn.remove();
            }
            
            // Notificacion
            showNotice(response.status, response.message.title, response.message.detail);                

        });
    };
    
    return {
        init: function(s){
            settings = s;
            
            $("#btn-modal-mlab").on("click", cargarAlterEstudianteHandler);
            $("#btn-modal-rment").on('click', cargarMentoriasEstudianteHandler);            
            $(".btn-cargar-reporte").on('click', cargarModalReporteHandler);
            $(".btn-aprobar").on('click', aprobarPruebaHandler);
        }
    }
    
}();