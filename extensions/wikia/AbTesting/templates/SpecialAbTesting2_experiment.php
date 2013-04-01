<?
/** @var $abTesting AbTesting */
$active = $experiment[ 'status' ] == AbTesting::STATUS_ACTIVE;
$has_next = $active ? $experiment[ 'next_deactivate' ] !== null : $experiment[ 'next_activate' ] !== null;
$classList = array('collapsed', 'exp');
if ($active) {
	$status = wfMessage('abtesting-status-active')->text();
	$timeAgo = $experiment[ 'next_deactivate' ] ? wfTimestamp(TS_ISO_8601, $experiment[ 'next_deactivate' ] ) : '';
	$timeString = $experiment[ 'next_deactivate' ] ? $wg->Lang->timeanddate($experiment[ 'next_deactivate' ] ) : '';
	$classList[] = 'status-active';
} else {
	$status = wfMessage('abtesting-status-inactive')->text();
	$timeAgo = $experiment[ 'next_activate' ] ? wfTimestamp(TS_ISO_8601, $experiment[ 'next_activate' ] ) : '';
	$timeString = $experiment[ 'next_activate' ] ? $wg->Lang->timeanddate($experiment[ 'next_activate' ] ) : '';
	$classList[] = 'status-inactive';
}
$class = implode(' ', $classList);
?>
<tr class="<?= $class ?>" data-row="0" data-id="<?= $experiment[ 'id' ] ?>">
	<td class="arrow-nav"><img class="arrow" src="<?= $wg->BlankImgUrl ?>" /></td>
	<td><?= $experiment[ 'id' ] ?></td>
	<td><?= htmlspecialchars( $experiment[ 'name' ] ) ?></td>
	<td class="column-status"><?= $status ?></td>
	<td>
		<? if ($active && $has_next): ?>
			Ends <span class="timeago" title="<?= $timeAgo ?>"><?= $timeString ?> </span>
		<? elseif (!$active && $has_next): ?>
			Starts <span class="timeago" title="<?= $timeAgo ?>"><?= $timeString ?> </span>
		<? endif; ?>
	</td>

</tr>
<?
$classList[] = 'details';
$class = implode(' ', $classList);
?>

<tr class="<?= $class ?>" data-row="1" data-id="<?= $experiment[ 'id' ] ?>">
	<td></td>
	<td><?= wfMsg( 'abtesting-heading-description' ) ?></td>
	<td colspan="3"><?= htmlspecialchars( $experiment[ 'description' ] ) ?></td>

</tr>
<tr class="<?= $class ?>" data-row="2" data-id="<?= $experiment[ 'id' ] ?>">
	<td></td>
	<td><?= wfMsg( 'abtesting-heading-actions' ) ?></td>
	<td colspan="3" class="actions">
		<? foreach( $experiment[ 'actions' ] as $action ): ?>
			<button data-command="<?= $action[ 'cmd' ] ?>"><?= $action[ 'text' ] ?></button>
		<? endforeach ?>
	</td>
</tr>
