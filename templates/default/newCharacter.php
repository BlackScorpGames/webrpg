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
            <a href="/newCharacter" class="list-group-item active">New</a>
            <?php foreach ($characters as $character): ?>
                <a href="/view/<?= $character['name'] ?>" class="list-group-item"><?= $character['name'] ?></a>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="col-md-8">
        <?php if (count($errors) > 0): ?>
            <div class="alert alert-danger" role="alert">
                <?php foreach ($errors as $message): ?>
                    <?= $message ?><br/>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <form action="/newCharacter" method="POST">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <label for="characterName"><?= _('Charactername') ?></label>
                    <input type="text" class="form-control" id="characterName" name="characterName" placeholder="<?= _('Charactername') ?>"
                           value="<?= escape($newCharacter['name']) ?>">
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-12 text-center">
                            <h5><strong>Select gender</strong></h5>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-xs-6 col-md-6 col-lg-6 col-sm-6">
                            <label class="thumbnail text-center">
                                <input<?= $newCharacter['gender'] === 'male'?  ' checked="checked"':""?> type="radio" name="gender" value="male"> <?= _('Male') ?> ♂
                            </label>
                        </div>
                        <div class="col-xs-6 col-md-6 col-lg-6 col-sm-6">
                            <label class="thumbnail text-center">
                                <input<?= $newCharacter['gender'] === 'female'? ' checked="checked"':""?> type="radio" name="gender" value="female"> <?= _('Female') ?> ♀
                            </label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12 text-center">
                            <h5><strong>Select a class</strong></h5>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-xs-4 col-md-4 col-lg-4 col-sm-4">
                            <label class="thumbnail text-center">
                                <input<?= $newCharacter['class'] === 'warrior'?  ' checked="checked"':""?> type="radio" name="class" value="warrior"> <?= _('Warrior') ?>
                                <img src="assets/img/classes/warrior.svg" alt="<?= _('Warrior') ?>" class="img-responsive">
                            </label>
                        </div>
                        <div class="col-xs-4 col-md-4 col-lg-4 col-sm-4">
                            <label class="thumbnail text-center">
                                <input<?= $newCharacter['class'] === 'ranger'? ' checked="checked"':""?> type="radio" name="class" value="ranger"> <?= _('Ranger') ?>
                                <img src="assets/img/classes/ranger.svg" alt="<?= _('Ranger') ?>" class="img-responsive">
                            </label>
                        </div>
                        <div class="col-xs-4 col-md-4 col-lg-4 col-sm-4">
                            <label class="thumbnail text-center">
                                <input<?= $newCharacter['class'] === 'mage'? ' checked="checked"':""?> type="radio" name="class" value="mage"> <?= _('Mage') ?>
                                <img src="assets/img/classes/mage.svg" alt="<?= _('Mage') ?>" class="img-responsive">
                            </label>
                        </div>
                    </div>

                </div>
                <div class="panel-footer">
                    <button type="submit" class="btn btn-success">Create</button>
                </div>
            </div>
        </form>
    </div>
<?php section('content') ?>

<?php section('scripts') ?>
    <script>


    </script>
<?php section('scripts') ?>

<?php layout('layouts/game'); ?>