(function () {
    var html_opcion = '<li><input type="text" name="form[opciones][]" required="required" class="form-control" style="width: 70%;"/><input type="text" name="form[pesos][]" value="0" required="required" class="form-control" style="width: 10%;"/><button type="button" class="btn btn-danger btn-delete-opcion"><i class="icon-remove"></i></button></li>';

    var val_tipo_pregunta = $('#form_preguntaTipo').val();
    toggleOpciones(val_tipo_pregunta);


    $("#btn-add-opcion").on('click', function(){
        $("#ol-opciones").append(html_opcion);
    });

    $("#ol-opciones").on('click', '.btn-delete-opcion', function(){
        $(this).parent().remove();
    });

    $('#form_preguntaTipo').on('change', function(){
        toggleOpciones($(this).val());            
    });

    $(".btn-delete-pregunta").on('click', function(e){
        e.preventDefault();
        var route = $(this).attr('href');
        var remove = $(this).data('remove');

        $.ajax({
            type: "POST",
            url: route,
            dataType: "json",
            success: function (response){
                if(response.status == "success"){
                    $.gritter.add({
                        title: response.message,
                        text: response.detail,
                        time: 5000,
                        image: asset_path+'img/success.png'
                    });    
                    $(remove).remove();
                }
                else if(response.status == "error"){
                   $.gritter.add({
                        title: response.message,
                        text: response.detail,
                        time: 5000,
                        image: asset_path+'img/delete.png'
                    });
                }
            },
            error: function() {
                console.error('AJAX Error');                    
            }
        });
    });

    function toggleOpciones(tipoPregunta){
        if(tipoPregunta == 1 || tipoPregunta == 2 || tipoPregunta == 3){
            $("#div-opciones-pregunta").show();
            $("#ol-opciones li input").attr('required','required');
        }
        else{ 
            $("#div-opciones-pregunta").hide();
            $("#ol-opciones li input").removeAttr('required');
        }   
    }

})();
        


