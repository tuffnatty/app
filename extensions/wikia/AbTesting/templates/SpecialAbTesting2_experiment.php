<?
/** @var $abTesting AbTesting */
global $wgLang;
$active = $experiment[ 'status' ] == AbTesting::STATUS_ACTIVE;
$has_next = $active ? $experiment[ 'next_deactivate' ] !== null : $experiment[ 'next_activate' ] !== null;
if ($active) {
	$status = wfMessage('abtesting-status-active')->text();
	$timeAgo = $experiment[ 'next_deactivate' ] ? wfTimestamp(TS_ISO_8601, $experiment[ 'next_deactivate' ] ) : '';
	$timeString = $experiment[ 'next_deactivate' ] ? $wgLang->timeanddate($experiment[ 'next_deactivate' ] ) : '';
} else {
	$status = wfMessage('abtesting-status-inactive')->text();
	$timeAgo = $experiment[ 'next_activate' ] ? wfTimestamp(TS_ISO_8601, $experiment[ 'next_activate' ] ) : '';
	$timeString = $experiment[ 'next_activate' ] ? $wgLang->timeanddate($experiment[ 'next_activate' ] ) : '';
}
?>
<tr class="exp<?= empty( $showDetails ) ? ' collapsed' : '' ?>" data-id="<?= $experiment[ 'id' ] ?>">
	<td class="arrow-nav"><img class="arrow" src="<?= $wg->BlankImgUrl ?>" /></td>
	<td><?= $experiment[ 'id' ] ?></td>
	<td><?= htmlspecialchars( $experiment[ 'name' ] ) ?></td>
	<td><?= htmlspecialchars( $experiment[ 'description' ] ) ?></td>
	<td><?= $status ?></td>
	<td>
		<? if ($active && $has_next): ?>
			Ends <span class="timeago" title="<?= $timeAgo ?>"><?= $timeString ?> </span>
		<? elseif (!$active && $has_next): ?>
			Starts <span class="timeago" title="<?= $timeAgo ?>"><?= $timeString ?> </span>
		<? endif; ?>
	</td>

	<td class="actions">
		<? foreach( $experiment[ 'actions' ] as $action ): ?>
			<button data-command="<?= $action[ 'cmd' ] ?>"><?= $action[ 'text' ] ?></button>
		<? endforeach ?>
	</td>
</tr>
