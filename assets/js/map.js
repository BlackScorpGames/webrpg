$(function () {
return;
    var arrows = $('.arrows > div > a');

    var map = $('.mapWrapper');
    var body =$('body');
    map.on('redraw', function (event, direction, data) {
        console.log(data);
    });


    arrows.on('click', function () {
        var direction = $(this).attr('data-direction');
        $.ajax("ajax/character/move/" + direction, {
                "dataType": "json",
                "success": function (data) {
                    if (data instanceof Array) {
                        return;
                    }
                    body.trigger('characterMoved',[]);
                    map.trigger('redraw', [direction, data]);
                }
            }
        );

    }).each(function(){
        $(this).attr('href', '#' + $(this).attr('data-direction'));
    });

});