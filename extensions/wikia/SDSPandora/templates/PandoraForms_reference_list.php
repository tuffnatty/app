<div class="input-group <?= $type ?>">
	<label for="<?= $name ?>"><?= $labelMsg ?></label>
	<input type="text" name="<?= $name ?>-reference" id="<?= $name ?>" data-suggestions-type="<?= (isset($suggestionsType)) ?
		$suggestionsType : '' ?>" class="suggestions" autocomplete="off">
	<ul class="reference-list <?= (!is_array( $list )) ? 'hidden' : ''; ?>">
		<?php
		if ( !is_array( $list ) ) $list = array();
		$pos = 0;
		foreach ($list as $item) {
			echo $app->renderView(
				'PandoraFormsController',
				'referenceItem',
				array(
					'item' => $item,
					'pos' => $pos,
					'propName' => $name,
					'removeBtnMsg' => wfMessage('sdsvideometadata-vc-remove-item')->text()
				)
			);
			$pos = $pos + 1;
		}
		?>
	</ul>
</div>