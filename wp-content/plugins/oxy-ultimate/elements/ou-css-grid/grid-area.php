<?php

namespace Oxygen\OxyUltimate;

Class OUCSSGridArea extends \OxyUltimateEl {
	public $css_added = false;

	function name() {
		return __( "Grid Area", 'oxy-ultimate' );
	}

	function slug() {
		return "ou_css_grid_area";
	}

	function button_place() {
		return "oucssgrid::comp";
	}

	/*function icon() {
		return CT_FW_URI . '/toolbar/UI/oxygen-icons/add-icons/columns.svg';
	}*/

	function tag() {
		$tags = array('default' => 'div', 'choices' => 'div,article,aside,figure,footer,header,main,nav,section' );
		return $tags;
	}

	function init() {
		$this->El->useAJAXControls();
		$this->enableNesting();
		add_action("ct_toolbar_component_settings", function() {
		?>
			<label class="oxygen-control-label oxy-ou_css_grid_area-elements-label"
				ng-if="isActiveName('oxy-ou_css_grid_area')&&!hasOpenTabs('oxy-ou_css_grid_area')" style="text-align: center; margin-top: 15px;">
				<?php _e("Available Elements","oxy-ultimate"); ?>
			</label>
			<div class="oxygen-control-row oxy-ou_css_grid_area-elements"
				ng-if="isActiveName('oxy-ou_css_grid_area')&&!hasOpenTabs('oxy-ou_css_grid_area')">
				<?php do_action("oxygen_add_plus_oucssgrid_comp"); ?>
			</div>
		<?php }, 16 );
	}

	function controls() {

		$this->addCustomControl(
			__('<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 14px;">Click on <span style="color:#ff7171;">Apply Params</span> button at below, if contents are not displaying properly on Builder editor.</div>'), 
			'description'
		)->setParam('heading', 'Note:');

		$this->addTagControl();

		$area = $this->addStyleControl([
			'control_type' 		=> 'textfield',
			'name' 				=> __('Grid Area Name'),
			'selector' 			=> ' ',
			'property' 			=> 'grid-area'
		]);
		$area->setParam('description','Do not use space, single/double quote. After creating the area, you will setup the Grid Template Areas under CSS Grid component settings.');

		$length = $this->addControlSection('gridarea_length', __('Size'), 'assets/icon.png', $this);

		//* Columns
		$columns = $length->addStyleControl([
			'control_type' 		=> 'textfield',
			'name' 				=> __('Grid Template Columns Length'),
			"selector" 			=> '.grid-area-inner-wrap',
			'property' 			=> 'grid-template-columns'
		]);
		$columns->setParam('description','<a href="https://learncssgrid.com/#explicit-grid" target="_blank" style="color:rgba(255,255,255,0.5);text-decoration: none;">Click here to see the instruction</a>. Unit value would be px, %, em, auto, fr etc');

		//* Rows
		$rows = $length->addStyleControl([
			'control_type' 		=> 'textfield',
			'name' 				=> __('Grid Template Rows Length'),
			"selector" 			=> '.grid-area-inner-wrap',
			'property' 			=> 'grid-template-rows'
		]);
		$rows->setParam('description','<a href="https://learncssgrid.com/#explicit-grid" target="_blank" style="color:rgba(255,255,255,0.5);text-decoration: none;">Click here to see the instruction</a>. Unit value would be px, %, em, auto, fr etc');

		$autocolumns = $length->addStyleControl([
			'control_type' 		=> 'textfield',
			'name' 				=> __('Default Columns Length'),
			"selector" 			=> '.grid-area-inner-wrap',
			'property' 			=> 'grid-auto-columns',
			'default' 			=> 'auto'
		]);
		$autocolumns->setParam('description','Set a default size for the columns in a grid. Default size is auto. <a href="https://www.w3schools.com/cssref/pr_grid-auto-columns.asp" target="_blank" style="color:rgba(255,255,255,0.5);text-decoration: none;">Click here to see the instruction</a>.');

		$autorows = $length->addStyleControl([
			'control_type' 		=> 'textfield',
			'name' 				=> __('Default Rows Length'),
			"selector" 			=> '.grid-area-inner-wrap',
			'property' 			=> 'grid-auto-rows',
			'default' 			=> 'auto'
		]);
		$autorows->setParam('description','Set a default size for the rows in a grid. Default size is auto. <a href="https://www.w3schools.com/cssref/pr_grid-auto-rows.asp" target="_blank" style="color:rgba(255,255,255,0.5);text-decoration: none;">Click here to see the instruction</a>.');

		$flow = $length->addStyleControl([
			'control_type' 		=> 'radio',
			'name' 				=> __('Grid Auto Flow'),
			"selector" 			=> '.grid-area-inner-wrap',
			'property' 			=> 'grid-auto-flow',
			'value' 			=> [ 
				'row' 			=> 'Row', 
				'column' 		=> 'Column', 
				'dense' 		=> 'Dense', 
				'row dense' 	=> 'Row Dense', 
				'column dense' 	=> 'Column Dense'
			],
			'default' 			=> 'row'
		]);

		//* Columns
		$column = $length->addStyleControl([
			'control_type' 		=> 'textfield',
			'name' 				=> __('Grid Column Span'),
			'selector' 			=> ' ',
			'property' 			=> 'grid-column'
		]);
		$column->setParam('description','<a href="https://www.w3schools.com/css/css_grid_item.asp" target="_blank" style="color:rgba(255,255,255,0.5);text-decoration: none;">Click here to see the instruction</a>.');

		//* Rows
		$row = $length->addStyleControl([
			'control_type' 		=> 'textfield',
			'name' 				=> __('Grid Row Span'),
			'selector' 			=> ' ',
			'property' 			=> 'grid-row'
		]);
		$row->setParam('description','<a href="https://www.w3schools.com/css/css_grid_item.asp" target="_blank" style="color:rgba(255,255,255,0.5);text-decoration: none;">Click here to see the instruction</a>.');

		//* Gap
		$gap = $this->addControlSection('gridarea_gap', __('Gap'), 'assets/icon.png', $this);
		$gap->addStyleControl(
			array(
				"name" 			=> __('Column Gap', "oxy-ultimate"),
				"selector" 		=> '.grid-area-inner-wrap',
				"control_type" 	=> 'slider-measurebox',
				"property" 		=> 'grid-column-gap'
			)
		)->setRange(0, 100, 5)->setUnits("px", "px,%,auto,em,fr")->setDefaultValue(10);

		$gap->addStyleControl(
			array(
				"name" 			=> __('Row Gap', "oxy-ultimate"),
				"selector" 		=> '.grid-area-inner-wrap',
				"control_type" 	=> 'slider-measurebox',
				"property" 		=> 'grid-row-gap'
			)
		)->setRange(0, 100, 5)->setUnits("px", "px,%,auto,em,fr");

		$layout = $this->addControlSection('gridarea_layout', __('Layout'), 'assets/icon.png', $this);

		$display = $layout->addStyleControl([
			'control_type' 		=> 'radio',
			"selector" 			=> '.grid-area-inner-wrap',
			'property' 			=> 'display',
			'value' 			=> [ 'grid' => 'grid', 'inline-grid' => 'inline-grid'],
			'default' 			=> 'grid'
		]);

		$align_items = $layout->addStyleControl([
			'control_type' 		=> 'radio',
			"selector" 			=> '.grid-area-inner-wrap',
			'property' 			=> 'align-items',
			'value' 			=> [ 'start' => 'start', 'end' => 'end', 'center' => 'center', 'stretch' => 'stretch']
		]);

		$justify_items = $layout->addStyleControl([
			'control_type' 		=> 'radio',
			"selector" 			=> '.grid-area-inner-wrap',
			'property' 			=> 'justify-items',
			'value' 			=> [ 'start' => 'start', 'end' => 'end', 'center' => 'center', 'stretch' => 'stretch']
		]);

		$text_align = $layout->addStyleControl([
			'control_type' 		=> 'radio',
			"selector" 			=> '.grid-area-inner-wrap',
			'property' 			=> 'text-align',
			'value' 			=> [ 'left' => 'left', 'center' => 'center', 'right' => 'right' ]
		]);
	}

	function render( $options, $defaults, $content ) {
		echo '<div class="grid-area-inner-wrap oxy-inner-content">';
		
		if( function_exists('do_oxygen_elements') )
			echo do_oxygen_elements( $content );
		else
			echo do_shortcode( $content );
		
		echo '</div>';
	}

	function customCSS($original, $selector) {
		$css = '';
		if( ! $this->css_added ) {
			$css .= '.oxy-ou-css-grid-area,.grid-area-inner-wrap{display: grid;width:100%;min-height:50px;}
				#ct-builder .oxy-ou-css-grid-area:not(.ct-active){outline: 1px dashed #bbb;}
				@media only screen and (max-width: 992px) {
					.grid-area-inner-wrap{grid-template-columns: auto;}
				}';
			$this->css_added = true;
		}

		return $css;
	}
}

new OUCSSGridArea();