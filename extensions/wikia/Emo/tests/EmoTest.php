<?php
require_once dirname(__FILE__) . '/../Emo.setup.php';

class EmoTest extends WikiaBaseTest {

	/**
	 * @dataProvider emoTagDataProvider
	 * @param $wikiText: wiki string that needs to be put throught mediawiki parser
	 * @param $expectedOutput: ...
	 */
	public function testEmoTag($wikiText, $expectedOutput) {
		$this->assertEquals($expectedOutput, $this->parse($wikiText));
	}

	protected function parse($text) {
		$parser = F::build('Parser');
		$parserOutput = $parser->parse($text, F::build('Title'), F::build('ParserOptions'), false);
		return $parserOutput->getText();
	}

	public function emoTagDataProvider() {
		return array(
			array('foo', 'foo'),
			array('<emo>simple test</emo>', '<nowiki>simple test</nowiki>'),
			array('<emo count="5">foo</emo>', '<nowiki>foo foo foo foo foo</nowiki>'),
			array('<emo count="0">foo</emo>', '<nowiki></nowiki>'),
			array('<emo count="invalid value">foo</emo>', '<nowiki></nowiki>'),
			array('<emo count="-1">foo</emo>', '<nowiki></nowiki>'),
			array("<emo>''bar''</emo>", '<nowiki><i>bar</i></nowiki>'),
			);
	}

}
