<?php

function viewMap($direction = 'south')
{
    if (!isLoggedIn()) {
        return event('http.403');
    }

    if (!isCharacterSelected()) {
        echo router('/character/view');
        return;
    }
    navigation(_('Map'), '/');
    navigation(_('Select character'), '/character/view');
    navigation(_('Logout'), '/logout');

    activateNavigation('/');

    $activeCharacter = getSelectedCharacter();
    $activeCharacter['inventory'] = getEquipmentForCharacter($activeCharacter['name']);
    $viewPort = config('viewport');
    $tileSize = config('tileSize');

    $mapData = loadMap($activeCharacter['map'], $activeCharacter['x'], $activeCharacter['y'], $viewPort['width'], $viewPort['height'], $tileSize['width'], $tileSize['height']);

    $mapData = addCharacterToMap($mapData,$viewPort['width'], $viewPort['height']);

    $data = [
        'location' => 'test city',
        'map' => $mapData,
        'viewPort' => $viewPort,
        'tile' => $tileSize,
        'activeCharacter' => $activeCharacter,
        'viewDirection' => $direction,
        'equipmentSlots' => config('equipmentSlots')
    ];

    echo render('map', $data);
}

function addCharacterToMap(array $mapData,$width,$height)
{
    if(!isset($mapData['character'])){
        trigger_error('Missing layer "character"');
        return $mapData;
    }
    $y = ~~($height/2);
    $x = ~~($width/2);
    $index = $width * $y + $x;
    $mapData['character'][$index]['partial'] = 'displayCharacter';

    return $mapData;
}

function loadMap($name, $centerX, $centerY, $viewPortWidth, $viewPortHeight, $tileWidth, $tileHeight)
{
    $pathToMapFile = realpath(ROOT_DIR . '/gamedata/maps/' . $name . '.json');
    if (!$pathToMapFile) {
        trigger_error(_("File for map not exists"));
        return;
    }
    $mapContent = file_get_contents($pathToMapFile);
    if (!$mapContent) {
        trigger_error(_("File content is empty"));
        return;
    }
    $mapData = json_decode($mapContent, true);
    if (json_last_error()) {
        trigger_error(json_last_error_msg());
        return;
    }
    $originalLayers = $mapData['layers'];
    $tilesets = $mapData['tilesets'];
    $tiles = [];
    foreach ($tilesets as $tileset) {
        $firstId = $tileset['firstgid'];
        $ratioHeight = ~~($tileHeight / $tileset['tileheight']);
        $ratioWidth = ~~($tileWidth / $tileset['tilewidth']);
        $tileSetImageWidth = ($tileset['imagewidth'] * $ratioWidth);
        $tileSetImageHeight = ($tileset['imageheight'] * $ratioHeight);
        $tileSetTileImageHeight = ($tileset['tileheight'] * $ratioHeight);
        $tileSetTileImageWidth = ($tileset['tilewidth'] * $ratioWidth);

        for ($tileSetHeight = 0; $tileSetHeight < $tileSetImageHeight; $tileSetHeight += $tileSetTileImageHeight) {
            for ($tileSetWidth = 0; $tileSetWidth < $tileSetImageWidth; $tileSetWidth += $tileSetTileImageWidth) {
                $tiles[$firstId] = [
                    'tileSetName' => $tileset['name'],
                    'size' => sprintf('%dpx %dpx', $tileSetImageWidth, $tileSetImageHeight),
                    'position' => sprintf('-%dpx -%dpx', $tileSetWidth, $tileSetHeight)
                ];

                $firstId++;
            }
        }

    }

    $layers = [];


    $halfViewPortWidth = ~~($viewPortWidth / 2);
    $halfViewportHeight = ~~($viewPortHeight / 2);
    $startX = $centerX - $halfViewPortWidth;
    $startY = $centerY - $halfViewportHeight;
    $endX = $startX + $viewPortWidth;
    $endY = $startY + $viewPortHeight;

    foreach ($originalLayers as $layer) {
        if ($layer['name'] !== 'collision' && $layer['visible'] === false) {
            continue;
        }
        if ($layer['type'] !== 'tilelayer') {
            continue;
        }

        $data = [];
        $originalData = $layer['data'];

        $width = $layer['width'];
        $height= $layer['height'];
        for ($y = $startY; $y < $endY; $y++) {
            for ($x = $startX; $x < $endX; $x++) {
                $dataKey = $width * $y + $x;

                $value = null;
                if (isset($originalData[$dataKey]) && isset($tiles[$originalData[$dataKey]])) {
                    $value = $tiles[$originalData[$dataKey]];
                }
                if($x <= 0 || $y <= 0 || $x >= $width || $y >= $height){
                    $value = [
                        'tileSetName' =>'empty'
                    ];
                }

                $data[] = $value;
            }
        }


        $layers[$layer['name']] = $data;


    }


    return $layers;


}