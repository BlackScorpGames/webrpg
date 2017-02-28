<!DOCTYPE html>
<html lang="en">
<head>
    <title><?= $title ?></title>
    <meta charset="UTF-8"/>
    <base href="/">
    <link rel="stylesheet" type="text/css" media="screen" href="/assets/css/unsemantic.css">
    <link rel="stylesheet" type="text/css" media="screen" href="/assets/css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1 " />
    <?= $styles ?>
</head>
<body>
<header class="hide-on-tablet hide-on-mobile">
    <h1>WebRPG</h1>
</header>

<div class="grid-container main grid-parent">
        <?= $content ?>
</div>


<?= $scripts ?>
</body>
</html>