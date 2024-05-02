<?php

namespace Oxygen\OxyUltimate;

Class OUCSSGridItem extends \OxyUltimateEl {
	public $css_added = false;

	function name() {
		return __( "Grid Item", 'oxy-ultimate' );
	}

	function slug() {
		return "ou_css_grid_item";
	}

	function button_place() {
		return "oucssgrid::comp";
	}

	function icon() {
		return CT_FW_URI . '/toolbar/UI/oxygen-icons/add-icons/columns.svg';
	}

	function init() {
		$this->El->useAJAXControls();
		$this->enableNesting();
		add_action("ct_toolbar_component_settings", function() {
		?>
			<label class="oxygen-control-label oxy-ou_css_grid_item-elements-label"
				ng-if="isActiveName('oxy-ou_css_grid_item')&&!hasOpenTabs('oxy-ou_css_grid_item')" style="text-align: center; margin-top: 15px;">
				<?php _e("Available Elements","oxy-ultimate"); ?>
			</label>
			<div class="oxygen-control-row oxy-ou_css_grid_item-elements"
				ng-if="isActiveName('oxy-ou_css_grid_item')&&!hasOpenTabs('oxy-ou_css_grid_item')">
				<?php do_action("oxygen_add_plus_oucssgrid_comp"); ?>
			</div>
		<?php }, 15 );
	}

	function tag() {
		$tags = array('default' => 'div', 'choices' => 'div,article,aside,figure,footer,header,main,nav,section' );
		return $tags;
	}

	function controls() {

		$this->addCustomControl(
			__('<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 14px;">Click on <span style="color:#ff7171;">Apply Params</span> button at below, if contents are not displaying properly on Builder editor.</div>'), 
			'description'
		)->setParam('heading', 'Note:');

		$this->addTagControl();

		//* Columns
		$column = $this->addStyleControl([
			'control_type' 		=> 'textfield',
			'name' 				=> __('Grid Column Span'),
			'selector' 			=> ' ',
			'property' 			=> 'grid-column'
		]);
		$column->setParam('description','<a href="https://www.w3schools.com/css/css_grid_item.asp" target="_blank" style="color:rgba(255,255,255,0.5);text-decoration: none;">Click here to see the instruction</a>.');

		//* Rows
		$row = $this->addStyleControl([
			'control_type' 		=> 'textfield',
			'name' 				=> __('Grid Row Span'),
			'selector' 			=> ' ',
			'property' 			=> 'grid-row'
		]);
		$row->setParam('description','<a href="https://www.w3schools.com/css/css_grid_item.asp" target="_blank" style="color:rgba(255,255,255,0.5);text-decoration: none;">Click here to see the instruction</a>.');

		$layout_child = $this->addControlSection('child_layout', __('Child Elements Layout'), 'assets/icon.png', $this);
		
		$layout_child->addStyleControl([
			'selector' 			=> '.css-grid-item-wrap',
			'property' 			=> 'display',
			'default' 			=> 'flex'
		]);

		$align_items = $this->addStyleControl([
			'control_type' 		=> 'radio',
			'selector' 			=> ' ',
			'property' 			=> 'align-self',
			'value' 			=> ['start' => 'start', 'end' => 'end', 'center' => 'center', 'stretch' => 'stretch']
		]);

		$justify_items = $this->addStyleControl([
			'control_type' 		=> 'radio',
			'selector' 			=> ' ',
			'property' 			=> 'justify-self',
			'value' 			=> ['start' => 'start', 'end' => 'end', 'center' => 'center', 'stretch' => 'stretch']
		]);
		
		$layout_child->flex('.css-grid-item-wrap', $this);
	}

	function render( $options, $defaults, $content ) {
		echo '<div class="css-grid-item-wrap oxy-inner-content">';
		if( $content ) {
			if( function_exists('do_oxygen_elements') )
				echo do_oxygen_elements( $content );
			else
				echo do_shortcode( $content );
		}
		echo '</div>';
	}

	function customCSS( $original, $selector ) {
		$css = '';
		if( ! $this->css_added ) {
			$css .= '.css-grid-item-wrap{display: flex; height: 100%;flex-direction: column; position: relative;}
					#ct-builder .oxy-ou-css-grid-item:not(.ct-active){outline: 1px dashed #bbb;}';
			$css .= '@media only screen and (max-width: 768px) {' . $selector .'{grid-column: auto;}}';
			$this->css_added = true;
		}

		return $css;
	}
}

new OUCSSGridItem();