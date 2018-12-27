<?php section('navigation') ?>
    <ul class="nav navbar-nav">
        <?php foreach (navigation() as $item): ?>
            <li<?= $item['isActive'] ? ' class="active"' : '' ?>><a href="<?= $item['url'] ?>"><?= $item['title'] ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php section('navigation') ?>
