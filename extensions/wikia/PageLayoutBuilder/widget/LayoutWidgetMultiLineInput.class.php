<?php

class LayoutWidgetMultiLineInput extends LayoutWidgetInput {
	public function getName() {
		return 'plb_mlinput';
	}

	public function renderForForm() {
		$style = "";
		if($this->error) {
			$style = "border-color: red; border-style: solid;";
		}

		return XML::element('textarea',
							array(
								'name' => 'plb_'.$this->getAttrVal("id"),
								'type' => 'text',
								'style' => $style,
								'class' => 'plb-mlinput-textarea '.(empty($this->value) ? "plb-empty-input":""),
							),
							empty($this->value) ? $this->getAttrVal('instructions', true) : $this->value,
							false );
	}

	public function renderForPreview() {
		wfLoadExtensionMessages( 'PageLayoutBuilder' );
		return "<p>".wfMsg('plb-parser-preview-mlinput')."</p>";
	}

	public function renderForResult() {
		return "<p>".$this->value."</p>";
	}

	public function renderForResultEmpty($url) {
		return "<p>".wfMsg("plb-parser-empty-value", array("%1" => $url ))."</p>";
	}

	public function renderForRTE() {
		wfLoadExtensionMessages("PageLayoutBuilder");
		$caption = $this->getAttrVal('caption'); // is not default value is error message for RTE
		$sampleText = wfMsg('plb-parser-preview-mlinput');
		return
			XML::openElement('p',
				array(
					"class" => "plb-rte-widget plb-rte-widget-plb_mlinput",
				) + $this->getBaseParamForRTE())
			."<span class=\"plb-rte-widget-caption\">"
				.htmlspecialchars(empty($caption) ? wfMsg("plb-editor-enter-caption") : $caption)
				."</span>"
			."<span class=\"plb-rte-widget-sample-text\">" . htmlspecialchars($sampleText) . "</span>"
			.$this->getRTEUIMarkup()
			.XML::closeElement('p');
	}

	function isParagraph() {
		return true;
	}

	public function getAllAttrs() {
		//param name => default value
		return array(
				'id' => '',
				'caption'  => '',
				'instructions' => '',
				'required'  => 0,
		);
	}
}

