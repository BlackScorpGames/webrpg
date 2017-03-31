<?php

/**
 * @param string $direction
 * @return null
 */
function viewMap($direction = 'south')
{
    if (!isLoggedIn()) {
        return event('http.403');
    }

    if (!isCharacterSelected()) {
        echo router('/character/view');
        return null;
    }
    navigation(_('Map'), '/');
    navigation(_('Select character'), '/character/view');
    navigation(_('Logout'), '/logout');

    activateNavigation('/');

    $activeCharacter = getSelectedCharacter();
    $activeCharacter['inventory'] = getEquipmentForCharacter($activeCharacter['name']);
    $viewPort = config('viewport');
    $tileSize = config('tileSize');


    list($layers,$mapData) = loadMap($activeCharacter['map'], $activeCharacter['x'], $activeCharacter['y'], $viewPort['width'], $viewPort['height'], $tileSize['width'], $tileSize['height']);
    unset($layers['collision']);
    $layers = addCharacterToMap($layers, $viewPort['width'], $viewPort['height'], $activeCharacter);

    $data = [
        'location' => $mapData['name'],
        'map' => $layers,
        'viewPort' => $viewPort,
        'tile' => $tileSize,
        'activeCharacter' => $activeCharacter,
        'viewDirection' => $direction,
        'equipmentSlots' => config('equipmentSlots')
    ];


    echo render('map', $data);

    return null;
}

/**
 * @param array $mapData
 * @param int $width
 * @param int $height
 * @param $activeCharacter
 * @return array
 */
function addCharacterToMap(array $mapData, $width, $height, $activeCharacter)
{
    if (!isset($mapData['character'])) {
        trigger_error('Missing layer "character"');

        return $mapData;
    }
    $y = ~~($height / 2);
    $x = ~~($width / 2);
    $index = $width * $y + $x;
    $mapData['character'][$index]['partial'] = 'displayCharacter';
    $mapData['character'][$index]['tileSetName'] = 'character ' . $activeCharacter['name'];
    return $mapData;
}

/**
 * @param string $name
 * @param int $centerX
 * @param int $centerY
 * @param int $viewPortWidth
 * @param int $viewPortHeight
 * @param int $tileWidth
 * @param int $tileHeight
 * @return array|null
 */
function loadMap($name, $centerX, $centerY, $viewPortWidth, $viewPortHeight, $tileWidth, $tileHeight)
{
    $pathToMapFile = realpath(ROOT_DIR . '/gamedata/maps/' . $name . '.json');
    if (!$pathToMapFile) {
        trigger_error(_('File for map not exists'));

        return null;
    }
    $mapContent = file_get_contents($pathToMapFile);
    if (!$mapContent) {
        trigger_error(_('File content is empty'));

        return null;
    }
    $mapData = json_decode($mapContent, true);
    if (json_last_error()) {
        trigger_error(json_last_error_msg());

        return null;
    }


    $tiles = [];
    $originalLayers = $mapData['layers'];

    foreach ($mapData['tilesets'] as $tileSet) {
        $firstId = $tileSet['firstgid'];
        $ratioHeight = ~~($tileHeight / $tileSet['tileheight']);
        $ratioWidth = ~~($tileWidth / $tileSet['tilewidth']);
        $tileSetImageWidth = ($tileSet['imagewidth'] * $ratioWidth);
        $tileSetImageHeight = ($tileSet['imageheight'] * $ratioHeight);
        $tileSetTileImageHeight = ($tileSet['tileheight'] * $ratioHeight);
        $tileSetTileImageWidth = ($tileSet['tilewidth'] * $ratioWidth);

        for ($tileSetHeight = 0; $tileSetHeight < $tileSetImageHeight; $tileSetHeight += $tileSetTileImageHeight) {
            for ($tileSetWidth = 0; $tileSetWidth < $tileSetImageWidth; $tileSetWidth += $tileSetTileImageWidth) {
                $tiles[$firstId] = [
                    'tileSetName' => $tileSet['name'],
                    'size' => sprintf('%dpx %dpx', $tileSetImageWidth, $tileSetImageHeight),
                    'position' => sprintf('-%dpx -%dpx', $tileSetWidth, $tileSetHeight),
                    'width' => (int)$tileSet['tilewidth'],
                    'height' => (int)$tileSet['tileheight']
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
            $layers[$layer['name']] =$layer;
            continue;
        }
        $data = [];
        $width = $layer['width'];
        $height = $layer['height'];
        $originalData = $layer['data'];
        for ($y = $startY; $y < $endY; $y++) {
            for ($x = $startX; $x < $endX; $x++) {
                $dataKey = $width * $y + $x;

                $value = null;
                if (isset($originalData[$dataKey]) && isset($tiles[$originalData[$dataKey]])) {
                    $value = $tiles[$originalData[$dataKey]];
                }
                if ($x < 0 || $y < 0 || $x >= $width || $y >= $height) {
                    $value = [
                        'tileSetName' => 'empty'
                    ];
                }

                $data[] = $value;
            }
        }
        $layers[$layer['name']] = $data;
    }
    $data = [
        'baseTile' =>  $tiles[1],
        'name' => isset($mapData['properties']['name'])?$mapData['properties']['name']:_('Unnamed')
    ];
    return [$layers,$data];
}
