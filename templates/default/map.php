<?php layout('layouts/game'); ?>
<?php section('title') ?>
<?= _('Map') ?>
<?php section('title') ?>
<?php section('styles') ?>

<?php section('styles') ?>
<?php require_once __DIR__ . '/../partials/navigationSection.php'; ?>
<?php section('content') ?>
    <div class="col-lg-4 col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading">
                <?= _('Actions') ?>
            </div>
            <div class="panel-body">

            </div>
        </div>

    </div>
    <div class="col-lg-8 col-md-8">
        <div class="panel panel-default">
            <div class="panel-heading">
                <?= $location ?>
            </div>
            <div class="panel-body">

                <div class="viewport">
                    <div class="arrows">
                        <div class="north"><a href="/character/move/north" class="glyphicon glyphicon-chevron-up"></a>
                        </div>
                        <div class="east"><a href="/character/move/east" class="glyphicon glyphicon-chevron-right"></a>
                        </div>
                        <div class="south"><a href="/character/move/south" class="glyphicon glyphicon-chevron-down"></a>
                        </div>
                        <div class="west"><a href="/character/move/west" class="glyphicon glyphicon-chevron-left"></a>
                        </div>
                    </div>
                    <div class="mapWrapper"
                         style="margin-left:-<?= ~~(($tile['width'] * $viewPort['width']) / 2) ?>px;width:<?= $tile['width'] * $viewPort['width'] ?>px;height: <?= $tile['height'] * $viewPort['height'] ?>px ">
                        <?php foreach ($map as $name => $mapData): ?>
                            <div class="map <?= $name ?>">
                                <?php for ($y = 0; $y < $viewPort['height']; $y++): ?>
                                    <?php for ($x = 0; $x < $viewPort['width']; $x++):
                                        $dataKey = $viewPort['width'] * $y + $x;
                                        $data = isset($mapData[$dataKey]) ? $mapData[$dataKey] : null;
                                        ?>

                                        <div style="height:<?= $tile['height'] ?>px;width:<?= $tile['width'] ?>px;left:<?= $x * $tile['width'] ?>px;top:<?= $y * $tile['height'] ?>px<?= ($data && isset($data['position'])) ? ';background-position:' . $data['position'] : '' ?><?= ($data && isset($data['size'])) ? ';background-size:' . $data['size'] : '' ?>"
                                             class="tile<?= ($data && isset($data['tileSetName'])) ? ' ' . $data['tileSetName'] : '' ?>">
                                            <?php if($data && isset($data['partial'])) require_once __DIR__.'/../partials/'.$data['partial'].'.php';?>

                                        </div>
                                    <?php endfor; ?>
                                <?php endfor; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>

        </div>
    </div>
<?php section('content') ?>
<?php section('scripts') ?>
    <script>


    </script>
<?php section('scripts') ?>

<?php layout('layouts/game'); ?>