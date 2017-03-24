<?php layout('layouts/game'); ?>
<?php section('title') ?>
<?= _('Select a character') ?>
<?php section('title') ?>
<?php section('styles') ?>

<?php section('styles') ?>
<?php require_once __DIR__ . '/../partials/navigationSection.php'; ?>


<?php section('content') ?>
<?php if ($isDeletion): ?>
    <div class="modal fade in show" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <a href="/character/view/<?= $activeCharacter['name'] ?>" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></a>
                    <h4 class="modal-title"><?= _('Confirm') ?></h4>
                </div>
                <div class="modal-body">
                    <p><?= sprintf(_('You are going to delete <b>%s</b>, are you sure?'), $activeCharacter['name']) ?></p>
                </div>
                <div class="modal-footer">
                    <a href="/character/confirmDelete" class="btn btn-success">OK</a>
                    <a href="/character/view/<?= $activeCharacter['name'] ?>" class="btn btn-default">Cancel</a>

                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
    <div class="col-md-4">


        <div class="list-group">
            <a href="/character/new" class="list-group-item">New</a>
            <?php foreach ($characters as $character): ?>
                <a href="/character/view/<?= $character['name'] ?>"
                   class="list-group-item <?= $activeCharacter['name'] === $character['name'] ? 'active' : '' ?>  "><?= $character['name'] ?></a>
            <?php endforeach; ?>


        </div>
    </div>
    <div class="col-md-8">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3><?= $activeCharacter['name'] ?></h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-2">
                        <div class="equipment <?= $activeCharacter['class'] ?>">
                            <div class="body <?= $activeCharacter['gender'] ?> walk south big"></div>
                            <?php foreach ($equipmentSlots as $slotNumber => $slotName): ?>
                                <div class="<?= $slotName ?> <?= $activeCharacter['gender'] ?> <?= $activeCharacter['inventory'][$slotNumber]['itemName'] ?> walk south big"></div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="col-lg-10">

                    </div>
                </div>
            </div>
            <div class="panel-footer">
                <a href="/character/select/<?= $activeCharacter['name'] ?>" class="btn btn-success">Select</a>
                <a href="/character/delete/<?= $activeCharacter['name'] ?>" class="btn btn-default">Delete</a>
            </div>
        </div>

    </div>

<?php section('content') ?>

<?php section('scripts') ?>
    <script>


    </script>
<?php section('scripts') ?>

<?php layout('layouts/game'); ?>