<?php
/**
 * Class definition for Wikia\Search\QueryService\Select\VideoGenre
 */
namespace Wikia\Search\QueryService\Select;
use Solarium_Result_Select, Solarium_Query_Select;
/**
 * Searches for video genres, with faceting.
 * @author relwell
 *
 */
class VideoGenre extends OnWiki
{
	/**
	 * Facets and counts
	 * @var array
	 */
	protected $facets = [];
	
	public function getFormulatedQuery() {
		return 'video_genres_txt:*';
	}
	
	public function registerFilterQueries( Solarium_Query_Select $query ) {
		$query->setFilterQueries( 'genre', sprintf( 'video_genres_txt:%s', $query ) ); 
	}
	
	public function getQueryFieldsString() {
		return '';
	}
	
	public function prepareResponse( Solarium_Result_Select $result ) {
		$this->setFacets( $result->getFacetSet()->getFacet( 'video_genres_txt' ) );
		return parent::prepareResponse( $result );
	}
	
	/**
	 * @return the $facets
	 */
	public function getFacets() {
		return $this->facets;
	}

	/**
	 * @param multitype: $facets
	 */
	public function setFacets($facets) {
		$this->facets = $facets;
	}
	
	protected function registerComponents( Solarium_Query_Select $query ) {
		return $this->registerFacets( $query )
		            ->registerQueryParams( $query );
	}
	
	protected function registerFacets( Solarium_Query_Select $query ) {
		$facetSet = $query->getFacetSet();
		$facetSet->createFacetField( 'video_genres_txt' )->setField( 'video_genres_txt' );
		return $this;
	}
	
	public function extractMatch() {
		return null;
	}
}