<?php

namespace Oxygen\OxyUltimate;

Class OUImageAccordion extends \OxyUltimateEl {
	public $css_added = false;
	public $js_added = false;

	function name() {
		return __( "Image Accordion", 'oxy-ultimate' );
	}

	function slug() {
		return "ou_imgpanel";
	}

	function oxyu_button_place() {
		return "images";
	}

	function icon() {
		return OXYU_URL . 'assets/images/elements/ou-image-panels.svg';
	}

	function init() {
		$this->El->useAJAXControls();
		$this->enableNesting();
		add_action("ct_toolbar_component_settings", function() {
		?>
			<label class="oxygen-control-label oxy-ou_imgpanel-elements-label"
				ng-if="isActiveName('oxy-ou_imgpanel')&&!hasOpenTabs('oxy-ou_imgpanel')" style="text-align: center; margin-top: 15px;">
				<?php _e("Available Elements","oxy-ultimate"); ?>
			</label>
			<div class="oxygen-control-row oxy-ou_imgpanel-elements"
				ng-if="isActiveName('oxy-ou_imgpanel')&&!hasOpenTabs('oxy-ou_imgpanel')">
				<?php do_action("oxygen_add_plus_ouimgpanel_comp"); ?>
			</div>
		<?php }, 22 );
	}

	function controls() {

		$preview = $this->El->addControl( 'buttons-list', 'ouimgp_preview', __('Preview') );
		$preview->setValue( array( "Enable", "Disable" ) );
		$preview->setValueCSS( array(
			"Disable" 	=> ".builder-peview .oxy-ou-panel-item:hover{flex: 1;transform: none;}
							.builder-peview .ou-panel-caption,
							.builder-peview .oxy-ou-panel-item:hover .ou-panel-caption,
							.builder-peview .oupa-caption-bg,
							.builder-peview .oxy-ou-panel-item:hover .oupa-caption-bg {
								transform:scale(1,1);
								-webkit-transform:scale(1,1);
								-moz-transform:scale(1,1);
							}"
		));
		$preview->setDefaultValue("Disable");
		$preview->whiteList();


		/*****************************
		 * Settings
		 ****************************/
		$height = $this->addStyleControl([
			'selector' 		=> '.ou-image-panel-wrap',
			'property' 		=> 'height',
			'control_type' 	=> 'slider-measurebox'
		]);

		$height->setRange( '0', '1000', '10');
		$height->setValue( '450' );
		$height->setUnits( 'px', 'px' );

		$layout = $this->El->addControl( 'buttons-list', 'ouimgp_layout', __('Layout') );
		$layout->setValue( array( "Vertical", "Horizontal" ) );
		$layout->setValueCSS( array(
			"Horizontal" 	=> ".ou-image-panel-wrap{flex-direction: column;} .oxy-ou-panel-item{width: 100%;}"
		));
		$layout->setDefaultValue("Vertical");
		$layout->whiteList();

		$this->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> 'Display Horizontally on Mobile',
			'slug' 		=> 'ouip_rp',
			'value' 	=> [ 'yes' => __( "Yes" ), "no" => __( "No" ) ],
			'default' 	=> 'yes',
			'condition' => 'ouimgp_layout=Vertical'
		]);


		/***************************
		 * Image Hover Animation
		 **************************/
		$hvAnim = $this->addControlSection( 'hover_animation', __('Hover Animation', "oxy-ultimate"), 'assets/icon.png', $this );

		$flexGrow = $hvAnim->addStyleControl([
			'selector' 		=> '.oxy-ou-panel-item:hover',
			'property' 		=> 'flex-grow',
			'control_type' 	=> 'slider-measurebox'
		]);
		$flexGrow->setRange( '2', '10', '1');
		$flexGrow->setValue( '4' );
		$flexGrow->setUnits( ' ', ' ');

		$td = $hvAnim->addStyleControl([
			'selector' 		=> '.oxy-ou-panel-item',
			'property' 		=> 'transition-duration',
			'control_type' 	=> 'slider-measurebox'
		]);
		$td->setRange( '0', '10', '0.1');
		$td->setValue( '0.8' );
		$td->setUnits( 's', 'sec');

		$hvScale = $hvAnim->addControlSection( 'hover_sacle', __('Scale', "oxy-ultimate"), 'assets/icon.png', $this );

		$scaleX = $hvScale->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Scale X', "oxy-ultimate"),
			'slug' 		=> 'ouip_scaleX'
		]);

		$scaleX->setUnits(' ',' ');
		$scaleX->setRange('1', '10', '1');

		$scaleY = $hvScale->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Scale Y', "oxy-ultimate"),
			'slug' 		=> 'ouip_scaleY'
		]);

		$scaleY->setUnits(' ',' ');
		$scaleY->setRange('1', '10', '1');

		$scaleZ = $hvScale->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Scale Z', "oxy-ultimate"),
			'slug' 		=> 'ouip_scaleZ'
		]);

		$scaleZ->setUnits(' ',' ');
		$scaleZ->setRange('1', '10', '1');
		
		$hvAnim->boxShadowSection( __('Box Shadow'), '.oxy-ou-panel-item:hover', $this );
	}

	function render( $options, $defaults, $content) {
		$builderPreview = '';

		if (defined('OXY_ELEMENTS_API_AJAX') && OXY_ELEMENTS_API_AJAX) {
			$builderPreview = ' builder-peview';
		}

		echo '<div class="ou-image-panel-wrap oxy-inner-content'.$builderPreview .'">';
		
		if( $content ) {
			if( function_exists('do_oxygen_elements') )
				echo do_oxygen_elements( $content );
			else
				echo do_shortcode( $content );
		}

		echo '</div>';

		if( ! $this->js_added ) {
			$this->El->footerJS("
				jQuery(document).ready(function($){
					$('.oxy-ou-panel-item').bind('touchstart touchmove', function(event) {
						event.stopPropagation();						
					});
				});"
			);
			$this->js_added = true;
		}
	}

	function customCSS( $original, $selector ) {
		$css =  $transform = '';
		$prefix = $this->El->get_tag();

		// Scale
		if ( isset($original[$prefix . '_ouip_scaleX']) && isset($original[$prefix . '_ouip_scaleY']) && isset($original[$prefix . '_ouip_scaleZ']) &&
			$original[$prefix . '_ouip_scaleX'] !== "" && $original[$prefix . '_ouip_scaleY'] !== "" && $original[$prefix . '_ouip_scaleZ'] !== "" ) {
			$transform .= "scale3d(" 
					. $original[$prefix . '_ouip_scaleX'] . "," 
					. $original[$prefix . '_ouip_scaleY'] . ","
					. $original[$prefix . '_ouip_scaleZ'] . ")";
		}
		else if (isset($original[$prefix . '_ouip_scaleX']) && isset($original[$prefix . '_ouip_scaleY']) &&
				 $original[$prefix . '_ouip_scaleX'] !== "" && $original[$prefix . '_ouip_scaleY'] !== "") {
			$transform .= "scale(" 
				. $original[$prefix . '_ouip_scaleX'] . "," 
				. $original[$prefix . '_ouip_scaleY'] . ")";
		}
		else {

			if (isset($original[$prefix . '_ouip_scaleX']) &&
					 $original[$prefix . '_ouip_scaleX'] !== "") {
				$transform .= " scaleX(" . $original[$prefix . '_ouip_scaleX'] . ")";
			}

			if (isset($original[$prefix . '_ouip_scaleY']) &&
					 $original[$prefix . '_ouip_scaleY'] !== "") {
				$transform .= " scaleY(" . $original[$prefix . '_ouip_scaleY'] . ")";
			}

			if (isset($original[$prefix . '_ouip_scaleZ']) &&
					 $original[$prefix . '_ouip_scaleZ'] !== "") {
				$transform .= " scaleZ(" . $original[$prefix . '_ouip_scaleZ'] . ")";
			}
		}

		if( ! empty( $transform )) {
			$css .= $selector .' .oxy-ou-panel-item:hover{transform:' . $transform . ';}';
		}

		if( $this->css_added == false ) {
			$css .= '.oxy-ou-imgpanel{display: flex; flex-direction: row; width: 100%; position: relative;}';
			$css .= '.ou-image-panel-wrap{display: flex; flex-direction: row; flex-wrap: nowrap; align-items: flex-start; width: 100%; height: 450px; position: relative;}';

			$this->css_added = true;
		}

		if( isset( $original[$prefix . '_ouip_rp'] ) && $original[$prefix . '_ouip_rp'] == "yes" ) {
			$css .= '@media only screen and (max-width: 768px){ '. $selector .' .ou-image-panel-wrap{flex-direction: column;} '. $selector .' .oxy-ou-panel-item{width: 100%;} }';
		}

		return $css;
	}

	function enablePresets() {
		return true;
	}

	function enableFullPresets() {
		return true;
	}
}

new OUImageAccordion();

include_once OXYU_DIR . 'elements/ou-image-panels/ou-panel-item.php';