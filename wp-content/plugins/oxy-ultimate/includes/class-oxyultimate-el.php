<?php

/**
 * OxyUltimateEl
 */
class OxyUltimateEl extends OxyEl
{
	
	function init()
	{
		$this->El->useAJAXControls();
	}

	function class_names() {
		return array('oxy-ultimate-element');
	}

	function button_place() {
		$button_place = $this->oxyu_button_place();
		if( $button_place )
			return "oxyultimate::" . $button_place;

		return "";
	}

	function button_priority() {
        return '';
    }

    function isBuilderEditorActive() {
		if( isset($_GET['oxygen_iframe']) || defined('OXY_ELEMENTS_API_AJAX') ) {
			return true;
		}

		return false;
    }
}