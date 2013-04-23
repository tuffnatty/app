<?php
/**
 * @author: Jacek Jursza <jacek@wikia-inc.com>
 * Date: 23.04.13 15:46
 *
 */

class ArticleSubject {

	protected $articleId;
	protected $articleBody;
	protected $allSubjectList;

	public function __construct( $articleId, $articleBody='' ) {

		$this->articleId  = $articleId;
		if ( !empty( $articleBody ) ) {
			$this->articleBody = $articleBody;
		}
	}

	public function setAllSubjectList( $subjectList ) {
		$this->allSubjectList = $subjectList;
	}

	public function getAllSubjectList() {
		return $this->allSubjectList;
	}

	protected function fetchArticleBody() {

		$titleObject = Title::newFromID( $this->articleId );
		$article = false;

		if ( !empty( $titleObject ) && $titleObject->exists() ) {

			$article = new Article( $titleObject );

		}
		else {

			throw new Exception("Couldnt load article");
		}

		if ( $article ) {
			$this->articleBody = $article->getContent();
		} else {

			throw new Exception(" Couldnt create article");
		}
	}

	public function getSubjects() {

		if ( empty( $this->articleBody ) ) {
			$this->fetchArticleBody();
		}

		var_dump( $this->articleBody );

	}



}