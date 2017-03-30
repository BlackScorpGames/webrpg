<?php

router('/character/select/(\S+)','selectCharacter');
router('/character/view/(\S+)?','viewCharacter');
router('/character/view','viewCharacter');
router('/character/new','newCharacter');
router('/character/confirmDelete','deleteCharacter');
router('/character/delete/(\S+)','askToDeleteCharacter');
router('/character/move/(north|south|east|west)','moveCharacter');
