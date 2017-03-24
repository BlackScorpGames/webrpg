<?php layout('layouts/game'); ?>
<?php section('title') ?>
<?= _('Map') ?>
<?php section('title') ?>
<?php section('styles') ?>

<?php section('styles') ?>
<?php require_once __DIR__ . '/../partials/navigationSection.php'; ?>
<?php section('content') ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <?= $location ?>
        </div>
        <div class="panel-body">

        </div>
    </div>
<?php section('content') ?>
<?php section('scripts') ?>
    <script>


    </script>
<?php section('scripts') ?>

<?php layout('layouts/game'); ?>