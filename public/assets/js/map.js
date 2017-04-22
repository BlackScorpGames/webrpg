$(function () {
    var arrows = $('.arrows > div > a');

    var map = $('.mapWrapper');

    map.on('redraw', function (event, data) {

        for (var layerName in data) {
            var layerData = data[layerName];


            for (var tileIndex in layerData) {

                var currentData = layerData[tileIndex];

                if (null === currentData) {
                    continue;
                }

                var coordinateClass = 'Y' + currentData['coordinates']['y'] + 'X' + currentData['coordinates']['x'];
                var currentTile = $('.mapWrapper').find('.map.' + layerName + ' > .noContent.' + coordinateClass);

                if (0 === currentTile.length) {

                    continue;
                }


                if (currentData.tileSetName) {

                    currentTile.css({
                        'background-position': '',
                        'background-size': ''
                    }).attr('class', 'tile ' + currentData.tileSetName + ' ' + coordinateClass);

                }

                if (currentData.size) {
                    currentTile.css('background-size', currentData.size);
                }
                if (currentData.position) {
                    currentTile.css('background-position', currentData.position);
                }

            }

        }

    }).on('newRow', function (event, direction, character) {

        var characterX = ~~character['x'];
        var characterY = ~~character['y'];
        var left = characterX - ~~(viewPortWidth/2);
        var top = characterY - ~~(viewPortHeight/2);
        var right = characterX + ~~(viewPortWidth/2);
        var bottom = characterY + ~~(viewPortHeight/2);
        $('.mapWrapper .map').each(function (index, element) {
            if ('north' === direction || 'south' === direction) {
                var topPosition = tileHeight * -1;
                var layerTop = top;
                if('south' === direction){
                    topPosition = viewPortHeight * tileHeight;
                    layerTop = bottom;
                }
                for (var i = 0, il = viewPortWidth; i < il; i++) {
                    var layerLeft = left+i;
                    var coodiantesClassName = 'Y'+layerTop+'X'+layerLeft;
                    var div = $('<div>').attr('class', 'tile noContent ' +coodiantesClassName).css({
                        'top': topPosition  + 'px',
                        'left': i * tileWidth + 'px',
                        'width': tileWidth + 'px',
                        'height': tileWidth + 'px'
                    });


                    $(this).prepend(div);
                }

            }

            if ('west' === direction || 'east' === direction) {
                var leftPosition = tileWidth * -1;
                var layerLeft = left;
                if('east' === direction){
                    leftPosition = viewPortWidth * tileWidth;
                    layerLeft = right;
                }
                for (i = 0, il = viewPortHeight; i < il; i++) {
                     layerTop = top+i;
                     coodiantesClassName = 'Y'+layerTop+'X'+layerLeft;
                     div = $('<div>').attr('class', 'tile noContent ' +coodiantesClassName).css({
                        'top': i*tileHeight  + 'px',
                        'left': leftPosition + 'px',
                        'width': tileWidth + 'px',
                        'height': tileWidth + 'px'
                    });


                    $(this).prepend(div);
                }

            }

        });

    });


    arrows.on('click', function () {
        var direction = $(this).attr('data-direction');


        $.ajax("/ajax/character/move/" + direction, {

                "dataType": "json",
                "success": function (data) {
                    if (data instanceof Array) {
                        return;
                    }
                    map.trigger('newRow', [direction, data.character]);
                    map.trigger('redraw', data.layers);

                }
            }
        );

    });


    for (var arrowIndex = 0, arrowIndexLength = arrows.length; arrowIndex < arrowIndexLength; arrowIndex++) {
        var currentArrow = $(arrows[arrowIndex]);
        currentArrow.attr('href', '#' + currentArrow.attr('data-direction'));

    }

});