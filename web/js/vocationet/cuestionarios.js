var Cuestionario = (function(settings) {
    
    settings = $.extend({
        slider_min: 1,
        slide_max: 5,
        touchSpin_min: 1,
        touchSpin_max: 3,
        stepy: true,
        stepy_titleClick: false
    }, settings);
    
    $(".slider-number-container .slider-value").val(settings.slider_min);
    $(".slider-number-container .slider-info").html(settings.slider_min);
    
    if(settings.stepy === true){
        $('#form').stepy({
            backLabel: prev_trans,
            block: true,
            nextLabel: next_trans,
            titleClick: settings.stepy_titleClick,
            titleTarget: '.stepy-tab',
            validate: true,
            next: function(){
                $('html,body').animate({
                    scrollTop: $("#div-cuestionario").offset().top
                });
                $(".sub-step").hide();
            }
        });
    }

    $( ".slider" ).slider({
        min: settings.slider_min,
        max: settings.slide_max,
        value: settings.slider_min,
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
        min: settings.touchSpin_min,
        max: settings.touchSpin_max
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
    
    
});