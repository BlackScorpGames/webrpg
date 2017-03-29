<?php

function viewMap()
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
    $activeCharacter['left'] = '0';
    $activeCharacter['top'] = '0';
    $viewPort = config('viewport');
    $tileSize = config('tileSize');

    $mapData = loadMap($activeCharacter['map'], $activeCharacter['x'], $activeCharacter['y'], $viewPort['width'], $viewPort['height'], $tileSize['width'], $tileSize['height']);

    $data = [
        'location' => 'test city',
        'map' => $mapData,
        'viewPort' => $viewPort,
        'tile' => $tileSize,
        'activeCharacter' => $activeCharacter,
        'equipmentSlots' => config('equipmentSlots')
    ];

    echo render('map', $data);
}

function loadMap($name, $centerX, $centerY, $viewPortWidth, $viewPortHeight, $tileWidth, $tileHeight)
{
    $pathToMapFile = realpath(ROOT_DOR . '/gamedata/maps/' . $name . '.json');
    if (!$pathToMapFile) {
        trigger_error(_("File for map not exists"), E_USER_ERROR);
        return;
    }
    $mapContent = file_get_contents($pathToMapFile);
    if (!$mapContent) {
        trigger_error(_("File content is empty"), E_USER_ERROR);
        return;
    }
    $mapData = json_decode($mapContent, true);
    if (json_last_error()) {
        trigger_error(json_last_error_msg(), E_USER_ERROR);
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
        if ($layer['visible'] === false) {
            continue;
        }
        if ($layer['type'] !== 'tilelayer') {
            continue;
        }

        $data = [];
        $originalData = $layer['data'];

        $width = $layer['width'];
        for ($y = $startY; $y < $endY; $y++) {
            for ($x = $startX; $x < $endX; $x++) {
                $dataKey = $width * $y + $x;
                $value = null;
                if (isset($originalData[$dataKey]) && isset($tiles[$originalData[$dataKey]])) {
                    $value = $tiles[$originalData[$dataKey]];
                }
                $data[] = $value;
            }
        }

        $viewPort = [
            'data' => $data,
        ];

        $layers[$layer['name']] = $viewPort;


    }

    return $layers;


}