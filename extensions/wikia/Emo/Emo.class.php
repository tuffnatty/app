<?php
class Emo {
	private static $counter = 1;
	/*
	 * @brief This function set renderTag hook
	 *
	 * @param Parser parser
	 *
	 * @return true
	 */
	public function onParserFirstCallInit( Parser $parser ) {
		/*
		 * @first argument to $parser->setHook is name of tag, eg: <emo>
		 * 
		 * @second argument to $parser->setHook is the class method to be executed when
		 * tag is parsed
		 */
		$parser->setHook('emo', array($this, 'renderTag'));

		// You can disable the cache! (Uh... I guess this doesn't work?)
		// $parser->disableCache();

		// this function must always return true
		// this has no meaning, but must be: http://www.mediawiki.org/wiki/Manual:Tag_Extensions
		return true;
	}

	/*
	 * @param $input Input between the tags, or null if the tag is closed, i.e. <sample />
	 * @param $args Tag arguments, which are entered like HTML attributes; this is an associative array indexed by attribute names
	 *
	 * (opt) Parser $parser
	 * (opt) PPFrame $frame Used with $parser to provide with more context in which the extension was called
	 */
	public function renderTag( $input, $params /* For MW tag ext: @param Parser $parser, PPFrame $frame */ ) {

		$app = F::app();

		if ( !isset($params['count']) ) {
			$params['count'] = 1;
		}

		$returnString = str_repeat($input . ' ' , $params['count']);

		// check wikia globals to see if Rich Text Editor Parser is enabled
		if ( !empty($app->wg->RTEParserEnabled) ) {
			// RTE Extension
			$id = 'emo-' . self::$counter++;

			// render <emo> node
			// TODO: This factory method is deprecrated, construct class using new operator instead i.e new Xml($params)
			$html = F::build('Xml',
				array('div', 
					array(
						'class' => 'emo',
						'data-message' => $input,
					), 
				trim($returnString)
			),
			'element');
			return $html;

		} else {
			// page-view html
			return '<nowiki>' . trim($returnString) . '</nowiki>';
		}
	}
}
