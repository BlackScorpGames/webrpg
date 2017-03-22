<?php

router('/selectCharacter','selectCharacter');
router('/newCharacter','newCharacter');
router('/confirmDelete','deleteCharacter');
router('/delete/(\S+)','askToDeleteCharacter');
router('/select/(\S+)','selectCharacter');
router('/view/(\S+)','selectCharacter');