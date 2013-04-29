<?php

/**
 * Class VideoMetaService
 */

class VideoMetaService {

	// Predefine some limits to use for term frequency
	const LIM_TERM_SM  = 10;
	const LIM_TERM_MED = 75;
	const LIM_TERM_LG  = 150;

	// Some fuzzy limits on what terms to return.  Specific should be terms
	// specific to the subject of the title, Related should include specicific
	// terms and those related to the more general topic of the video (e.g. LOTR for
	// a video specifically about the character Legolas)
	const LIM_SPECIFIC = -1;
	const LIM_RELATED = -2;

	/**
	 * termsForTitle
	 *
	 * Return a list of terms for a video title
	 *
	 * @param string $videoTitle - The text title for a video
	 * @param int $maxCount - Filter the terms returned to only those
	 *                        who appear less than maxCount times across all videos
	 * @return array - An array of VideoMetaTerm objects
	 */
	public function termsForTitle ( $videoTitle, $maxCount = 0 ) {
		$app = F::App();

		$dbr = wfGetDB(DB_SLAVE, array(), $app->wg->ExternalDatawareDB);

		$sql = "SELECT a.term, " .
			   "       a.type, " .
			   "       c.count " .
			   "  FROM vid_term a, " .
			   "       vid_title b, " .
			   "       vid_term_freq c, " .
			   "       vid_title_term x " .
			   "WHERE a.id = x.term_id " .
			   "  AND b.id = x.title_id " .
			   "  AND a.id = c.term_id " .
		       "  AND b.title = " . $dbr->addQuotes($videoTitle) . ' ';

		// Limit by count if we get a maxCount passed in
		if ($maxCount > 0) {
			$sql  .= "AND c.count <= $maxCount";
		}

		$res = $dbr->query($sql, __METHOD__);

		$terms = array();
		while ($row = $res->fetchObject()) {
			$term = new VideoMetaTerm($row->type, $row->term, $row->count);
			if (!empty($term)) {
				$terms[] = $term;
			}
		}

		// If a fuzzy limit was specified, first grab all the terms and then inspect
		// them to determine the best terms to return rather than using an arbitrary number
		// number for all titles.
		if ($maxCount < 0) {
			// For now just use arbitrary numbers until we can investigate good algorithms for
			// determine the right limit for the dataset.
			switch ($maxCount) {
				case self::LIM_SPECIFIC:
					$terms = self::filterTerms($terms, self::LIM_TERM_SM);
					break;
				case self::LIM_RELATED:
					$terms = self::filterTerms($terms, self::LIM_TERM_MED);
					break;
			}
		}

		return $terms;
	}

	/**
	 * relatedVideos
	 *
	 * Returns a list of relevant videos based on a list of terms passed in.
	 *
	 * @param array $terms - An array of VideoMetaTerm objects
	 * @param string $type - The type of term to constrain on.  If not give, the first
	 *                       term in $terms will be examined and its type will be used to filter
	 *                       the rest
	 * @throws Exception "Empty array of terms passed"
	 * @return array - An associative array with keys:
	 *                 video: VideoMeta objects
	 *                 relevancy: A floating point number between 0 and 1 giving how relevant this video
	 *                            is with 0 being least relevant and 1 being most relevant.
	 */
	public function relatedVideos ( $terms, $type = null ) {
		$app = F::App();

		if (count($terms) == 0) {
			throw new Exception('Empty array of terms passed');
		}

		$dbr = wfGetDB(DB_SLAVE, array(), $app->wg->ExternalDatawareDB);

		$termText = array();
		foreach ( $terms as $term ) {
			// If no type has been set, use the first type in the list
			if (empty($type)) {
				$type = $term->type();
			}

			// Skip this term if its not of the type we're looking for
			if ( $term->type() != $type ) {
				continue;
			}

			$termText[] = $dbr->addQuotes( $term->value() );
		}

		$sql = "SELECT a.title as title, COUNT(b.id) as freq " .
			   "  FROM vid_title a, " .
			   "       vid_term b, " .
			   "       vid_title_term x " .
			   " WHERE a.id = x.title_id " .
			   "   AND b.id = x.term_id " .
			   "   AND b.term IN (" . implode(',', $termText) . ") " .
			   "GROUP BY a.title " .
			   "HAVING COUNT(b.id) > 1 " .
			   "ORDER BY COUNT(b.id) DESC";

		$res = $dbr->query($sql, __METHOD__);

		$totalTypes = count($terms);
		$videoTitles = array();
		while ($row = $res->fetchObject()) {
			$videoMeta = new VideoMeta( $row->title );
			$videoTitles[] = array( "video"     => $videoMeta,
									"relevancy" => ( $row->freq/$totalTypes ) );
		}

		return $videoTitles;
	}

	/**
	 * leastUsedKeywords
	 *
	 * Return an array of the least used keywords (a term where type == 'keyword')
	 *
	 * @param int $threshold - Do not return any keywords used more than $threshold times
	 * @param int $limit - Return only $limit keywords
	 * @return array - An array of VideoMetaTerm objects
	 */
	public function leastUsedKeywords ( $threshold = 10, $limit = 100 ) {
		return self::fetchTerms( $threshold, $limit, 'ASC', 'keyword');
	}

	/**
	 * mostUsedKeywords
	 *
	 * Return an array of the most used keywords (a term where type == 'keyword')
	 *
	 * @param int $threshold - Do not return any keywords used less than $threshold times
	 * @param int $limit - Return only $limit keywords
	 * @return array - An array of VideoMetaTerm objects
	 */
	public function mostUsedKeywords ( $threshold = 10000, $limit = 100 ) {
		return self::fetchTerms( $threshold, $limit, 'DESC', 'keyword');
	}

	/**
	 * leastUsedTerms
	 *
	 * Return an array of the least used terms
	 *
	 * @param int $threshold - Do not return any keywords used more than $threshold times
	 * @param int $limit - Return only $limit keywords
	 * @param string $type - The type of term to return (e.g. 'keyword', 'category', 'genre')
	 * @return array - An array of VideoMetaTerm objects
	 */
	public function leastUsedTerms ( $threshold = 10, $limit = 100, $type = '' ) {
		return self::fetchTerms( $threshold, $limit, 'ASC', $type);
	}

	/**
	 * mostUsedTerms
	 *
	 * Return an array of the most used terms;
	 *
	 * @param int $threshold - Do not return any keywords used less than $threshold times
	 * @param int $limit - Return only $limit keywords
	 * @param string $type - The type of term to return (e.g. 'keyword', 'category', 'genre')
	 * @return array - An array of VideoMetaTerm objects
	 */
	public function mostUsedTerms ( $threshold = 10000, $limit = 100, $type = '' ) {
		return self::fetchTerms( $threshold, $limit, 'DESC', $type);
	}

	/**
	 * fetchTerms
	 *
	 * Returns an array of terms
	 *
	 * @param $threshold - A threshold on the term frequency to return
	 * @param int $limit - Return only $limit keywords
	 * @param string $sort - The sort direction of the terms returned
	 * @param string $type - The type of term to return (e.g. 'keyword', 'category', 'genre')
	 * @return array - An array of VideoMetaTerm objects
	 * @throws Exception
	 */
	private function fetchTerms ( $threshold, $limit, $sort = 'ASC',  $type = '' ) {
		$app = F::App();

		if ( !preg_match('/^(asc|desc)$/i', $sort)) {
			throw new Exception("Bad parameter: 3rd parameter to fetchTerms must be 'asc' or 'desc'");
		}

		$cmp = '<';
		if ( strtolower($sort) == 'desc' ) {
			$cmp = '>';
		}

		$sql = 'SELECT type, term, count ' .
			'  FROM vid_term, vid_term_freq ' .
			' WHERE term_id=id ' .
			"   AND count $cmp $threshold ";

		if (!empty($type)) {
			$sql .= "AND type = $type ";
		}

		$sql .= "ORDER BY count $sort " .
				" LIMIT $limit ";

		$dbr = wfGetDB(DB_SLAVE, array(), $app->wg->ExternalDatawareDB);
		$res = $dbr->query($sql, __METHOD__);

		$terms = array();
		while ($row = $res->fetchObject()) {
			$term = new VideoMetaTerm( $row->type, $row->term, $row->count );
			if (!empty($term)) {
				$terms[] = $term;
			}
		}

		return $terms;
	}
}

/**
 * Class VideoMeta
 */
class VideoMeta {

	private $title;

	/**
	 * @param string $title - A video title
	 */
	public function __construct ( $title ) {
		$this->title = $title;
	}

	/**
	 * @return string The video title for this VideoMeta object
	 */
	public function title () {
		return $this->title;
	}

	/**
	 * Return the list of terms for this video, optionally constraining on $type
	 * 
	 * @param string $type Only return terms of type $type
	 */
	public function terms ( $type = '' ) {

	}

	/**
	 * Return all existing types for this video
	 */
	public function types () {

	}
}

/**
 * Class VideoMetaTerm
 */
class VideoMetaTerm {

	private $type;
	private $value;
	private $count;

	/**
	 * @param string $type - The type of video term (e.g. 'keyword', 'category', 'genre')
	 * @param string $value - The value of the term (e.g., 'lotr', 'puppies')
	 * @param int $count - (OPTIONAL) the number of times this video term occurs across all videos
	 */
	public function __construct ( $type, $value, $count = 0 ) {
		$this->type  = $type;
		$this->value = $value;

		if ($count) {
			$this->count = $count;
		}
	}

	/**
	 * @return string - Type of this video term
	 */
	public function type () {
		return $this->type;
	}

	/**
	 * @return string - Value of this video term
	 */
	public function value () {
		return $this->value;
	}

	/**
	 * @return int - Frequency of this term across all videos
	 */
	public function frequency () {
		if (!$this->count) {
			// Load counts
		}

		return $this->count;
	}
}