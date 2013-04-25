<?php
/**
 * @author: Jacek Jursza <jacek@wikia-inc.com>
 * Date: 25.04.13 15:47
 *
 */

class ArticleVideoSuggestion {

	protected $articleId;
	protected $articleSubject;
	protected $wikiSubject;

	public function __construct( $articleId ) {

		$this->articleId = $articleId;

		$this->wikiSubject = new WikiSubjects();
		$this->articleSubject = new ArticleSubject( $articleId );
	}

	public function getBySubject() {


		$wikiSubject = $this->wikiSubject->get();

		$articleSubject = $this->articleSubject->getSubjects();



		var_dump( $wikiSubject );
		var_dump( $articleSubject );

	}
}