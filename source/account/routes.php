<?php

router('/', 'login');
router('/login', 'login');

router('/register', 'registration');

router('/logout', function () {
    session_regenerate_id(true);
    session_destroy();
    redirect('/');
});
