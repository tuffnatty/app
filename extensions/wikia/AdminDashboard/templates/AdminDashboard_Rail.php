<aside class="AdminDashboardRail WikiaRail" id="AdminDashboardRail">
	<?= $wg->EnableFounderProgressBarExt ? $app->renderView( 'FounderProgressBar', 'widget' ) : '' ?>
	<?= $app->renderView( 'QuickStats', 'getStats') ?>
</aside>
