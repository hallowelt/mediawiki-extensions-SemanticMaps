<?php

/**
* Form input hook that adds an Yahoo! Maps map format to Semantic Forms
 *
 * @file SM_YahooMapsFormInput.php
 * @ingroup SMYahooMaps
 * 
 * @author Jeroen De Dauw
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

final class SMYahooMapsFormInput extends SMFormInput {
	
	public $serviceName = MapsYahooMaps::SERVICE_NAME;
	
	protected $spesificParameters = array();
	
	/**
	 * @see MapsMapFeature::setMapSettings()
	 */
	protected function setMapSettings() {
		global $egMapsYahooMapsZoom, $egMapsYahooMapsPrefix;
		
		$this->elementNamePrefix = $egMapsYahooMapsPrefix;
		$this->showAddresFunction = 'showYAddress';

		$this->earthZoom = 17;

        $this->defaultZoom = $egMapsYahooMapsZoom;
	}
	
	/**
	 * @see MapsMapFeature::addFormDependencies()
	 */
	protected function addFormDependencies() {
		global $wgOut, $wgParser;
		global $smgScriptPath, $smgYahooFormsOnThisPage, $smgStyleVersion, $egMapsJsExt;
		
		MapsYahooMaps::addYMapDependencies( $wgParser );
		
		if ( empty( $smgYahooFormsOnThisPage ) ) {
			$smgYahooFormsOnThisPage = 0;
			
			$wgOut->addScriptFile( "$smgScriptPath/Services/YahooMaps/SM_YahooMapsFunctions{$egMapsJsExt}?$smgStyleVersion" );
		}
	}
	
	/**
	 * @see MapsMapFeature::doMapServiceLoad()
	 */
	protected function doMapServiceLoad() {
		global $egYahooMapsOnThisPage, $smgYahooFormsOnThisPage, $egMapsYahooMapsPrefix;
		
		self::addFormDependencies();
		
		$egYahooMapsOnThisPage++;
		$smgYahooFormsOnThisPage++;
		
		$this->elementNr = $egYahooMapsOnThisPage;
		$this->mapName = $egMapsYahooMapsPrefix . '_' . $egYahooMapsOnThisPage;
	}
	
	/**
	 * @see MapsMapFeature::addSpecificMapHTML()
	 */
	protected function addSpecificMapHTML() {
		global $wgOut;
		
		$this->output .= Html::element(
			'div',
			array(
				'id' => $this->mapName,
				'style' => "width: $this->width; height: $this->height; background-color: #cccccc;",
			),
			wfMsg( 'maps-loading-map' )
		);
		
		$wgOut->addInlineScript( <<<EOT
addOnloadHook(
	function() {
		makeFormInputYahooMap(
			'$this->mapName',
			'$this->coordsFieldName',
			$this->centreLat,
			$this->centreLon,
			$this->zoom,
			$this->type,
			[$this->types],
			[$this->controls],
			$this->autozoom,
			{$this->markerCoords['lat']},
			{$this->markerCoords['lon']}
		);
	}
);
EOT
		);

	}
	
	/**
	 * @see SMFormInput::manageGeocoding()
	 *
	 */
	protected function manageGeocoding() {
		global $egYahooMapsKey;
		$this->enableGeocoding = strlen( trim( $egYahooMapsKey ) ) > 0;
		if ( $this->enableGeocoding ) MapsYahooMaps::addYMapDependencies( $this->output );
	}

	
}