<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= $title ?></title>
    <meta charset="UTF-8"/>
    <base href="/">
    <link rel="stylesheet" type="text/css" media="screen" href="/assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" media="screen" href="/assets/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= $styles ?>
</head>
<body>

<nav class="navbar navbar-default">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#mainNavi"
                    aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

        </div>
        <div class="collapse navbar-collapse" id="mainNavi">
            <?= $navigation ?>
        </div>
    </div>
</nav>

<div class="container">

    <?= $content ?>

</div>
<script src="assets/js/vendor/jquery.min.js"></script>
<script src="assets/js/vendor/bootstrap.min.js"></script>
<?= $scripts ?>
</body>
</html>