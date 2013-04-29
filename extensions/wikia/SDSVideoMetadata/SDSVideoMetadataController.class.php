<?php
/**
 * @author ADi
 * @author Jacek Jursza
 */

class SDSVideoMetadataController extends WikiaSpecialPageController {

	// Flash player width
	const VIDEO_WIDTH = 500;

	//Forms config
	protected $formsConfig = array(


		/* COMMON */

		'description' => array(
			'controller' => 'PandoraForms',
			'template' => 'default',
			'textarea' => true,
			'label' => 'sdsvideometadata-vc-description',
			'ormKey' => 'description'
		),
		'inLanguage' => array(
			'controller' => 'PandoraForms',
			'template' => 'default',
			'label' => 'sdsvideometadata-vc-language',
			'ormKey' => 'inLanguage'
		),
		'subTitleLanguage' => array(
			'controller' => 'PandoraForms',
			'template' => 'default',
			'label' => 'sdsvideometadata-vc-subtitles',
			'ormKey' => 'subTitleLanguage'
		),

		/* GAME, TV, MUSIC, MOVIE */

		'setting' => array(
			'controller' => 'PandoraForms',
			'template' => 'literal_list',
			'type' => 'VideoClipGamingVideo VideoClipMusicVideo VideoClipTVVideo VideoClipMovieTrailersVideo',
			'label' => 'sdsvideometadata-vc-setting',
			'ormKey' => 'setting'
		),

		'characters' => array(
			'controller' => 'PandoraForms',
			'template' => 'reference_list',
			'type' => 'VideoClipGamingVideo VideoClipMusicVideo VideoClipTVVideo VideoClipMovieTrailersVideo',
			'label' => 'sdsvideometadata-vc-characters',
			'ormKey' => 'characters',
			'suggestionsType' => 'character'
		),

		/* MUSIC, HOW TO, TRAVEL, CRAFT, COOKING */
		'celebrity' => array(
			'controller' => 'PandoraForms',
			'template' => 'reference_list',
			'type' => 'VideoClipMusicVideo VideoClipHowToVideo VideoClipTravelVideo VideoClipCookingVideo VideoClipCraftVideo',
			'label' => 'sdsvideometadata-vc-celebrity',
			'ormKey' => 'celebrity',
			'suggestionsType' => 'person'
		),


		/* TV, MOVIE */
		'actors' => array(
			'controller' => 'PandoraForms',
			'template' => 'reference_list',
			'type' => 'VideoClipTVVideo VideoClipMovieTrailersVideo',
			'label' => 'sdsvideometadata-vc-actors',
			'ormKey' => 'actors',
			'suggestionsType' => 'actor'
		),


		/* GAME, TV */

		'keywords' => array(
			'controller' => 'PandoraForms',
			'template' => 'literal_list',
			'type' => 'VideoClipGamingVideo VideoClipTVVideo VideoClipMovieTrailersVideo',
			'label' => 'sdsvideometadata-vc-kind',
			'ormKey' => 'keywords'
		),


		/* GAME VIDEOS */

		'game' => array(
			'controller' => 'PandoraForms',
			'template' => 'reference_list',
			'type' => 'VideoClipGamingVideo',
			'label' => 'sdsvideometadata-vc-game',
			'ormKey' => 'game',
			'suggestionsType' => 'game'
		),

		'soundtrack' => array(
			'controller' => 'PandoraForms',
			'template' => 'reference_list',
			'type' => 'VideoClipGamingVideo',
			'label' => 'sdsvideometadata-vc-soundtrack',
			'ormKey' => 'soundtrack',
			'suggestionsType' => 'music_recording'
		),

		/* TV VIDEOS */

		'tvSeries' => array(
			'controller' => 'PandoraForms',
			'template' => 'reference_list',
			'type' => 'VideoClipTVVideo',
			'label' => 'sdsvideometadata-vc-series',
			'ormKey' => 'tvSeries',
			'suggestionsType' => 'tv_series'
		),

		'tvSeason' => array(
			'controller' => 'PandoraForms',
			'template' => 'reference_list',
			'type' => 'VideoClipTVVideo',
			'label' => 'sdsvideometadata-vc-season',
			'ormKey' => 'tvSeason',
			'suggestionsType' => 'tv_season'
		),

		/* MOVIES */

		'movie' => array(
			'controller' => 'PandoraForms',
			'template' => 'reference_list',
			'type' => 'VideoClipMovieTrailersVideo',
			'label' => 'sdsvideometadata-vc-movie',
			'ormKey' => 'movie',
			'suggestionsType' => 'movie'
		),

		/* MUSIC */
		'musicRecording' => array(
			'controller' => 'PandoraForms',
			'template' => 'reference_list',
			'type' => 'VideoClipMusicVideo',
			'label' => 'sdsvideometadata-vc-song',
			'ormKey' => 'musicRecording',
			'suggestionsType' => 'music_recording'
		),

		/* TRAVEL */

		'contentLocation' => array(
			'controller' => 'PandoraForms',
			'template' => 'reference_list',
			'type' => 'VideoClipTravelVideo',
			'label' => 'sdsvideometadata-vc-location',
			'ormKey' => 'contentLocation',
			'suggestionsType' => 'place'
		),

		/* COOKING */

		'recipe' => array(
			'controller' => 'PandoraForms',
			'template' => 'reference_list',
			'type' => 'VideoClipCookingVideo',
			'label' => 'sdsvideometadata-vc-location',
			'ormKey' => 'recipe',
			'suggestionsType' => 'recipe'
		),


	);

	public function __construct() {
		parent::__construct('VMD');
	}

	public function indexTest() {

		$file = $this->getVal('video');

		$formBuilder = new PandoraForms( $file );
		$this->setVal( 'form', $formBuilder );

		if($this->request->wasPosted()) {

			$requestParams = $this->getRequest()->getParams();
			$formBuilder->save( $requestParams );

		}

	}

	public function index() {

		$this->response->addAsset('extensions/wikia/SDSVideoMetadata/css/VideoMetadata.scss');
		$this->response->addAsset('resources/wikia/libraries/mustache/mustache.js');
		$this->response->addAsset('extensions/wikia/SDSPandora/js/modules/pandora.js');
		$this->response->addAsset('extensions/wikia/SDSVideoMetadata/js/VideoMetadata.js');
		$file = $this->getVal('video');

		$fileTitle = Title::newFromText( $file );
		$fileObject = wfFindFile( $fileTitle );

		if ( empty( $fileObject ) || !WikiaFileHelper::isFileTypeVideo( $fileObject ) ) {
			$this->setVal( 'isCorrectFile', false );
			return false;
		} else {

			//prepare pandoraForms
			$forms = new PandoraForms();
			$forms->setConfig( $this->formsConfig );

			$pandoraVideoId = Pandora::pandoraIdFromArticleId( $fileTitle->getArticleID() );

			$videoEmbedCode = $fileObject->getEmbedCode( self::VIDEO_WIDTH );
			$this->setVal( 'embedCode', $videoEmbedCode );

			$orm = PandoraORM::buildFromField( $pandoraVideoId, 'schema:additionalType', 'VideoObject' );
			if ( $orm->exist ) {
				$forms->loadFromORM( $orm );
				$mapper[ 'vcType' ] = get_class( $orm );
				$this->setVal( 'vcObj', $mapper );
			}

			if($this->request->wasPosted()) {
				$this->setVal( 'wasPasted', true );
				$isCompleted = (bool) $this->request->getVal('vcCompleted', false);
				$this->setFileCompleted( $fileTitle, $isCompleted );

				$requestParams = $this->getRequest()->getParams();

				$saveOrm = $forms->getOrmFromRequest( $requestParams, $pandoraVideoId );
				$saveOrm->set( 'name', $fileTitle->getBaseText() );
				$saveOrm->set( 'content_url', $fileTitle->getFullUrl() );
				//sets filed with key = 'additional_type' as default (using config 'value' filed)
				$saveOrm->set( 'additional_type' );

				$result = $saveOrm->save();

				$errorMessage = '';

				if ( !$result->isOK() ) {
					$errorMessage =  $result->getMessage();
				} else {
					//TODO: redirect
//					$specialPageUrl = SpecialPage::getTitleFor( 'VMD' )->getFullUrl() . '?video='.urlencode( $fileTitle->getPrefixedDBkey() );
//					$this->wg->out->redirect( $specialPageUrl );
//						$this->setVal( 'success', true );
				}

				$this->setVal( 'errorMessage', $errorMessage );
			}

			$this->setVal( 'isCorrectFile', true );
			$this->setVal( 'isCompleted', $this->getFileCompleted( $fileTitle ) );
			$this->setVal( 'formBuilder', $forms );
		}

		$this->setVal('file', $fileTitle->getBaseText());
	}

	/**
	 * set "completed" flag for given file
	 *
	 * @todo move this to model class when it will be ready
	 *
	 * @param Title $fileTitle
	 * @param bool $isCompleted
	 */
	private function setFileCompleted(Title $fileTitle, $isCompleted = true) {
		wfSetWikiaPageProp( WPP_VIDEO_METADATA_COMPLETED, $fileTitle->getArticleID(), $isCompleted );
	}

	/**
	 * get "completed" flag for given file
	 *
	 * @todo move this to model class when it will be ready
	 *
	 * @param Title $fileTitle
	 */
	private function getFileCompleted(Title $fileTitle) {
		return wfGetWikiaPageProp( WPP_VIDEO_METADATA_COMPLETED, $fileTitle->getArticleID() );
	}

}
