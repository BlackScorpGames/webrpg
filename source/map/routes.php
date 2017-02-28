<?php

router('/map', function () {
    if (!isLoggedIn()) {
        return event('http.403');
    }

    return 'This is a map';
});