<?php
/**
 * User: artur
 * Date: 26.04.13
 * Time: 17:31
 */

class ArticleInformation {
	private $article;

	function __construct( Article $article ) {
		$this->article = $article;
	}

	public function setArticle($article) {
		$this->article = $article;
	}

	public function getArticle() {
		return $this->article;
	}

	public function getSubjects() {
		$art = new ArticleSubject( $this->getArticle()->getTitle()->getArticleID() );
		$subjectsObject = new WikiSubjects();
		$art->setAllSubjectList( $subjectsObject->get() );
		return $art->getPrioritizedList();
	}
}
