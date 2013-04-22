<? if(!empty($results)): ?>

	<br /><h1>Genre: <?= ucfirst($genre) ?></h1>

	<ul>
	<?php foreach ( $results as $result ): ?>
	<li class="result">
		<b>
			<?php $title = $result->getTitle(); ?>

			<a href="<?= $result->getUrl(); ?>" ><?= $title ?></a>
		</b>

		<? if ($result->getVar('ns') == NS_FILE): ?>
			<p class="subtle">
				<? if (!$result->getVar('created_30daysago')) : ?>
				<span class="timeago abstimeago " title="<?= $result->getVar('fmt_timestamp') ?>" alt="<?= $result->getVar('fmt_timestamp') ?>">&nbsp;</span>
				<? else : ?>
				<span class="timeago-fmt"><?= $result->getVar('fmt_timestamp') ?></span>
				<? endif; ?>
				<?php
					if ( $videoViews = $result->getVideoViews() ) {
						echo '&bull; '.$videoViews;
					}
				?>
			</p>
		<? endif; ?>
		<?= $result->getText(); ?>
	</li>
	<? endforeach; ?>
	</ul>

<? else: ?>
	<br /><h1>Available Video Genres</h1>
	<ul>
	<?php foreach ( $facets as $facet => $count ): ?>
	    <li><a href="?genre=<?=$facet?>"><?=$facet?></a> ( <?=$count?> )</li>
	<?php endforeach; ?>
	</ul>

<? endif; ?>