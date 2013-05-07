<?php
/**
 * User: artur
 * Date: 23.04.13
 * Time: 16:18
 */

class VideoInformationProvider {

	protected $stopWords = array( 'a', 'a\'s', 'able', 'about', 'above', 'according', 'accordingly', 'across', 'actually',
		'after', 'afterwards', 'again', 'against', 'ain\'t', 'all', 'allow', 'allows', 'almost', 'alone', 'along', 'already',
		'also', 'although', 'always', 'am', 'among', 'amongst', 'an', 'and', 'another', 'any', 'anybody', 'anyhow', 'anyone',
		'anything', 'anyway', 'anyways', 'anywhere', 'apart', 'appear', 'appreciate', 'appropriate', 'are', 'aren\'t', 'around',
		'as', 'aside', 'ask', 'asking', 'associated', 'at', 'available', 'away', 'awfully', 'b', 'be', 'became', 'because',
		'become', 'becomes', 'becoming', 'been', 'before', 'beforehand', 'behind', 'being', 'believe', 'below', 'beside',
		'besides', 'best', 'better', 'between', 'beyond', 'both', 'brief', 'but', 'by', 'c', 'c\'mon', 'c\'s', 'came', 'can',
		'can\'t', 'cannot', 'cant', 'cause', 'causes', 'certain', 'certainly', 'changes', 'clearly', 'co', 'com', 'come', 'comes',
		'concerning', 'consequently', 'consider', 'considering', 'contain', 'containing', 'contains', 'corresponding', 'could',
		'couldn\'t', 'course', 'currently', 'd', 'definitely', 'described', 'despite', 'did', 'didn\'t', 'different', 'do', 'does',
		'doesn\'t', 'doing', 'don\'t', 'done', 'down', 'downwards', 'during', 'e', 'each', 'edu', 'eg', 'eight', 'either', 'else',
		'elsewhere', 'enough', 'entirely', 'especially', 'et', 'etc', 'even', 'ever', 'every', 'everybody', 'everyone', 'everything',
		'everywhere', 'ex', 'exactly', 'example', 'except', 'f', 'far', 'few', 'fifth', 'first', 'five', 'followed', 'following',
		'follows', 'for', 'former', 'formerly', 'forth', 'four', 'from', 'further', 'furthermore', 'g', 'get', 'gets', 'getting',
		'given', 'gives', 'go', 'goes', 'going', 'gone', 'got', 'gotten', 'greetings', 'h', 'had', 'hadn\'t', 'happens', 'hardly',
		'has', 'hasn\'t', 'have', 'haven\'t', 'having', 'he', 'he\'s', 'hello', 'help', 'hence', 'her', 'here', 'here\'s', 'hereafter',
		'hereby', 'herein', 'hereupon', 'hers', 'herself', 'hi', 'him', 'himself', 'his', 'hither', 'hopefully', 'how', 'howbeit',
		'however', 'i', 'i\'d', 'i\'ll', 'i\'m', 'i\'ve', 'ie', 'if', 'ignored', 'immediate', 'in', 'inasmuch', 'inc', 'indeed',
		'indicate', 'indicated', 'indicates', 'inner', 'insofar', 'instead', 'into', 'inward', 'is', 'isn\'t', 'it', 'it\'d',
		'it\'ll', 'it\'s', 'its', 'itself', 'j', 'just', 'k', 'keep', 'keeps', 'kept', 'know', 'knows', 'known', 'l', 'last',
		'lately', 'later', 'latter', 'latterly', 'least', 'less', 'lest', 'let', 'let\'s', 'like', 'liked', 'likely', 'little',
		'look', 'looking', 'looks', 'ltd', 'm', 'mainly', 'many', 'may', 'maybe', 'me', 'mean', 'meanwhile', 'merely', 'might',
		'more', 'moreover', 'most', 'mostly', 'much', 'must', 'my', 'myself', 'n', 'name', 'namely', 'nd', 'near', 'nearly',
		'necessary', 'need', 'needs', 'neither', 'never', 'nevertheless', 'new', 'next', 'nine', 'no', 'nobody', 'non', 'none',
		'noone', 'nor', 'normally', 'not', 'nothing', 'novel', 'now', 'nowhere', 'o', 'obviously', 'of', 'off', 'often', 'oh',
		'ok', 'okay', 'old', 'on', 'once', 'one', 'ones', 'only', 'onto', 'or', 'other', 'others', 'otherwise', 'ought', 'our',
		'ours', 'ourselves', 'out', 'outside', 'over', 'overall', 'own', 'p', 'particular', 'particularly', 'per', 'perhaps',
		'placed', 'please', 'plus', 'possible', 'presumably', 'probably', 'provides', 'q', 'que', 'quite', 'qv', 'r', 'rather',
		'rd', 're', 'really', 'reasonably', 'regarding', 'regardless', 'regards', 'relatively', 'respectively', 'right', 's',
		'said', 'same', 'saw', 'say', 'saying', 'says', 'second', 'secondly', 'see', 'seeing', 'seem', 'seemed', 'seeming',
		'seems', 'seen', 'self', 'selves', 'sensible', 'sent', 'serious', 'seriously', 'seven', 'several', 'shall', 'she',
		'should', 'shouldn\'t', 'since', 'six', 'so', 'some', 'somebody', 'somehow', 'someone', 'something', 'sometime',
		'sometimes', 'somewhat', 'somewhere', 'soon', 'sorry', 'specified', 'specify', 'specifying', 'still', 'sub', 'such',
		'sup', 'sure', 't', 't\'s', 'take', 'taken', 'tell', 'tends', 'th', 'than', 'thank', 'thanks', 'thanx', 'that', 'that\'s',
		'thats', 'the', 'their', 'theirs', 'them', 'themselves', 'then', 'thence', 'there', 'there\'s', 'thereafter', 'thereby',
		'therefore', 'therein', 'theres', 'thereupon', 'these', 'they', 'they\'d', 'they\'ll', 'they\'re', 'they\'ve', 'think',
		'third', 'this', 'thorough', 'thoroughly', 'those', 'though', 'three', 'through', 'throughout', 'thru', 'thus', 'to',
		'together', 'too', 'took', 'toward', 'towards', 'tried', 'tries', 'truly', 'try', 'trying', 'twice', 'two', 'u', 'un',
		'under', 'unfortunately', 'unless', 'unlikely', 'until', 'unto', 'up', 'upon', 'us', '	use', '		used', 'useful',
		'uses', 'using', 'usually', 'uucp', 'v', 'value', 'various', 'very', 'via', 'viz', 'vs', 'w', 'want', 'wants', 'was',
		'wasn\'t', 'way', 'we', 'we\'d', 'we\'ll', 'we\'re', 'we\'ve', 'welcome', 'well', 'went', 'were', 'weren\'t', 'what',
		'what\'s', 'whatever', 'when', 'whence', 'whenever', 'where', 'where\'s', 'whereafter', 'whereas', 'whereby', 'wherein',
		'whereupon', 'wherever', 'whether', 'which', 'while', 'whither', 'who', 'who\'s', 'whoever', 'whole', 'whom', 'whose',
		'why', 'will', 'willing', 'wish', 'with', 'within', 'without', 'won\'t', 'wonder', 'would', 'would', 'wouldn\'t', 'x',
		'y', 'yes', 'yet', 'you', 'you\'d', 'you\'ll', 'you\'re', 'you\'ve', 'your', 'yours', 'yourself', 'yourselves', 'z', 'zero'
	);

	const GENERAL_SCORE = 120;
	const TITLE_OBJECT_SCORE = 30;
	protected $extended = null;
	protected $fbClient;

	public function __construct() {
		$this->fbClient = new FreebaseClient();
	}

	public function get( $title, $getObject = true ) {

		$title = Title::newFromText( $title, NS_FILE );
		$file = wfFindFile($title);
		if( !$file ) return false;
		$meta = $file->getMetadata();
		$resultMeta = array();
		if( $meta ) {
			$meta= unserialize($meta);
			if ( isset($meta['keywords']) )
				$resultMeta['keywords'] = $meta['keywords'];
			if ( isset($meta['tags']) )
				$resultMeta['tags'] = $meta['tags'];
			if ( isset($meta['description']) )
				$resultMeta['description'] = $meta['description'];
			if ( isset($meta['category']) )
				$resultMeta['category'] = $meta['category'];
			if ( isset($meta['title']) )
				$resultMeta['title'] = $meta['title'];
			if ( isset($meta['actors']) ) {
				$resultMeta['actors'] = $meta['actors'];
			}
			if ( isset($meta['genres']) ) {
				$resultMeta['genres'] = $meta['genres'];
			}
			return ( $getObject === true ) ?  new VideoInformation( $resultMeta ) : $resultMeta;
		} else {
			return false;
		}
	}

	public function getExpanded( $title, $metadata=null ) {
		$this->extended = null;

		if ( $metadata === null ) {
			$metadata = $this->getMetadata( $title );
		}

		$exclude = $this->getMostUsedTerms();

		$this->getExpandedFormTitleMatches( $metadata );
		$this->getExpandedFromTitle( $metadata, $exclude );
		$this->getExpandedMetadata( $metadata, $exclude );

		$metadata[ 'expanded' ] = $this->extended;
		return $metadata;
	}

	protected function getMetadata( $title ) {
		$metadata = $this->get( $title, false );
		if ( empty( $metadata ) ) {
			return false;
		}

		$keywords =  isset( $metadata[ 'keywords' ] ) ? explode( ',', $metadata[ 'keywords' ] ) : array();
		$tags =  isset( $metadata[ 'tags' ] ) ? explode( ',', $metadata[ 'tags' ] ) : array();

		//join keywords and tags
		$joinedKeywords = array_merge( $keywords, $tags );
		//trim all keywords, just in case
		foreach ( $joinedKeywords as $key => $word ) {
			$joinedKeywords[ $key ] = trim( $word );
		}
		$metadata[ 'joinedKeywords' ] = $joinedKeywords;
		return $metadata;
	}

	protected function getMostUsedTerms() {
		//filter out common tags
		$dbr = wfGetDB( DB_SLAVE, array(), F::app()->wg->ExternalDatawareDB );
		$query = 'select * from vid_term_freq f, vid_term t where f.term_id = t.id order by count desc limit 100;';
		$dbResult = $dbr->query( $query, __METHOD__ );
		//get all forbidden words
		$exludeTerms = array();
		while ($row = $dbResult->fetchObject()) {
			$exludeTerms[] = $row->term;
		}
		return $exludeTerms;
	}

	public function getExpandedFromTitle( $metadata, $exludeTerms = array() ) {
		$normTitle = str_replace( '_', ' ', $metadata[ 'title' ] );
		$normTitle = preg_replace( "|\(\d{4}\)|", '-', strtolower( $normTitle ) );
		//czary mary dla titla - wiecej coli
		$normTitle = str_replace( $exludeTerms, '-', strtolower( $normTitle ) );
		foreach ( $this->stopWords as $word ) {
			$normTitle = preg_replace( "| {$word} |", '-', strtolower( $normTitle ) );
		}

		$toCheck = explode( '-', $normTitle );
		//filter out empty fields using anonymous function
		$toCheck = array_filter( $toCheck, function ( $element ) {
			$trimmed = trim( $element );
			return ( !empty( $trimmed ) ) ? true : false;
		} );

		foreach( $toCheck as $checkKeyword ) {
			$checkResult = $this->fbClient->query( $checkKeyword );
			$validated = $this->validateResult( $checkResult, $checkKeyword );
			$this->addToExtended( $validated );

			//do addtonal checks if keyword is longer than 2
			if ( count( $words = str_word_count( $checkKeyword, 1 ) ) > 2 ) {
				for ( $i = 0; $i < count( $words ) - 1; $i++ ) {
					$str = implode( ' ', array_slice( $words, $i, 2 ) );
					$checkResult = $this->fbClient->query( $str );
					$validated = $this->validateResult( $checkResult, $checkKeyword );
					$this->addToExtended( $validated );
				}
			}
		}
		return $this->extended;
	}

	public function getExpandedFormTitleMatches( $metadata ) {
		//try extracting video subject from title
		$normTitle = str_replace( '_', ' ', $metadata[ 'title' ] );

		preg_match( "|(?<text>.*)\((?<year>\d{4}\))|", $normTitle, $match );
		$checkForTitle = trim( $match[ 'text' ] );
		if ( !empty( $checkForTitle ) ) {
			$titleResult = $this->fbClient->query( $checkForTitle );
			$titleValidated = $this->validateResult( $titleResult, $checkForTitle, static::TITLE_OBJECT_SCORE );
			$this->addToExtended( $titleValidated );
		}
//		var_dump( $match[ 'text' ] );
		return $this->extended;
	}

	public function getExpandedMetadata( $metadata, $exludeTerms = array() ) {
		//ask freebase for types and titles
		foreach ( $metadata[ 'joinedKeywords' ] as $tag ) {
			if ( !in_array( $tag, $exludeTerms ) ) {
				$fbResult = $this->fbClient->query( $tag );

				$extended = $this->validateResult( $fbResult, $tag );
				$this->addToExtended( $extended );
			}
//			else {
//				var_dump( 'Excluded: '.$tag );
//			}
		}
		return $this->extended;
	}

	protected function validateResult( $freebaseResult, $keyword, $score = 0 ) {
		$score = ( $score > 0 ) ? $score : static::GENERAL_SCORE;
		$about = array();
		$matched = array();
		$fbClient = new FreebaseClient();
		if ( isset( $freebaseResult->result ) ) {
			foreach ( $freebaseResult->result as $res ) {
				if ( isset( $res->notable ) ) {
					$type = $fbClient->getTypeMapping( $res->notable->id );
					if ( $type !== null && $res->score > $score ) {
						$about[ $keyword ][] = array( 'title' => $res->name, 'type' => $type, 'score' => $res->score );
						continue;
					}
				}
				if ( trim( strtolower( $res->name ) ) === str_replace( '-', ' ', strtolower( $keyword ) ) ) {
					$type = ( isset( $res->notable ) ) ? $res->notable->id : '';
					$matched[ $keyword ][] = array( 'title' => $res->name, 'type' => $type, 'score' => $res->score );
				}
			}
		}
		//refine before returning
		if ( !empty( $about[ $keyword ] ) ) {
			$maxScore = array( 'score' => 0 );
			$exact = null;
			foreach( $about[ $keyword ] as $a ) {
				if ( trim( strtolower( $a[ 'title' ] ) ) === str_replace( '-', ' ', strtolower( $keyword ) ) ) {
					$exact = $a;
					break;
				}
				$maxScore = ( $maxScore[ 'score' ] < $a[ 'score' ] ) ? $a : $maxScore;
			}
			$about[ $keyword ] = ( $exact !== null ) ? $exact : $maxScore;
		}

		if ( !empty( $matched[ $keyword ] ) ) {
			$maxScore = array( 'score' => 0 );
			$exact = null;
			foreach( $matched[ $keyword ] as $element ) {
				if ( trim( strtolower( $element[ 'title' ] ) ) === str_replace( '-', ' ', strtolower( $keyword ) ) ) {
					$exact = $element;
					break;
				}
				$maxScore = ( $maxScore[ 'score' ] < $element[ 'score' ] ) ? $element : $maxScore;
			}
			$choosed = ( $exact !== null ) ? $exact : $maxScore;
			if  ( $choosed[ 'score' ] > $score ) {
				$matched[ $keyword ] = $choosed;
			} else {
				unset( $matched[ $keyword ] );
			}
		}

		return array( 'about' => $about, 'matched' => $matched );
	}

	protected function cmpValues( $valOne, $valTwo ) {
		$nOne = strtolower( trim( $valOne ) );
		$nTwo = strtolower( trim( $valTwo ) );

		if ( $nOne === $nTwo ) {
			return 0;
		}
		$one = strlen( $nOne );
		$two = strlen( $nTwo );

		if ( $one >= $two ) {
			if( strpos( $nOne, $nTwo ) !== false ) {
				return 1;
			}
		} else {
			if( strpos( $nTwo, $nOne ) !== false ) {
				return -1;
			}
		}
		return null;
	}

	protected function addToExtended( $additional ) {
		if ( $this->extended === null ) {
			$this->extended = $additional;
		} else {
			if ( is_array( $this->extended ) && is_array( $additional ) ) {
				$this->extended[ 'about' ] = array_merge( $additional[ 'about' ], $this->extended[ 'about' ] );
				$this->extended[ 'matched' ] = array_merge( $additional[ 'matched' ], $this->extended[ 'matched' ] );
			}
		}
		$tmp = null;
		foreach ( $this->extended[ 'about' ] as $key => $data ) {
			$tmp[ $key ] = md5( implode( '', $data ) );
		}
		if ( is_array( $tmp ) ) {
			$tmp = array_unique( $tmp );
			foreach ( $tmp as $key => $hash ) {
				$result[ $key ] = $this->extended[ 'about' ][ $key ];
			}
			$this->extended[ 'about' ] = $result;
		}
		$tmp = null;
		foreach ( $this->extended[ 'about' ] as $key => $data ) {
			$tmp[ $key ] = md5( implode( '', $data ) );
		}
		if ( is_array( $tmp ) ) {
			$tmp = array_unique( $tmp );
			foreach ( $tmp as $key => $hash ) {
				$result[ $key ] = $this->extended[ 'about' ][ $key ];
			}
			$this->extended[ 'about' ] = $result;
		}
	}

}
