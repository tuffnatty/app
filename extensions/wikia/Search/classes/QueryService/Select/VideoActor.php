<?php
/**
 * Class definition for Wikia\Search\QueryService\Select\VideoActor
 */
namespace Wikia\Search\QueryService\Select;

class VideoActor extends Lucene {

	/**
	 * Given a search query (which is the name of an actor), get videos from video wiki matching it 
	 * (non-PHPdoc)
	 * @see \Wikia\Search\QueryService\Select\AbstractSelect::getQueryClausesString()
	 * @return string
	 */
	protected function getFormulatedQuery() {
		$query = $this->config->getQuery()->getSolrQuery();
		return sprintf( 'video_actors_txt:"%s"', $query );
	}
}