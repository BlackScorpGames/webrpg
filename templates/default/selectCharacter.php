<?php layout('layouts/game'); ?>
<?php section('title') ?>
<?= _('Select a character') ?>
<?php section('title') ?>
<?php section('styles') ?>

<?php section('styles') ?>
<?php require_once __DIR__ . '/../partials/navigationSection.php'; ?>


<?php section('content') ?>


    <div class="col-md-4">


        <div class="list-group">
            <a href="/new" class="list-group-item active">New</a>
            <?php foreach ($characters as $character): ?>
                <a href="/view/<?= $character['name'] ?>" class="list-group-item"><?= $character['name'] ?></a>
            <?php endforeach; ?>


        </div>
    </div>
    <div class="col-md-8">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3><input type="text"></h3>
            </div>
            <div class="panel-body">

            </div>
            <div class="panel-footer">
                <a href="/select/" class="btn btn-success">Select</a>
                <a href="/delete/" class="btn btn-default">Delete</a>
            </div>
        </div>

    </div>

<?php section('content') ?>

<?php section('scripts') ?>
    <script>


    </script>
<?php section('scripts') ?>

<?php layout('layouts/game'); ?>