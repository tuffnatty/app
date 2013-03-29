<?
/** @var $abTesting AbTesting */
?>
<tr class="exp<?= empty( $showDetails ) ? ' collapsed' : '' ?>" data-id="<?= $experiment[ 'id' ] ?>">
	<td class="arrow-nav"><img class="arrow" src="<?= $wg->BlankImgUrl ?>" /></td>
	<td><?= $experiment[ 'id' ] ?></td>
	<td><?= htmlspecialchars( $experiment[ 'name' ] ) ?></td>
	<td><?= htmlspecialchars( $experiment[ 'description' ] ) ?></td>
	<td><?= htmlspecialchars( $experiment[ 'status' ] ) ?></td>
	<td><?= htmlspecialchars( $experiment[ 'next_activate' ] ) ?></td>
	<td class="actions">
		<? foreach( $experiment[ 'actions' ] as $action ): ?>
			<button data-command="<?= $action[ 'cmd' ] ?>"><?= $action[ 'text' ] ?></button>
		<? endforeach ?>
	</td>
</tr>
