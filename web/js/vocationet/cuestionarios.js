(function() {
    
    $('#form').stepy({
        backLabel: prev_trans,
        block: true,
        nextLabel: next_trans,
        titleClick: false,
        titleTarget: '.stepy-tab',
        validate: true,
        next: function(){
            $('html,body').animate({
                scrollTop: $("#div-cuestionario").offset().top
            });
        }
    });

    $( ".slider" ).slider({
        min: 1,
        max: 5,
        slide: function( event, ui ) {
            parent = $(this).parents(".slider-container");            
            parent.find(".slider-info").html(ui.value);
            parent.find(".slider-value").val(ui.value);
        }
    });

    $(".sortable").sortable({
        update: function(event, ui){
            var orden = $(this).sortable('toArray').toString();
            $(this).siblings(".sort-value").val(orden);
        }
    }).disableSelection();
    
    $(".input-number").TouchSpin({
        min: 1,
        max: 3
    });
    
    $(".slider-percent").slider({
        min:0,
        max:100,
        slide: function( event, ui ) {
            parent = $(this).parents(".slider-container");            
            parent.find(".slider-info").html(ui.value+"%");
            parent.find(".slider-value").val(ui.value);
        }
    });
    
    $('.option label').click(function(){
        var container = $(this).parents(".options-container");
        var parent = $(this).parent();
        
        container.find(".option").removeClass("active");        
        parent.addClass("active");
    });
    
    $('.option-check label').click(function(){
        //var container = $(this).parents(".options-container");
        var checkbox = $(this).find("input[type=checkbox]");
        var parent = $(this).parent();
        
        if(checkbox.is(":checked")){
            parent.addClass("active");        
        }
        else{
            parent.removeClass("active");        
        }
    });
    
    $.extend(jQuery.validator.messages, {
        required: msg_required_pregunta
    });

    $("#form").validate();
    
    $("fieldset").removeAttr('title');
    
    $(".button-back").remove();
    
    
})();