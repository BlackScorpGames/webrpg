<div class="equipment <?= $activeCharacter['class'] ?>">
    <div class="body <?= $activeCharacter['gender'] ?> walk south big"></div>
    <?php foreach ($equipmentSlots as $slotNumber => $slotName): ?>
        <div class="<?= $slotName ?> <?= $activeCharacter['gender'] ?> <?= $activeCharacter['inventory'][$slotNumber]['itemName'] ?> walk south big"></div>
    <?php endforeach; ?>
</div>