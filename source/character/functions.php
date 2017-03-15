<?php
function isCharacterSelected()
{
    return isset($_SESSION['characterName']);
}

function selectCharacter($character = null)
{

    navigation(_('Select character'), '/selectCharacter');
    navigation(_('Logout'), '/logout');

    activateNavigation('/selectCharacter');

    $characters = getCharactersForUser(getCurrentUsername());

    $data = [
        'characters' => $characters,
        'activeCharacter' => $characters[0]
    ];
    echo render('selectCharacter', $data);
}

function newCharacter()
{

    navigation(_('Select character'), '/selectCharacter');
    navigation(_('Logout'), '/logout');

    activateNavigation('/selectCharacter');

    $characters = getCharactersForUser(getCurrentUsername());


    $characterName = '';
    $characterClass = '';


    if (isPost()) {
        $characterName = filter_input(INPUT_POST, 'characterName');
        $characterClass = filter_input(INPUT_POST, 'class');

    }
    $newCharacter = [
        'name' => $characterName,
        'class' => $characterClass
    ];
    $data = [
        'characters' => $characters,
        'newCharacter' => $newCharacter
    ];

    echo render('newCharacter', $data);
}

function getCharactersForUser($username)
{
    $db = getDb();
    $username = mysqli_real_escape_string($db, $username);
    $sql = "SELECT name FROM characters 
      INNER JOIN users ON(characters.userId = users.userId) 
      WHERE username = '" . $username . "'";

    $characters = [];

    $result = mysqli_query($db, $sql);
    if (!$result) {
        return $characters;
    }

    while ($row = $result->fetch_assoc()) {
        $characters[] = $row;
    }
    return $characters;
}