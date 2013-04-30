<?php
/**
 * User: artur
 * Date: 29.04.13
 * Time: 12:50
 */

class WikiPageCategoryService {
	const TABLE_NAME = "wiki_page_category";
	private $wiki_id;
	private $connection;

	function __construct( $wiki_id ) {
		$this->wiki_id = $wiki_id;
		$this->connection = F::app()->wf->getDB( DB_MASTER, array(), "dataware" /* fixme */ );
	}

	function setCategory( $page_id, $category_name ) {
		$res = $this->connection->select( self::TABLE_NAME,
			"category",
			array("wiki_id" => $this->wiki_id, "page_id" => $page_id)
		);
		if( $res->numRows() > 0 ) {
			$this->connection->update( self::TABLE_NAME,
				array('category' => $category_name ),
				array("wiki_id" => $this->wiki_id, "page_id" => $page_id)
			);
			//if( $this->connection->affectedRows() != 1 ) {
			//	throw new Exception("Wrong number of affected rows (" . $this->connection->affectedRows() . "  expected 1).");
			//}
		} else {
			$this->connection->insert( self::TABLE_NAME,
				array('category' => $category_name, "wiki_id" => $this->wiki_id, "page_id" => $page_id)
			);

			if( $this->connection->affectedRows() != 1 ) {
				throw new Exception("Wrong number of affected rows (" . $this->connection->affectedRows() . "  expected 1).");
			}
		}
	}

	public function getCategory( $page_id ) {
		$res = $this->connection->select( self::TABLE_NAME,
			"category",
			array("wiki_id" => $this->wiki_id, "page_id" => $page_id)
		);
		if( $res->numRows() > 0 ) {
			$row = $res->fetchRow();
			return $row["category"];
		}
		return null;
	}

	public function getArticlesByCategory( $categoryName ) {
		$res = $this->connection->select( self::TABLE_NAME,
			"page_id",
			array( "category" => $categoryName )
		);
		$pages = array();
		$row = null;
		while ( ( $row = $res->fetchRow() ) != null ) {
			$title = Title::newFromID( $row["page_id"] );
			if( $title ) {
				$pages[] = new Article( $title );
			}
		}
		return $pages;
	}

	public function getArticleTitlesByCategory( $categoryName ) {
		$articles = $this->getArticlesByCategory( $categoryName );
		$titles = array();
		foreach( $articles as $i => $article ) {
			$titles[] = $article->getTitle()->getText();
		}
		return $titles;
	}
}
