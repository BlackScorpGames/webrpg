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
    loadMap('city');
    $data = [
        'location' => 'Test city'
    ];

    echo render('map', $data);
}

function loadMap($name)
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
    $mapData = json_decode($mapContent,true);
    if(json_last_error()){
        trigger_error(json_last_error_msg(), E_USER_ERROR);
        return;
    }
    $layers = $mapData['layers'];
    $tielsets = $mapData['tilesets'];


}