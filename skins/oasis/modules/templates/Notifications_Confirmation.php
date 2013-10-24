<?php
	if ($confirmation != '') {
?>

<div class="global-notification <?= $confirmationClass ?> notification-from-server">
	<button class="close wikia-chiclet-button"><img src=" <?= $wg->StylePath ?>/oasis/images/icon_close.png"></button>
	<div class="msg"><?= $confirmation ?></div>
</div>

<?php
	}
?>
