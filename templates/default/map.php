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
        <div class="panel-body" style="position: relative">
            <div style="position: relative">
                <?php foreach ($map as $name => $mapData): ?>
                    <div class="map <?= $name ?>">
                        <?php for ($y = 0; $y < $viewPort['height']; $y++): ?>
                            <?php for ($x = 0; $x < $viewPort['width']; $x++):
                                $dataKey = $viewPort['width'] * $y + $x;
                                $data = isset($mapData['data'][$dataKey]) ? $mapData['data'][$dataKey] : null;
                                ?>

                                <div style="height:<?= $tile['height'] ?>px;width:<?= $tile['width'] ?>px;left:<?= $x * $tile['width'] ?>px;top:<?= $y * $tile['height'] ?>px<?= ($data && isset($data['position'])) ? ';background-position:' . $data['position'] : '' ?>"
                                     class="tile<?= ($data && isset($data['tileSetName'])) ? ' ' . $data['tileSetName'] : '' ?>"></div>
                            <?php endfor; ?>
                        <?php endfor; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>

<?php section('content') ?>
<?php section('scripts') ?>
    <script>


    </script>
<?php section('scripts') ?>

<?php layout('layouts/game'); ?>