<?php

require_once( "LanguageUtf8.php" );

/* private */ $wgWeekdayNamesEl = array(
	"Κυριακή", "Δευτέρα", "Τρίτη", "Τετάρτη", "Πέμπτη",
	"Παρασκευή", "Σαββάτο"
);

/* private */ $wgMonthNamesEl = array(
	"Ιανουαρίου", "Φεβρουαρίου", "Μαρτίου", "Απριλίου", "Μαΐου", "Ιουνίου",
	"Ιουλίου", "Αυγούστου", "Σεπτεμβρίου", "Οκτωβρίου", "Νοεμβρίου",
	"Δεκεμβρίου"
);

/* private */ $wgMonthAbbreviationsEl = array(
	"Ιαν". "Φεβρ", "Μαρτ", "Απρ", "Μαΐου", "Ιουν", "Ιουλ",
	"Αυγ", "Σεπτ", "Οκτ", "Νοεμβ", "Δεκ"
);

class LanguageEl extends LanguageUtf8 {
	function fallback8bitEncoding() {
		return "windows-1253";
	}

	function getMonthName( $key )
	{
		global $wgMonthNamesEl;
		return $wgMonthNamesEl[$key-1];
	}

	function getMonthAbbreviation( $key )
	{
		global $wgMonthAbbreviationsEl;
		return $wgMonthAbbreviationsEl[$key-1];
	}

	function getWeekdayName( $key )
	{
		global $wgWeekdayNamesEl;
		return $wgWeekdayNamesEl[$key-1];
	}

}

?>
