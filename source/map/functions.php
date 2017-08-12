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
    navigation(_('Map'), '');
    navigation(_('Select character'), 'character/view');
    navigation(_('Logout'), 'logout');

    activateNavigation('');

    $activeCharacter = getSelectedCharacter();

    $viewPort = config('viewport');
    $tileSize = config('tileSize');


    list($layers, $mapData) = loadMap($activeCharacter['map'], $activeCharacter['x'], $activeCharacter['y'], $viewPort['width'], $viewPort['height'], $tileSize['width'], $tileSize['height']);
    unset($layers['collision']);
    unset($layers['events']);
    $halfViewPortWidth = ~~($viewPort['width'] / 2);
    $halfViewPortHeight = ~~($viewPort['height'] / 2);
    $viewPort['left'] = $activeCharacter['x'] - $halfViewPortWidth;
    $viewPort['top'] = $activeCharacter['y'] - $halfViewPortHeight;
    $viewPort['right'] = $viewPort['left'] + $viewPort['width'];
    $viewPort['bottom'] = $viewPort['top'] + $viewPort['height'];

    $characters = getCharactersForArea($viewPort['left'], $viewPort['right'], $viewPort['top'], $viewPort['bottom']);

    foreach ($characters as $character) {
        $character['inventory'] = getEquipmentForCharacter($character['name']);
        $layers = addCharacterToMap($layers, $mapData, $character);
    }


    $data = [
        'map' => $mapData,
        'location' => $mapData['name'],
        'layers' => $layers,
        'viewPort' => $viewPort,
        'tile' => $tileSize,
        'activeCharacter' => $activeCharacter,
        'equipmentSlots' => config('equipmentSlots')
    ];

    if (!isAjax()) {
        echo render('map', $data);
        return null;
    }
    header('Content-Type:application/json;charset=utf-8');
    echo json_encode($data);
    return null;
}

/**
 * @param array $layers
 * @param array $mapData
 * @param array $character
 * @return array
 */
function addCharacterToMap(array $layers, array $mapData, array $character)
{
    if (!isset($layers['character'])) {
        trigger_error('Missing layer "character"');

        return $layers;
    }
    $y = $character['y'];
    $x = $character['x'];
    $index = $mapData['width'] * $y + $x;


    $layers['character'][$index]['partial'] = 'displayCharacter';
    $layers['character'][$index]['tileSetName'] = 'character';
    $layers['character'][$index]['characters'][] = $character;
    $layers['character'][$index]['coordinates'] = [
        'y' => $y,
        'x' => $x
    ];


    return $layers;
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
    static $mapData = null;
    static $mapName = null;
    if (!$mapName) {
        $mapName = $name;
    }
    if ($mapName !== $name) {
        $mapData = null;
    }
    if (!$mapData) {

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

    $width = 0;
    $height = 0;
    foreach ($originalLayers as $layer) {
        if ($layer['name'] !== 'collision' && $layer['visible'] === false) {
            continue;
        }

        if ($layer['type'] !== 'tilelayer') {
            $layers[$layer['name']] = $layer;
            continue;
        }
        $data = [];
        $width = (int)$layer['width'];
        $height = (int)$layer['height'];
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

                $value['coordinates'] = [
                    'x' => $x,
                    'y' => $y
                ];


                $data[$dataKey] = $value;
            }
        }
        $layers[$layer['name']] = $data;
    }

    $data = [
        'baseTile' => $tiles[1],
        'width' => $width,
        'height' => $height,
        'name' => isset($mapData['properties']['name']) ? $mapData['properties']['name'] : _('Unnamed')
    ];

    return [$layers, $data];
}
