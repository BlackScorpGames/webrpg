<?php layout('layouts/default'); ?>
<?php section('title') ?>
<?= _('Login') ?>
<?php section('title') ?>
<?php section('styles') ?>

<?php section('styles') ?>

<?php require_once __DIR__.'/../partials/navigationSection.php'; ?>
<?php section('content') ?>

    <div class="col-lg-4">
        <form method="POST" action="/login" class="form-group">
            <div class="panel panel-default">
                <div class="panel-heading">Login</div>
                <div class="panel-body">
                    <?php if (count($errors) > 0): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php foreach ($errors as $message): ?>
                                <?= $message ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="username"><?= _('Username') ?></label>
                        <input class="form-control" id="username" type="text" name="username"
                               placeholder="<?= _('Username') ?>"
                               value="<?= $username ?>">
                    </div>
                    <div class="form-group">
                        <label for="password"><?= _('Password') ?></label>
                        <input class="form-control" id="password" type="password" name="password"
                               placeholder="<?= _('Password') ?>"
                               value="<?= $password ?>">
                    </div>


                </div>
                <div class="panel-footer">
                    <button name="login" class="btn btn-default"><?= _('Login') ?></button>
                </div>
            </div>

        </form>

    </div>
    <div class="col-lg-8">
        <div class="panel panel-default">
            <div class="panel-body">
                <h2>WebRPG</h2>
                <p><?= _('very long text') ?></p>
            </div>
        </div>
    </div>


<?php section('content') ?>

<?php section('scripts') ?>
    <script>


    </script>
<?php section('scripts') ?>

<?php layout('layouts/default'); ?>
