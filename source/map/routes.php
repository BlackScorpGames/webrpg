<?php

router('/map', function () {
    if (!isLoggedIn()) {
        return event('http.403');
    }

    if(!isCharacterSelected()){
        echo router('/character/select');
        return;
    }
    return 'This is a map';
});