$(function () {
    var arrows = $('.arrows > div > a');

    var map = $('.mapWrapper');

    map.on('redraw', function (event, data) {
        for (var layerName in data) {
            var layerData = data[layerName];
            var layerTiles = $(this).find('.' + layerName + '> div');
            for (var tileIndex in layerData) {
                var currentTile = layerTiles[tileIndex];
                var newData = layerData[tileIndex];
                if (currentTile === undefined) {
                    continue;
                }
                currentTile = $(currentTile);
                if (newData === null) {
                    currentTile.css({
                        'background-position': '',
                        'background-size': ''
                    }).attr('class', 'tile');
                    continue;
                }
                if (newData.tileSetName) {
                    currentTile.css({
                        'background-position': '',
                        'background-size': ''
                    }).attr('class', 'tile ' + newData.tileSetName);

                }
                if (newData.size) {
                    currentTile.css('background-size', newData.size);
                }
                if (newData.position) {
                    currentTile.css('background-position', newData.position);
                }

            }

        }

    });


    arrows.on('click', function () {
        var direction = $(this).attr('data-direction');

        var layers = $('.mapWrapper .map');


        $.ajax("/ajax/character/move/" + direction, {

                "dataType": "json",
                "success": function (data) {
                    if (data instanceof Array) {

                        return;
                    }
                    map.trigger('redraw', data);

                }
            }
        );

    });


    for (var arrowIndex = 0, arrowIndexLength = arrows.length; arrowIndex < arrowIndexLength; arrowIndex++) {
        var currentArrow = $(arrows[arrowIndex]);
        currentArrow.attr('href', '#' + currentArrow.attr('data-direction'));

    }

});