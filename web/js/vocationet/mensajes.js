var Mensajes = (function (settings){

    getMensajes(settings.inboxRoute);

    $("#btn-inbox").on('click', function(e){
        e.preventDefault();
        getMensajes(settings.inboxRoute);

        $(".inbox-nav li").removeClass('active');
        $(this).parent().addClass('active');

    });
    $("#btn-sent").on('click', function(e){
        e.preventDefault();

        getMensajes(settings.sentMessagesRoute);

        $(".inbox-nav li").removeClass('active');
        $(this).parent().addClass('active');
    });
    $("#btn-unread").on('click', function(e){
        e.preventDefault();

        getMensajes(settings.unreadMessagesRoute);

        $(".inbox-nav li").removeClass('active');
        $(this).parent().addClass('active');
    });
    $("#btn-trash").on('click', function(e){
        e.preventDefault();

        getMensajes(settings.trashMessagesRoute);

        $(".inbox-nav li").removeClass('active');
        $(this).parent().addClass('active');
    });            
    $("#inbox-tbody").on('click', '.lnk-msg', function(e){
        e.preventDefault();

        var mid = $(this).data('id');
        route = settings.showMessageRoute.replace("MID", mid);
        $("#inbox-tbody").html('<div style="text-align:center;"><img src="'+ asset_path +'img/ajax-loader.gif"></div>');
        $.get(route, {},
            function(data){
                $("#inbox-tbody").html(data);
                refreshAlertBadge();
        });
    });

    $("#inbox-tbody").on('click', '.btn-delete-msg, .btn-recovery-msg', function(e){
        e.preventDefault();
        
        var href = $(this).data('href');

        $("#inbox-tbody").html('<div style="text-align:center;"><img src="'+ asset_path +'img/ajax-loader.gif"></div>');
        $.ajax({
            type: "POST",
            url: href,
            dataType: "json",
            success: function (response){
                if(response.status === "success"){
                    $(".inbox-nav li").removeClass('active');
                    $("#btn-inbox").parent().addClass('active');

                    $.get(settings.inboxRoute, {},
                        function(data){
                            $("#inbox-tbody").html(data);
                            refreshAlertBadge();
                    });
                }
            },
            error: function() {
                console.error('AJAX Error');                    
            }
        });
    });

    $("#inbox-tbody").on('click', '.btn-forward', function(e){
        e.preventDefault();
        $(".modal-forward").modal('show');
    });

    $("#inbox-tbody").on('click', '.btn-reply', function(e){
        e.preventDefault();
        $(".modal-reply").modal('show');
    });


    $("#inbox-tbody").on('submit', 'form', function(e){
        e.preventDefault();        

        var form = $(this);
        var btn = $(":submit", this);

        var oldHtmlBtn = btn.html();

        btn.attr('disabled', true);
        btn.html(submit_text);

        $.ajax({
            type: "POST",
            url: form.attr('action'),
            data: form.serialize(),
            dataType: "json",
            success: function (response){
                if(response.status == "success"){
                    btn.removeAttr('disabled');
                    btn.html(oldHtmlBtn);
                    $.gritter.add({
                        title: response.title_message,
                        text: response.message,
                        time: 5000,
                        image: asset_path+'img/success.png'
                    });                       
                    $(".modal").modal('hide');

                }
                else if(response.status == "error"){
                    btn.removeAttr('disabled');
                    btn.html(oldHtmlBtn);
                    $.gritter.add({
                        title: response.title_message,
                        text: response.message,
                        time: 5000,
                        image: asset_path+'img/delete.png'
                    });
                }
            },
            error: function() {
                console.error('AJAX Error');                    
            }
        });
        return false;

    });


    function getMensajes(route){
        $("#inbox-tbody").html('<div style="text-align:center;"><img src="'+ asset_path +'img/ajax-loader.gif"></div>');
        $.get(route, {},
            function(data){
                $("#inbox-tbody").html(data);
                refreshAlertBadge();
        });
    }

});


