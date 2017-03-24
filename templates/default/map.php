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

            <?php foreach ($map as $name => $mapData): ?>
                <div class="map">
                    <?php for ($y = 0; $y < $viewPort['height']; $y++): ?>
                        <?php for ($x = 0; $x < $viewPort['width']; $x++): ?>
                        <div style="
                                height:<?= $tile['height']?>px;
                                width:<?= $tile['width']?>px;
                                left:<?= $x*$tile['width']?>px;
                                top:<?= $y*$tile['height']?>px" class="tile"></div>
                        <?php endfor; ?>
                    <?php endfor; ?>
                </div>
            <?php endforeach; ?>

        </div>
    </div>
<?php section('content') ?>
<?php section('scripts') ?>
    <script>


    </script>
<?php section('scripts') ?>

<?php layout('layouts/game'); ?>