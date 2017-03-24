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
    $mapData = loadMap('city', 12, 12, 7, 7);

    $data = [
        'location' => 'Test city',
        'map' => $mapData,
        'viewPort' =>[
            'width' => 7,
            'height' => 7
        ],
        'tile'=>[
            'width' => 64,
            'height' => 64
        ]
    ];

    echo render('map', $data);
}

function loadMap($name, $centerX, $centerY, $viewPortWidth, $viewPortHeight)
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
    $tielsets = $mapData['tilesets'];

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
                if (isset($originalData[$dataKey])) {
                    $data[] = $originalData[$dataKey];
                }
            }
        }

        $viewPort = [
            'data' => $data,
        ];
        $layers[$layer['name']] = $viewPort;


    }

    return $layers;


}