<?php


router('/character/select(/\S+)?','selectCharacter');
router('/character/view/(\S+)','selectCharacter');

router('/character/new','newCharacter');
router('/character/confirmDelete','deleteCharacter');
router('/character/delete/(\S+)','askToDeleteCharacter');
