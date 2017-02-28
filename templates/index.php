<?php layout('layouts/default'); ?>

<?php section('styles') ?>

<?php section('styles') ?>


<?php section('content') ?>
    Original content
<?php section('content') ?>


<?php section('content') ?>
    Overwritten content
<?php section('content') ?>

<?php sectionAppend('content') ?>
    <b>Appended</b>
<?= escape($variable) ?>
<?php sectionAppend('content') ?>

<?php section('scripts') ?>
    <script>
        var foo = 'test';

    </script>
<?php section('scripts') ?>

<?php layout('layouts/default'); ?>