<div class="characterName"><?= $character['name']?></div>
<div class="equipment <?= $character['class'] ?>">
    <div class="body <?= $character['gender'] ?> walk <?= $character['viewDirection'] ?> big"></div>
    <?php foreach ($equipmentSlots as $slotNumber => $slotName): ?>
        <div class="<?= $slotName ?> <?= $character['gender'] ?> <?= $character['inventory'][$slotNumber]['itemName'] ?> walk <?= $character['viewDirection'] ?> big"></div>
    <?php endforeach; ?>
</div>