<div class="input-group <?= (!empty($type)) ? $type : '' ?>">
	<label for="<?= $name ?>"><?= $labelMsg ?><?= (!empty($required) && $required === true ) ? '* <small>(' . wfMessage
		('sdsvideometadata-vc-required')->text() . ')</small>' : '' ?></label>
	<?php if (!empty($textarea) && $textarea === true): ?>
		<textarea name="<?= $name ?>" id="<?= $name ?>"><?= (!empty($value)) ? htmlspecialchars($value) : '' ?></textarea>
	<?php else: ?>
		<input type="text" name="<?= $name ?>" id="<?= $name ?>" value="<?= (!empty($value)) ? htmlspecialchars($value) : '' ?>">
	<?php endif; ?>
</div>