<?php

/**
 * Class VideoSuggestTestHooksHelper
 */
class VideoSuggestTestHooksHelper {

	const VIDEO_THUMB_DEFAULT_WIDTH = 160;
	const VIDEO_THUMB_DEFAULT_HEIGHT = 90;

	public static function onPageHeaderVideoSuggest ( &$html ) {
		if ( F::app()->wg->Title->getNamespace() != NS_MAIN ) {
			return true;
		}

		$articleName = F::app()->wg->Title->getPrefixedText();

		$config = new Wikia\Search\Config;

		// search page (video only)
		$config->setFilterQueryByCode( \Wikia\Search\Config::FILTER_VIDEO );
		$config->setLimit( 5 );
		$config->setNamespaces( array( NS_FILE ) );
		$config->setCityID( Wikia\Search\QueryService\Select\Video::VIDEO_WIKI_ID );
		$config->setQuery( $articleName );
		$response = (new \Wikia\Search\QueryService\Factory)->getFromConfig( $config )->search();

		$html = self::convertToHtml( $response );

		// VET suggestion
		$config->setVideoEmbedToolSearch( true );
		$config->setQuery( $articleName );
		$response = (new \Wikia\Search\QueryService\Factory)->getFromConfig( $config )->search();

		$html .= self::convertToHtml( $response, 'VET Suggestion' );
		$html .= '<br style="clear: both" /><br/>';

		return true;
	}

	public static function convertToHtml( $response, $subTitle = 'Search Page' ) {
		$float = ( $subTitle == 'Search Page' ) ? 'left' : 'right';
		$html =<<<TXT
<div class="results-wrapper grid-4 alpha" style="float:$float; width:300px;">
<h1>$subTitle</h1><br/>
<div class="results-wrapper grid-3 alpha" style="width:300px;">
<ul class="Results" style="list-style-type: none;">
TXT;

		foreach( $response  as $result ) {
			$singleVideoData['title'] = $result->getTitle();

			if ( empty( $singleVideoData['title'] ) ) {
				continue;
			}

			WikiaFileHelper::inflateArrayWithVideoData(
				$singleVideoData,
				Title::newFromText($singleVideoData['title'], NS_FILE),
				self::VIDEO_THUMB_DEFAULT_WIDTH,
				self::VIDEO_THUMB_DEFAULT_HEIGHT,
				true
			);

			if ( empty( $singleVideoData['thumbnail'] ) ) {
				continue;
			}

			$html .=<<<TXT
<li class="result">
	<article>
		<div class="grid-1 alpha">
			{$singleVideoData['thumbnail']}

			<a href="{$singleVideoData['url']}" class="result-link" data-pos="1">{$singleVideoData['title']}</a>
		</div>
	</article>
</li>
TXT;
		}

		$html .= '</ul></div></div>';

		return $html;
	}

}