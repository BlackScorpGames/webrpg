<div class="equipment <?= $activeCharacter['class'] ?>">
    <div class="body <?= $activeCharacter['gender'] ?> walk <?= $viewDirection ?> big"></div>
    <?php foreach ($equipmentSlots as $slotNumber => $slotName): ?>
        <div class="<?= $slotName ?> <?= $activeCharacter['gender'] ?> <?= $activeCharacter['inventory'][$slotNumber]['itemName'] ?> walk <?= $viewDirection ?> big"></div>
    <?php endforeach; ?>
</div>