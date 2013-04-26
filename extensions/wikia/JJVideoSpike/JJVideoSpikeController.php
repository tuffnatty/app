<?php
/**
 * @author: Jacek Jursza <jacek@wikia-inc.com>
 * Date: 23.04.13 15:36
 *
 */

class JJVideoSpikeController extends WikiaSpecialPageController {

	public function __construct() {

		// parent SpecialPage constructor call MUST be done
		parent::__construct( 'JJVideoSpike', '', false );
	}


	private function getArticleId( $param = 'art' ) {

		$title = $this->request->getVal( $param, '' );
		$art = false;

		if ( !empty( $title ) ) {

			$titleObj = Title::newFromText( $title );
			if ( !empty( $titleObj ) && $titleObj->exists() ) {

				$art = $titleObj->getArticleID();
			}
		}

		return $art;
	}

	public function index() {


		die("AAAA");

	}

	public function test() {

		$articleId = $this->getArticleId();
		if ( !$articleId ) {
			die("ARTICLE NOT FOUND");
		}

		$art = new ArticleSubject( $articleId );


		$subjectsObject = new WikiSubjects();
		$art->setAllSubjectList( $subjectsObject->get() );

		$subjects = $art->getSubjects();
		var_dump( $subjects );

		die("<hr>!");
	}


	public function testSuggestions() {

		$articleId = $this->getArticleId();
		if ( !$articleId ) {
			die("ARTICLE NOT FOUND");
		}

		$suggestions = new ArticleVideoSuggestion( $articleId );

		$result = $suggestions->getBySubject();

		$subjects = $suggestions->getSubject();

		$this->setVal( 'subject', $subjects[0][0] );

		$this->inflateWithVideoData( $result );


		$this->setVal( 'results' , $result );


	}

	private function inflateWithVideoData( &$result ) {

		$config = array(
			'contextWidth' => 460,
			'maxHeight' => 250
		);

		foreach ( $result['items'] as $i => $r ) {


			$title = Title::newFromText( $r['title'], NS_FILE );
			$file = wfFindFile( $title );

			$htmlParams = array(
				'custom-title-link' => $title,
				'linkAttribs' => array( 'class' => 'video-thumbnail' )
			);

			if ( !empty( $file ) ) {
				$thumb = $file->transform( array('width'=>460, 'height'=>250), 0 );
				$result['items'][$i]['thumb'] = $thumb->toHtml( $htmlParams );
			}
		}

	}

}