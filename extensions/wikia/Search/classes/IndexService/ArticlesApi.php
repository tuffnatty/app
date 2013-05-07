<?php
/**
 * Class definition for \Wikia\Search\IndexService\DefaultContent
 * @author relwell
 */
namespace Wikia\Search\IndexService;
use Wikia\Search\Utilities, simple_html_dom;
/**
 * This is intended to provide core article content
 * @author relwell
 * @package Search
 * @subpackage IndexService
 */
class ArticlesApi extends AbstractService
{
	/**
	 * Returns the fields required to make the document searchable (specifically, wid and title and body content)
	 * @see \Wikia\Search\IndexService\AbstractService::execute()
	 * @return array
	 */
	public function execute() {
		$service = $this->getService();
		$pageId = $service->getCanonicalPageIdFromPageId( $this->currentPageId );

		// we still assume the response is the same format as MediaWiki's
		$response   = $service->getParseResponseFromPageId( $pageId );
		
		// ensure the response is an array, even if empty.
		$response   = $response == false ? array() : $response;
		$titleStr   = $service->getTitleStringFromPageId( $pageId );
		
		$pageFields = array(
				
		);
		
		return array_merge( 
				$this->getPageContentFromParseResponse( $response ), 
				$this->getHeadingsFromParseResponse( $response ),
				$pageFields 
				);
	}
	
	/**
	 * Wraps logic for creating the initial result array, based on which implementation we're using.
	 * The old version strips HTML from the backend; the new version strips HTML within the IndexService.
	 * @param array $response
	 * @return array
	 */
	protected function getPageContentFromParseResponse( array $response ) {
		$html = empty( $response['parse']['text']['*'] ) ? '' : $response['parse']['text']['*'];
		if ( $this->getService()->getGlobal( 'AppStripsHtml' ) ) {
			return $this->prepValuesFromHtml( $html );
		}
		return [ 'text' => html_entity_decode($html, ENT_COMPAT, 'UTF-8') ];
	}
	
	/**
	 * Returns an array with section headings for the page.
	 * @param array $response
	 * @return array
	 */
	protected function getHeadingsFromParseResponse( array $response ) {
		$headings = array();
		if (! empty( $response['parse']['sections'] ) ) {
			foreach( $response['parse']['sections'] as $section ) {
				$headings[] = $section['line'];
			}
		}
		return [ 'headings' => $headings ];
	}
	
	/**
	 * Allows us to strip and parse HTML
	 * By the way, if every document on the site was as big as the Jim Henson page,
	 * then it would take under two minutes to parse them all using this function. 
	 * So this scales on the application side. I promise. I mathed it.
	 * @param string $html
	 * @return array
	 */
	protected function prepValuesFromHtml( $html ) {
		$result = array();
		$paragraphs = array();
		// default value; we'll overwrite if dom can parse
		$plaintext = preg_replace( '/\s+/', ' ', html_entity_decode( strip_tags( $html ), ENT_COMPAT, 'UTF-8' ) );
		
		$dom = new \simple_html_dom( html_entity_decode($html, ENT_COMPAT, 'UTF-8') );
		if ( $dom->root ) {
			$this->transformTablesFromDom( $dom );
			$this->transformListsFromDom( $dom );
			$plaintext = $this->getPlaintextFromDom( $dom );
		}
		
		return  array_merge( $result,
				[
				'content' => $plaintext
				]);
	}

	/**
	 * Returns all text from tables as plaintext, and then removes them.
	 * @param simple_html_dom $dom
	 * @return string
	 */
	protected function transformTablesFromDom( simple_html_dom $dom ) {
		foreach( $dom->find( 'table' ) as $table ) {
			$tableInfo = [];
			foreach ( $table->find( 'tr' ) as $row ) {
				$rowArray = [];
				foreach ( $row->find( 'td' ) as $cell ) {
					$rowArray[] = $this->strip( $cell->outertext );
				}
				$tableInfo[] = $rowArray;
			}
			$table->outertext = json_encode( ['table' => $tableInfo] );
		}
	}
	
	protected function transformListsFromDom( simple_html_dom $dom ) {
		foreach ( $dom->find( 'ul,ol' ) as $list ) {
			$listArray = [];
			foreach ( $list->find( 'li' ) as $item ) {
				$listArray[] = $this->strip( $item->outertext );
			}
			$list->outertext = json_encode( ['list' => $listArray] );
		} 
	}
	
	/**
	 * Returns HTML-free article text. Appends any tables to the bottom of the dom.
	 * @param simple_html_dom $dom
	 * @return string
	 */
	protected function getPlaintextFromDom( simple_html_dom $dom ) {
		$dom->load( $dom->save() );
		return $this->strip( $dom->outertext );
	}
	
	protected function strip( $string ) {
		return  trim( preg_replace( '/\s+/', ' ', strip_tags( $string, 'u,i,a,b' ) ) );
	}
}