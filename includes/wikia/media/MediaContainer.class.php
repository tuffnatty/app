<?php

/**
 * Class MediaContainer
 */
class MediaContainer {

	/** @var User */
	protected $user;

	/** @var File */
	protected $file;

	/** @var Title */
	protected $title;

	/** @var string */
	protected $description;

	/**
	 * @param array $param
	 * @throws Exception
	 */
	public function __construct( array $param ) {
		if ( !empty( $param['title'] ) ) {
			$this->title = $param['title'];
		} elseif ( !empty( $param['file'] )) {
			$this->file = $param['file'];
		} else {
			throw new Exception( "Missing file or title parameter" );
		}

		if ( !empty( $param['premium'] ) ) {
			$this->isPremium = $param['premium'];
		}
	}

	/**
	 * @param string $key
	 * @param array $options
	 * @return MediaContainer
	 */
	public static function newFromText( $key, array $options = null ) {
		$title = Title::newFromText( $key, NS_FILE );

		$options['title'] = $title;

		$media = new static( $options );
		return $media;
	}

	/**
	 * @param Title $title
	 * @param array $options
	 * @return MediaContainer
	 */
	public static function newFromTitle( Title $title, array $options = null ) {
		$options['title'] = $title;

		$media = new static( $options );
		return $media;
	}

	/**
	 * @param string $url
	 * @param array $options
	 */
	public static function newFromURL( $url, array $options = null ) {
		// Look to see if its part of the video wiki, then mark it as such so that we get details from there
	}

	/**
	 * @param File $file
	 * @param array $options
	 * @return MediaContainer
	 */
	public function newFromFile( File $file, array $options = null ) {
		$options['file'] = $file;

		$media = new static( $options );
		return $media;
	}

	/**
	 * @return File
	 */
	public function getFile() {
		if ( $this->file == null ) {
			$this->file = wfFindFile( $this->title );
		}

		return $this->file;
	}

	/**
	 * @return Title
	 */
	public function getTitle() {
		if ( $this->title == null ) {
			$this->title = $this->file->getTitle();
		}

		return $this->title;
	}

	/**
	 * @return String
	 */
	public function getDBKey() {
		return $this->getTitle()->getDBkey();
	}

	/**
	 * @return String
	 */
	public function getText() {
		return $this->getTitle()->getText();
	}

	/**
	 * Get description, which is the content of the file page minus the category wiki tags
	 *
	 * @param array $options An array of options.  Keys are:
	 *     [
	 *         'fillFromMeta' : Boolean, whether to use the description found in metadata
	 *     ]
	 * @return string $text
	 */
	public function getDescription( array $options = null ) {
		if ( $this->description != null ) {
			return $this->description;
		}

		wfProfileIn( __METHOD__ );

		// Default to true for the fillFromMeta option
		if ( empty( $options['fillFromMeta'] ) ) {
			$fillFromMeta = true;
		} else {
			$fillFromMeta = $options['fillFromMeta'];
		}

		// Get the file page for this file
		$page = WikiPage::factory( $this->getTitle() );

		// Strip the description header
		$text = $this->stripDescriptionHeader( $page->getText() );

		// Strip out the category tags so they aren't shown to the user
		$text = FilePageHelper::stripCategoriesFromDescription( $text );

		// If we have an empty string or a bunch of whitespace, and we're asked to do so,
		// use the default description from the file metadata
		if ( $fillFromMeta && ( trim( $text ) == '' ) ) {
			$text = $this->file->getMetaDescription();
		}

		wfProfileOut( __METHOD__ );

		return $text;
	}

	/**
	 * @param $content
	 * @return mixed|string
	 */
	public function stripDescriptionHeader( $content ) {
		wfProfileIn( __METHOD__ );

		$headerText = wfMessage( 'videohandler-description' );

		// Grab everything after the description header
		preg_match("/^==\s*$headerText\s*==\n*(.+)/sim", $content, $matches);

		$newContent = '';
		if ( !empty($matches[1]) ) {
			// Get rid of any H2 headings after the description
			$newContent = preg_replace('/^==[^=]+==.*/sm', '', $matches[1]);
		}

		wfProfileOut( __METHOD__ );

		return $newContent;
	}

	/**
	 * @return String
	 */
	public function getFileUrl() {
		return $this->title->getFullURL();
	}

	/**
	 * @param $width
	 * @param $height
	 * @return string
	 */
	public function getThumbUrl( $width, $height ) {
		$thumb = $this->getFile()->transform( [ 'width' => $width, 'height' => $height ] );
		$thumbUrl = $thumb->getUrl();

		return $thumbUrl;
	}

	/**
	 * @param $width
	 * @param $height
	 * @return string
	 */
	public function getThumbnail( $width, $height ) {
		$thumb = $this->getFile()->transform( [ 'width' => $width, 'height' => $height ] );
		$thumbNail = $thumb->toHtml( [
			'useTemplate' => true,
			'fluid' => true,
			'forceSize' => 'small',
		] );
		return $thumbNail;
	}

	/**
	 * @return User
	 */
	public function getUser() {
		if ( $this->user == null ) {
			$userId = $this->getUserID();
			$this->user = User::newFromId( $userId );
		}

		return $this->user;
	}

	/**
	 * @return int|string
	 */
	public function getUserID() {
		// The file object does not return an actual user, just the ID or Name
		return $this->getFile()->getUser( 'id' );
	}

	/**
	 * @return String
	 */
	public function getUserName() {
		$user = $this->getUser();
		$userName = ( User::isIP($user->getName()) ) ? wfMessage( 'oasis-anon-user' )->text() : $user->getName();
		return $userName;

	}

	/**
	 * @return String
	 */
	public function getUserUrl() {
		return $this->getUser()->getUserPage()->getFullURL();
	}

	/**
	 * @return false|Mixed|string
	 */
	public function getTimestamp() {
		$ts = $this->getFile()->getTimestamp();
		return $ts ? $ts : wfTimestamp( TS_MW );
	}

	/**
	 * Get the list of articles this media file appears in
	 *
	 * @return array|Mixed
	 */
	public function getAppearsInArticles() {
		$mediaQuery = new ArticlesUsingMediaQuery( $this->getTitle() );
		$articles = $mediaQuery->getArticleList();

		// Trim off the URL decoration on comments
		if ( !empty( $articles ) ) {
			foreach ( $articles as $article ) {
				$article['titleText'] = preg_replace( '/\/@comment-.*/', '', $article['titleText'] );
			}
		}

		return $articles;
	}
}