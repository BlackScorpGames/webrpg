$(function () {
    var arrows = $('.arrows > div > a');

    var map = $('.mapWrapper');

    map.on('redraw', function (event, direction, data) {

        for (var layerName in data.layers) {
            var layerData = data.layers[layerName];
            if (undefined === layerData) {
                continue;
            }

            for (var tileIndex in layerData) {

                var currentData = layerData[tileIndex];

                if (undefined === currentData) {
                    continue;
                }

                var coordinateClass = 'Y' + currentData['coordinates']['y'] + 'X' + currentData['coordinates']['x'];
                var currentTile = $('.mapWrapper').find('.map.' + layerName + ' > .noContent.' + coordinateClass);

                if (0 === currentTile.length) {
                    continue;
                }
                currentTile.removeClass('noContent');
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
        var animationModifier = {
            north: {
                left: '+=0',
                top: '+=' + tileHeight + 'px'
            },
            south: {
                left: '+=0',
                top: '-=' + tileHeight + 'px'
            },
            east: {
                left: '-=' + tileWidth + 'px',
                top: '+=0'
            },
            west: {
                left: '+=' + tileWidth + 'px',
                top: '+=0'
            }
        };
        var currentMapAnimation = animationModifier[direction];

        if (undefined !== currentMapAnimation) {
            var characterDiv = $('.tile.character .equipment');
            var character = data.character;

            var characterX = ~~character['x'];
            var characterY = ~~character['y'];
            var coordinatesClassName = '.map.character .tile.Y' + characterY + 'X' + characterX;
            characterDiv.css({position: 'fixed'});
            map.animate(currentMapAnimation, 300, function () {
                characterDiv.parent('.tile').removeClass('character');
                characterDiv.detach().appendTo(coordinatesClassName);
                characterDiv.css({position: 'relative'}).parent('.tile').addClass('character');
                map.trigger('deleteRow', [direction, data]);
            });
        }

    })
        .on('newRow', function (event, direction, data) {
        var character = data.character;

        var characterX = ~~character['x'];
        var characterY = ~~character['y'];
        var left = characterX - ~~(viewPortWidth / 2);
        var top = characterY - ~~(viewPortHeight / 2);
        var right = characterX + ~~(viewPortWidth / 2);
        var bottom = characterY + ~~(viewPortHeight / 2);
            console.log(left,top,right,bottom,characterX,characterY);
        return;


        $('.mapWrapper .map').each(function () {
            if ('north' === direction || 'south' === direction) {
                var topPosition = (tileHeight) * -1;
                var layerTop = top;

                if ('south' === direction) {
                    topPosition = (viewPortHeight) * (tileHeight);
                    layerTop = bottom;
                }


                for (var i = 0, il = viewPortWidth; i < il; i++) {
                    var layerLeft = left + i;
                    var coordinatesClassName = 'Y' + layerTop + 'X' + layerLeft;
                    var div = $('<div>').attr('class', 'tile noContent ' + coordinatesClassName).css({
                        'top': topPosition + 'px',
                        'left': i * tileWidth + 'px',
                        'width': tileWidth + 'px',
                        'height': tileWidth + 'px'
                    });


                    $(this).append(div);
                }

            }

            if ('west' === direction || 'east' === direction) {
                var leftPosition = (tileWidth) * -1;
                layerLeft = left;
                if ('east' === direction) {
                    leftPosition = viewPortWidth * (tileWidth);
                    layerLeft = right;
                }
                for (i = 0, il = viewPortHeight; i < il; i++) {
                    layerTop = top + i;
                    coordinatesClassName = 'Y' + layerTop + 'X' + layerLeft;
                    div = $('<div>').attr('class', 'tile noContent ' + coordinatesClassName).css({
                        'top': i * tileHeight + 'px',
                        'left': leftPosition + 'px',
                        'width': tileWidth + 'px',
                        'height': tileWidth + 'px'
                    });


                    $(this).append(div);
                }

            }

        });

      //  map.trigger('redraw', [direction, data]);
    }).on('deleteRow', function (event, direction, data) {
        var character = data.character;

        var characterX = ~~character['x'];
        var characterY = ~~character['y'];
        var left = characterX - ~~(viewPortWidth / 2 );
        var top = characterY - ~~(viewPortHeight / 2 );
        var right = characterX + ~~(viewPortWidth / 2 );
        var bottom = characterY + ~~(viewPortHeight / 2 );


        $('.mapWrapper .map').each(function () {
            if ('north' === direction || 'south' === direction) {
                var layerTop = bottom+1;
                if ('south' === direction) {
                    layerTop = top-1;
                }
                for (var i = 0, il = viewPortWidth; i < il; i++) {
                    var layerLeft = left + i;
                    var coordinatesClassName = 'Y' + layerTop + 'X' + layerLeft;
                    $(this).find('.tile.' + coordinatesClassName).remove();

                }

            }

            if ('west' === direction || 'east' === direction) {
                layerLeft = right+1;
                if ('east' === direction) {
                    layerLeft = left-1;
                }
                for (i = 0, il = viewPortHeight; i < il; i++) {
                    layerTop = top + i;

                    coordinatesClassName = 'Y' + layerTop + 'X' + layerLeft;
                    $(this).find('.tile.' + coordinatesClassName).remove();

                }

            }

        });


    });


    arrows.on('click', function () {
        var direction = $(this).attr('data-direction');


        $.ajax("ajax/character/move/" + direction, {

                "dataType": "json",
                "success": function (data) {
                    if (data instanceof Array) {
                        return;
                    }
                    map.trigger('newRow', [direction, data]);


                }
            }
        );

    });


    for (var arrowIndex = 0, arrowIndexLength = arrows.length; arrowIndex < arrowIndexLength; arrowIndex++) {
        var currentArrow = $(arrows[arrowIndex]);
        currentArrow.attr('href', '#' + currentArrow.attr('data-direction'));

    }

});