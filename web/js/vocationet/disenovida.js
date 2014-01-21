var DisenoVida = (function(settings){
            
    // Pantallazo 2 - Intereses de carrera (Flor)
    $(".step").on("click", ".btn-sub-step", function(){
        var step = $(this).data("step");

        $(".sub-step").hide();
        $(".sub-step-"+step).show();
    });

    // Pantallazo 3 - Intereses de carrera (2)
    var arr_nivel3 = new Array();
    $(".step").on("click", "#form-next-1", function(){  
        var count_nivel3 = 0;
        $(".nivel-act").html('');   

        $(".sub-step .slider-value").each(function(){

            var value = $( this ).attr("value");
            var pregunta = $(this).data("pregunta");                    

            $("#nivel-"+value).append("<li>"+pregunta+"</li>");   

            if(value == 3){
                arr_nivel3.push(pregunta);
                count_nivel3++;
            }                    
        });  

        if(count_nivel3 < 3){
            $("#form-back-2").show();
            $("#form-next-2").hide();
            alert(settings.msg_min_opc_carr);
        }
        else{
            $("#form-back-2").hide();
            $("#form-next-2").show();                    
        }
    });

    // Pantallazo 5 - Valores ocupacionales
    $(".step").on("click", "#form-next-3", function(){
        var count = 0;
        var arr_sort = new Array();
        $("#sort-val-ocup").html('');
        $("#form-step-3 input[type='radio']:checked").each(function(){
            var value = $(this).attr("value");
            var pregunta = $(this).data("pregunta");

            if(value == 1){
                if(count <= 10){
                    $("#sort-val-ocup").append('<li class="label label-primary" id="'+pregunta+'">'+pregunta+'</li>');
                    arr_sort.push(pregunta);
                    count++;                            
                }
            }

        });
        
        if(count < 10){
            $("#form-next-4").hide();
            $("#form-back-4").show();
            alert(settings.msg_min_opc_val);
        }
        else{
            $("#form-next-4").show();
            $("#form-back-4").hide();
        }


        var str_sort = arr_sort.join(",");                
        $("#orden-val-ocup").attr("value", str_sort);            
    });


    // Pantallazo 6 - Analisis
    $(".step").on("click", "#form-next-4", function(){
        var orden = $("#orden-val-ocup").val();
        var list = orden.split(",");
        var length = list.length;
        if(length > 5){
            length = 5;
        }                

        for (var i = 0; i < length; i++) {
            $("#list-val-ocup").append('<li>'+list[i]+'</li>');
        }

        var count = 1;
        $("#form-step-0 .sortable li").each(function(){
            var opcion = $(this).data("opcion");
            if(count <= 5){
                $("#list-int-acad").append('<li>'+opcion+'</li>');
                count++;
            }
        });

        length = arr_nivel3.length;

        for (var i = 0; i < length; i++) {

            $("#table-int-carr tbody").append('\
                <tr class="act-int-arr">\n\
                    <td>\n\
                        '+arr_nivel3[i]+'\
                        <input type="hidden" name="adicional[val_int_carr]['+i+'][nombre]" value="'+arr_nivel3[i]+'" class="nombre"/>\n\
                    </td>\n\
                    <td><div style="width:120px;"><input type="text" name="adicional[val_int_carr]['+i+'][afi_val_ocup]" class="input-number afi_val_ocup" value="0" required="required"/></div></td>\n\
                    <td><div style="width:120px;"><input type="text" name="adicional[val_int_carr]['+i+'][afi_int_acad]" class="input-number afi_int_acad" value="0" required="required"/></div></td>\n\
                    <td>\n\
                        <div class="slider-container slider-percent-container" style="margin-top:0px;"> \n\
                            <div class="" style="text-align: right;"> \n\
                                <div class="slider-info" style="line-height: 20px; font-size: 16px;">0%</div> \n\
                                <input type="hidden" name="adicional[val_int_carr]['+i+'][porc]" value="0" class="slider-value porc" required="required"/> \n\
                            </div> \n\
                            <div class="" style="padding-top: 10px;"> \n\
                                <div class="slider-percent"></div> \n\
                            </div> \n\
                        </div> \n\
                    </td>\n\
                </tr>'
            );
        }

        $(".input-number").TouchSpin({
            min: 0,
            max: 5
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
    });

    //Pantallazo 8 - Metas 
    $(".step").on("click", "#form-next-6", function(){
        var int_carr = new Array();

        $("#table-int-carr .act-int-arr").each(function(i){
            int_carr[i] = new Array();
            int_carr[i]['nombre'] = $(this).find(".nombre").val();
            int_carr[i]['afi_val_ocup'] = parseInt($(this).find(".afi_val_ocup").val());
            int_carr[i]['afi_int_acad'] = parseInt($(this).find(".afi_int_acad").val());
            int_carr[i]['porc'] = parseInt($(this).find(".porc").val());

            int_carr[i]['prom'] = (int_carr[i]['afi_val_ocup'] + int_carr[i]['afi_int_acad'] + int_carr[i]['porc']) / 3;                            
        });

        int_carr.sort(function(a,b){
            return a['prom'] < b['prom'];
        });

        var length = int_carr.length;

        if(length > 3){
            length = 3;
        }

        for(var i = 0; i < length; i++){
            $("#span-int-carr-"+ (i + 1)).html(int_carr[i]['nombre']);
            $("#input-int-carr-"+ (i + 1)).attr("value", int_carr[i]['nombre']);
        }

    });

});


