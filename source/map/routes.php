<?php

router('/map', function () {
    if (!isLoggedIn()) {
        return event('http.403');
    }

    if (!isCharacterSelected()) {
        echo router('/character/view');
        return;
    }

    echo 'This is a map';
});