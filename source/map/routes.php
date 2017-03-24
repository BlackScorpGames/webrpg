<?php

router('/map', function () {
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
    $data = [];
    echo render('map', $data);
});