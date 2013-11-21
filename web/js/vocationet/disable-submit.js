$(document).ready(function () {
    $("body").on("submit", "form", function() {
        var input = $(":submit", this);
        var tagName = input.prop("tagName");
        
        if(tagName == 'BUTTON'){
            //var default_text = input.html();
            input.html("Enviando...");
        }
        else if(tagName == 'INPUT'){
            //var default_text = input.attr('value');
            input.attr('value', 'Enviando...');
        }
        
        input.attr('disabled', true);
    });
});
