<?php

namespace Oxygen\OxyUltimate;

class OUCSSGrid extends \OxyUltimateEl {

	public $css_added = false;

	function name() {
		return __( "CSS Grid", 'oxy-ultimate' );
	}

	function slug() {
		return "ou_css_grid";
	}

	function oxyu_button_place() {
		return "content";
	}

	function icon() {
		return OXYU_URL . 'assets/images/elements/' . basename(__FILE__, '.php').'.svg';
	}

	function init() {
		$this->El->useAJAXControls();
		$this->enableNesting();
		add_action("ct_toolbar_component_settings", function() {
		?>
			<label class="oxygen-control-label oxy-ou_css_grid-elements-label"
				ng-if="isActiveName('oxy-ou_css_grid')&&!hasOpenTabs('oxy-ou_css_grid')" style="text-align: center; margin-top: 15px;">
				<?php _e("Available Elements","oxy-ultimate"); ?>
			</label>
			<div class="oxygen-control-row oxy-ou_css_grid-elements"
				ng-if="isActiveName('oxy-ou_css_grid')&&!hasOpenTabs('oxy-ou_css_grid')">
				<?php do_action("oxygen_add_plus_oucssgrid_comp"); ?>
			</div>
		<?php }, 14 );
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

		$template = $this->addControlSection('cssgrid_areas', __('Areas'), 'assets/icon.png', $this);

		$template->addCustomControl(
			__('<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 14px;">
				<ul>
					<li>Configure it after creating the grid area with Grid Area element.</li>
					<li>Use pipeline(|) as a seperator. Do not use single or double quote. <a href="https://www.w3schools.com/cssref/pr_grid-template-areas.asp" target="_blank" style="color:rgba(255,255,255,0.5);text-decoration: none;">Click here to see the instruction</a></li>
					<li>Grid area name for Repeater component is <span style="color:#ff7171;">gridrepeater</span>.</li>
					<li>Grid area name for Easy Posts component is <span style="color:#ff7171;">grideasyposts</span>.</li>
					<li>Grid area name for Product Lists component is <span style="color:#ff7171;">gridwooproducts</span>.</li>
					<li>Click on <span style="color:#ff7171;">Apply Params</span> button at below to see the changes.</li>
				</ul>
				</div>
			'), 
			'template_areas_note'
		)->setParam('heading', 'Note:');

		$template->addOptionControl([
			'type' 			=> 'textarea',
			'name' 			=> __('All Devices'),
			'slug' 			=> 'template_areas'
		]);

		$template->addOptionControl([
			'type' 			=> 'textarea',
			'name' 			=> __('Less than 992px'),
			'slug' 			=> 'template_areas_tablet'
		]);

		$template->addOptionControl([
			'type' 			=> 'textarea',
			'name' 			=> __('Less than 768px'),
			'slug' 			=> 'template_areas_landscape'
		]);

		$template->addOptionControl([
			'type' 			=> 'textarea',
			'name' 			=> __('Less than 680px'),
			'slug' 			=> 'template_areas_md'
		]);

		$template->addOptionControl([
			'type' 			=> 'textarea',
			'name' 			=> __('Less than 480px'),
			'slug' 			=> 'template_areas_portrait'
		]);

		$length = $this->addControlSection('cssgrid_length', __('Size'), 'assets/icon.png', $this);

		//* Columns
		$columns = $length->addStyleControl([
			'control_type' 		=> 'textfield',
			"selector" 			=> '.css-grid-inner-wrap',
			'name' 				=> __('Grid Template Columns Length'),
			'property' 			=> 'grid-template-columns',
			'placeholder' 		=> '1fr 1fr 1fr'
		]);
		$columns->setParam('description','<a href="https://learncssgrid.com/#explicit-grid" target="_blank" style="color:rgba(255,255,255,0.5);text-decoration: none;">Click here to see the instruction</a>. Unit value would be px, %, em, auto, fr etc');

		//* Rows
		$rows = $length->addStyleControl([
			'control_type' 		=> 'textfield',
			"selector" 			=> '.css-grid-inner-wrap',
			'name' 				=> __('Grid Template Rows Length'),
			'property' 			=> 'grid-template-rows',
			'placeholder' 		=> '1fr'
		]);
		$rows->setParam('description','<a href="https://learncssgrid.com/#explicit-grid" target="_blank" style="color:rgba(255,255,255,0.5);text-decoration: none;">Click here to see the instruction</a>. Unit value would be px, %, em, auto, fr etc');

		$autocolumns = $length->addStyleControl([
			'control_type' 		=> 'textfield',
			'name' 				=> __('Default Columns Length'),
			"selector" 			=> '.css-grid-inner-wrap',
			'property' 			=> 'grid-auto-columns',
			'default' 			=> 'auto'
		]);
		$autocolumns->setParam('description','Set a default size for the columns in a grid. Default size is auto. <a href="https://www.w3schools.com/cssref/pr_grid-auto-columns.asp" target="_blank" style="color:rgba(255,255,255,0.5);text-decoration: none;">Click here to see the instruction</a>.');

		$autorows = $length->addStyleControl([
			'control_type' 		=> 'textfield',
			'name' 				=> __('Default Rows Length'),
			"selector" 			=> '.css-grid-inner-wrap',
			'property' 			=> 'grid-auto-rows',
			'default' 			=> 'auto'
		]);
		$autorows->setParam('description','Set a default size for the rows in a grid. Default size is auto. <a href="https://www.w3schools.com/cssref/pr_grid-auto-rows.asp" target="_blank" style="color:rgba(255,255,255,0.5);text-decoration: none;">Click here to see the instruction</a>.');

		$flow = $length->addStyleControl([
			'control_type' 		=> 'radio',
			'name' 				=> __('Grid Auto Flow'),
			"selector" 			=> '.css-grid-inner-wrap',
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

		//* Gap
		$gap = $this->addControlSection('cssgrid_gap', __('Gap'), 'assets/icon.png', $this);
		$gap->addStyleControl(
			array(
				"name" 			=> __('Column Gap', "oxy-ultimate"),
				"selector" 		=> '.css-grid-inner-wrap',
				"control_type" 	=> 'slider-measurebox',
				"property" 		=> 'grid-column-gap'
			)
		)->setRange(0, 100, 5)->setUnits("px", "px,%,auto,em,fr")->setDefaultValue(10);

		$gap->addStyleControl(
			array(
				"name" 			=> __('Row Gap', "oxy-ultimate"),
				"selector" 		=> '.css-grid-inner-wrap',
				"control_type" 	=> 'slider-measurebox',
				"property" 		=> 'grid-row-gap'
			)
		)->setRange(0, 100, 5)->setUnits("px", "px,%,auto,em,fr");

		$layout = $this->addControlSection('cssgrid_layout', __('Layout'), 'assets/icon.png', $this);

		$display = $layout->addStyleControl([
			'control_type' 		=> 'radio',
			"selector" 			=> '.css-grid-inner-wrap',
			'property' 			=> 'display',
			'value' 			=> ['grid' => 'grid', 'inline-grid' => 'inline-grid'],
			'default' 			=> 'grid'
		]);

		$align_items = $layout->addStyleControl([
			'control_type' 		=> 'radio',
			"selector" 			=> '.css-grid-inner-wrap',
			'property' 			=> 'align-items',
			'value' 			=> ['start' => 'start', 'end' => 'end', 'center' => 'center', 'stretch' => 'stretch']
		]);

		$justify_items = $layout->addStyleControl([
			'control_type' 		=> 'radio',
			"selector" 			=> '.css-grid-inner-wrap',
			'property' 			=> 'justify-items',
			'value' 			=> ['start' => 'start', 'end' => 'end', 'center' => 'center', 'stretch' => 'stretch']
		]);

		$justify_content = $layout->addStyleControl([
			'control_type' 		=> 'radio',
			'selector' 			=> '.css-grid-inner-wrap',
			'property' 			=> 'justify-content',
			'value' 			=> ['start' => 'start', 'end' => 'end', 'center' => 'center', 'stretch' => 'stretch', 'space-around' => 'space-around', 'space-between' => 'space-between', 'space-evenly' => 'space-evenly' ]
		]);

		$align_content = $layout->addStyleControl([
			'control_type' 		=> 'radio',
			'selector' 			=> '.css-grid-inner-wrap',
			'property' 			=> 'align-content',
			'value' 			=> ['start' => 'start', 'end' => 'end', 'center' => 'center', 'stretch' => 'stretch', 'space-around' => 'space-around', 'space-between' => 'space-between', 'space-evenly' => 'space-evenly' ]
		]);

		$text_align = $layout->addStyleControl([
			'control_type' 		=> 'radio',
			"selector" 			=> '.css-grid-inner-wrap',
			'property' 			=> 'text-align',
			'value' 			=> ['left' => 'left', 'center' => 'center', 'right' => 'right' ]
		]);



		$loop = $this->addControlSection( 'cssgrid_loop', __('Grid Loop'), 'assets/icon.png', $this );

		$loop->addCustomControl(
			__('<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 14px;">Settings of this section are for Repeater, Easy Posts and Product Lists components only.</div>'), 
			'loop_note'
		)->setParam('heading', 'Note:');

		$repeater = $loop->addControlSection( 'cssgrid_rep', __('Repeater'), 'assets/icon.png', $this );
		//* Columns
		$repeater->addStyleControl([
			'control_type' 		=> 'textfield',
			'name' 				=> __('Grid Template Columns Length'),
			"selector" 			=> 'div.oxy-dynamic-list',
			'property' 			=> 'grid-template-columns',
			'default' 			=> 'repeat(auto-fill, minmax(300px, 1fr))'
		]);

		//* Rows
		$repeater->addStyleControl([
			'control_type' 		=> 'textfield',
			'name' 				=> __('Grid Template Rows Length'),
			"selector" 			=> 'div.oxy-dynamic-list',
			'property' 			=> 'grid-template-rows',
			'placeholder' 		=> '1fr'
		]);

		//* Gap
		$repeater->addStyleControl(
			array(
				"name" 			=> __('Column Gap', "oxy-ultimate"),
				"selector" 		=> 'div.oxy-dynamic-list',
				"control_type" 	=> 'slider-measurebox',
				"property" 		=> 'grid-column-gap'
			)
		)->setRange(0, 100, 5)->setUnits("px", "px,%,auto,em,fr")->setDefaultValue(15);

		$repeater->addStyleControl(
			array(
				"name" 			=> __('Row Gap', "oxy-ultimate"),
				"selector" 		=> 'div.oxy-dynamic-list',
				"control_type" 	=> 'slider-measurebox',
				"property" 		=> 'grid-row-gap'
			)
		)->setRange(0, 100, 5)->setUnits("px", "px,%,auto,em,fr")->setDefaultValue(15);

		$easyposts = $loop->addControlSection( 'cssgrid_easyposts', __('Easy Posts'), 'assets/icon.png', $this );
		//* Columns
		$easyposts->addStyleControl([
			'control_type' 		=> 'textfield',
			'name' 				=> __('Grid Template Columns Length'),
			"selector" 			=> 'div.oxy-posts',
			'property' 			=> 'grid-template-columns',
			'default' 			=> 'repeat(auto-fill, minmax(300px, 1fr))'
		]);

		//* Rows
		$easyposts->addStyleControl([
			'control_type' 		=> 'textfield',
			'name' 				=> __('Grid Template Rows Length'),
			"selector" 			=> 'div.oxy-posts',
			'property' 			=> 'grid-template-rows',
			'placeholder' 		=> '1fr'
		]);

		//* Gap
		$easyposts->addStyleControl(
			array(
				"name" 			=> __('Column Gap', "oxy-ultimate"),
				"selector" 		=> 'div.oxy-posts',
				"control_type" 	=> 'slider-measurebox',
				"property" 		=> 'grid-column-gap'
			)
		)->setRange(0, 100, 5)->setUnits("px", "px,%,auto,em,fr")->setDefaultValue(15);

		$easyposts->addStyleControl(
			array(
				"name" 			=> __('Row Gap', "oxy-ultimate"),
				"selector" 		=> 'div.oxy-posts',
				"control_type" 	=> 'slider-measurebox',
				"property" 		=> 'grid-row-gap'
			)
		)->setRange(0, 100, 5)->setUnits("px", "px,%,auto,em,fr")->setDefaultValue(15);

		$woo = $loop->addControlSection( 'cssgrid_woo', __('Product Lists'), 'assets/icon.png', $this );
		//* Columns
		$woo->addStyleControl([
			'control_type' 		=> 'textfield',
			'name' 				=> __('Grid Template Columns Length'),
			"selector" 			=> '.woocommerce ul.products',
			'property' 			=> 'grid-template-columns',
			'default' 			=> 'repeat(auto-fill, minmax(300px, 1fr))'
		]);

		//* Rows
		$woo->addStyleControl([
			'control_type' 		=> 'textfield',
			'name' 				=> __('Grid Template Rows Length'),
			"selector" 			=> '.woocommerce ul.products',
			'property' 			=> 'grid-template-rows',
			'placeholder' 		=> '1fr'
		]);

		//* Gap
		$woo->addStyleControl(
			array(
				"name" 			=> __('Column Gap', "oxy-ultimate"),
				"selector" 		=> '.woocommerce ul.products',
				"control_type" 	=> 'slider-measurebox',
				"property" 		=> 'grid-column-gap'
			)
		)->setRange(0, 100, 5)->setUnits("px", "px,%,auto,em,fr")->setDefaultValue(15);

		$woo->addStyleControl(
			array(
				"name" 			=> __('Row Gap', "oxy-ultimate"),
				"selector" 		=> '.woocommerce ul.products',
				"control_type" 	=> 'slider-measurebox',
				"property" 		=> 'grid-row-gap'
			)
		)->setRange(0, 100, 5)->setUnits("px", "px,%,auto,em,fr")->setDefaultValue(15);
	}

	function render( $options, $defaults, $content ) {
		echo '<div class="css-grid-inner-wrap oxy-inner-content">';
		
		if( function_exists('do_oxygen_elements') )
			echo do_oxygen_elements( $content );
		else
			echo do_shortcode( $content );
		
		echo '</div>';
	}

	function customCSS($original, $selector) {
		$css = '';
		 global $media_queries_list;
		if( ! $this->css_added ) {
			$css .= '.oxy-ou-css-grid{width: 100%}
					.css-grid-inner-wrap{display: grid;width:100%;min-height:50px;grid-column-gap: 10px;}
					#ct-builder .oxy-ou-css-grid:not(.ct-active){outline: 1px dashed #bbb;}
					@media only screen and (max-width: 992px) {
						.css-grid-inner-wrap{grid-template-columns: auto;}
					}
					.oxy-inner-content:not(.grid-area-inner-wrap) > .oxy-dynamic-list{grid-area: gridrepeater;}
					.oxy-inner-content:not(.grid-area-inner-wrap) > .oxy-posts{grid-area: grideasyposts;}
					.oxy-inner-content:not(.grid-area-inner-wrap) > .oxy-woo-products{grid-area: gridwooproducts;}
					.oxy-ou-css-grid .woocommerce ul.products::after, 
					.oxy-ou-css-grid .woocommerce ul.products::before{
						display: none;
					}
					.oxy-ou-css-grid .woocommerce ul.products{margin: 0}
					.oxy-ou-css-grid .woocommerce ul.products li.product{margin:0;padding:0;float: none; width: 100%;}
					@media (max-width: 768px){ 
						.oxy-ou-css-grid .woocommerce ul.products[class*="columns-"] li.product, 
						.oxy-ou-css-grid .woocommerce-page ul.products[class*="columns-"] li.product {width: 100%;}
					}';
			$this->css_added = true;
		}

		$css .= $selector . ' .oxy-dynamic-list, ' . $selector . ' .oxy-posts, .oxy-ou-css-grid .oxy-woo-products ul.products{display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); grid-column-gap: 15px; grid-row-gap: 15px; width: 100%;}';

		$css .= $selector . ' .oxy-posts-grid .oxy-post{width: 100%; padding:0; margin: 0;}' . $selector . ' .oxy-posts-grid .oxy-post-padding{margin: 0}@media only screen and (max-width: '. $media_queries_list['page-width']['maxSize'] . '){' . $selector . ' .oxy-posts-grid .oxy-post{width: 100%!important;}}';

			

		$prefix = $this->El->get_tag();
		if( isset($original[$prefix . '_template_areas']) ) {
			$areas = str_replace("|", '" "', $original[$prefix . '_template_areas']);
			$css .= $selector . ' .css-grid-inner-wrap{grid-template-areas:"'. $areas .'"}';
		}

		if( isset($original[$prefix . '_template_areas_tablet']) ) {
			$tablet_areas = str_replace("|", '" "', $original[$prefix . '_template_areas_tablet']);
			$css .= '@media only screen and (max-width: 992px){' . $selector . ' .css-grid-inner-wrap{grid-template-areas:"'. $tablet_areas .'"}}';
		}

		if( isset($original[$prefix . '_template_areas_landscape']) ) {
			$landscape_areas = str_replace("|", '" "', $original[$prefix . '_template_areas_landscape']);
			$css .= '@media only screen and (max-width: 768px){' . $selector . ' .css-grid-inner-wrap{grid-template-areas:"'. $landscape_areas .'"}}';
		}

		if( isset($original[$prefix . '_template_areas_md']) ) {
			$md_areas = str_replace("|", '" "', $original[$prefix . '_template_areas_md']);
			$css .= '@media only screen and (max-width: 680px){' . $selector . ' .css-grid-inner-wrap{grid-template-areas:"'. $md_areas .'"}}';
		}

		if( isset($original[$prefix . '_template_areas_portrait']) ) {
			$portrait_areas = str_replace("|", '" "', $original[$prefix . '_template_areas_portrait']);
			$css .= '@media only screen and (max-width: 480px){' . $selector . ' .css-grid-inner-wrap{grid-template-areas:"'. $portrait_areas .'"}}';
		}

		return $css;
	}
}

new OUCSSGrid();

include_once OXYU_DIR . 'elements/ou-css-grid/grid-area.php';
include_once OXYU_DIR . 'elements/ou-css-grid/grid-item.php';