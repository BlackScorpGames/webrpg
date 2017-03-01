<?php
function isCharacterSelected()
{
    return isset($_SESSION['characterName']);
}

function selectCharacter()
{

    $data = [];
    return render('selectCharacter', $data);
}