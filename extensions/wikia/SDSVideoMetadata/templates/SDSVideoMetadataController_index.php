<div class="VMD-wrapper">
	<?php if ( $isCorrectFile ) { ?>
		<h1 class="VMD-header"><?= $file ?></h1>
		<form class="WikiaForm VMD-form" id="VMDForm" method="POST">

			<fieldset>
				<legend class="VMD-header"><?= wfMessage('sdsvideometadata-common-metadata-legend')->text() ?></legend>

				<!-- Description -->
				<?= $formBuilder->renderField( 'description' ); ?>

				<!-- Language -->
				<?= $formBuilder->renderField( 'inLanguage' ); ?>

				<!-- Subtitles -->
				<?= $formBuilder->renderField( 'subTitleLanguage' ); ?>

				<!-- Video object type selection -->
				<div class="input-group">
					<label for="vcType"><?= wfMessage('sdsvideometadata-vc-select-type')->text() ?> <small>(<?=
						wfMessage
					('sdsvideometadata-vc-required') ?>)</small></label>
					<select name="vcType" id="vcType" data-type="<?= isset( $vcObj['vcType'] ) ? $vcObj['vcType'] : '' ?>">
						<option value="" selected="selected"><?= wfMessage('sdsvideometadata-vc-type-select')->text()
							?></option>
						<option value="VideoClipGamingVideo"><?= wfMessage('sdsvideometadata-vc-type-gaming')->text() ?></option>
						<option value="VideoClipTVVideo"><?= wfMessage('sdsvideometadata-vc-type-tv')->text() ?></option>
						<option value="VideoClipMovieTrailersVideo"><?= wfMessage('sdsvideometadata-vc-type-movie')->text() ?></option>
						<option value="VideoClipTravelVideo"><?= wfMessage('sdsvideometadata-vc-type-travel')->text() ?></option>
						<option value="VideoClipCookingVideo"><?= wfMessage('sdsvideometadata-vc-type-cooking')->text() ?></option>
						<option value="VideoClipCraftVideo"><?= wfMessage('sdsvideometadata-vc-type-craft')->text() ?></option>
						<option value="VideoClipHowToVideo"><?= wfMessage('sdsvideometadata-vc-type-how-to')->text() ?></option>
						<option value="VideoClipMusicVideo"><?= wfMessage('sdsvideometadata-vc-type-music')->text() ?></option>
					</select>
				</div>

			</fieldset>

			<fieldset id="VMDSpecificMD" class="hidden VMD-details">
				<legend class="VMD-header"><?= wfMessage('sdsvideometadata-type-specific-metadata-legend')->text() ?></legend>

				<!-- Recipe -->
				<?= $formBuilder->renderField( 'recipe' ); ?>

				<!-- Song -->
				<?= $formBuilder->renderField( 'musicRecording' ); ?>


				<!-- Location -->
				<?= $formBuilder->renderField( 'contentLocation' ); ?>

				<!-- Game -->
				<?= $formBuilder->renderField( 'game' ); ?>

				<!-- TV Series -->
				<?= $formBuilder->renderField( 'tvSeries' ); ?>

				<!-- Season -->
				<?= $formBuilder->renderField( 'tvSeason' ); ?>

				<!-- Movie -->
				<?= $formBuilder->renderField( 'movie' ); ?>

				<!-- Type -->
				<?= $formBuilder->renderField( 'keywords' ); ?>

				<!-- Age gate -->
				<?= $formBuilder->renderField( 'isFamilyFriendly' ); ?>

				<!-- Soundtrack -->
				<?= $formBuilder->renderField( 'soundtrack' ); ?>

				<!-- Setting -->
				<?= $formBuilder->renderField( 'setting' ); ?>

				<!-- Characters -->
				<?= $formBuilder->renderField( 'characters' ); ?>

				<!-- Actors -->
				<?= $formBuilder->renderField( 'actors' ); ?>

				<!-- Celebrity -->
				<?= $formBuilder->renderField( 'celebrity' ); ?>

			</fieldset>

			<div class="input-group">
				<label for="vcCompleted">
					<?= wfMessage('sdsvideometadata-vc-finished-flag')->text() ?>
					<input type="checkbox" name="vcCompleted" id="vcCompleted" value="1" <?= !empty($isCompleted) ? "checked" : "";?> >
				</label>
			</div>

			<?php if (!empty($wasPasted)): ?>
				<p><?= (isset($success) && $success === true ) ?  wfMessage('sdsvideometadata-vc-save')->text() : $errorMessage ?></p>
			<?php endif; ?>

			<input type="submit" id="VMDFormSave" value="<?= wfMessage('sdsvideometadata-save')->text() ?>"
			       disabled="disabled">
			<button id="VMDSkip" class="secondary"><?= wfMessage('sdsvideometadata-skip')->text() ?></button>

		</form>
		<div id="VMD-player-wrapper" class="VMD-player-wrapper">
			<div>
				<?= isset($embedCode) ? $embedCode : "" ?>
			</div>
		</div>
	<?php } else { ?>
		<?= wfMessage('sdsvideometadata-error-no-video-file')->text() ?>
	<?php } ?>
</div>

