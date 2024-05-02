<?php

namespace Oxygen\OxyUltimate;

class OUSlidingMenu extends \OxyUltimateEl {
	public $sdm_css_added = false;
	public $sdm_js_added = false;

	public $unique_index = 1;

	function name() {
		return __( "Sliding Menu", "oxy-ultimate" );
	}

	function slug() {
		return "ou_sliding_menu";
	}

	function oxyu_button_place() {
		return "menu";
	}

	function custom_init() {
		add_filter("oxy_allowed_empty_options_list", array( $this, "allowedEmptyOptions") );
	}

	function allowedEmptyOptions($options) {
        $options_to_add = array(
        	"oxy-ou_sliding_menu_back_text"
        );

        $options = array_merge($options, $options_to_add);

        return $options;
    }

	function subMenuArrow() {
		$arrow = $this->addControlSection( 'arrow_sec', __('Link Arrow', "oxy-ultimate"), "assets/icon.png", $this );

		$selector = '.ou-slide-menu-arrow';

		$icon = $arrow->addControlSection( 'icon_sec', __('Icon', "oxy-ultimate"), "assets/icon.png", $this );
		$icon->addOptionControl(
			array(
				"type" 		=> 'icon_finder',
				"name" 		=> __('Select Icon', "oxy-ultimate"),
				"slug" 		=> 'arrow_icon',
				"default" 	=> 'FontAwesomeicon-angle-right'
			)
		)->setParam('description', __("Click on Apply Params button and apply changes.", "oxy-ultimate"));

		$icon->addStyleControl([
			'name' 		=> __('Icon Size', "oxy-ultimate"),
			'selector' 	=> $selector . " svg",
			'property' 	=> "width|height",
			"control_type" => 'slider-measurebox'
		])->setUnits('px', 'px,em,%')->setDefaultValue(25);

		$icon->addStyleControl([
			'selector' 	=> $selector,
			'property' 	=> "width",
			"control_type" => 'slider-measurebox'
		])->setUnits('px', 'px,em,%')->setDefaultValue(50);

		$color = $arrow->addControlSection( 'clrs_sec', __('Color', "oxy-ultimate"), "assets/icon.png", $this );
		$color->addStyleControls([
			[
				'selector' 	=> $selector,
				'property' 	=> "background-color"
			],
			[
				'name' 		=> __('Background Color on Hover', "oxy-ultimate"),
				'selector' 	=> ".ou-slide-menu-item:hover > " . $selector,
				'property' 	=> "background-color"
			],
			[
				'name' 		=> __('Background Color of Current Item', "oxy-ultimate"),
				'selector' 	=> ".current-menu-item > " . $selector . ", .current-menu-ancestor > " . $selector,
				'property' 	=> "background-color"
			],
			[
				'name' 		=> __('Icon Color', "oxy-ultimate"),
				'selector' 	=> $selector . " svg",
				'property' 	=> "color"
			],
			[
				'name' 		=> __('Icon Color on Hover', "oxy-ultimate"),
				'selector' 	=> ".ou-slide-menu-item:hover > " . $selector . " svg",
				'property' 	=> "color"
			],
			[
				'name' 		=> __('Icon Color of Current Item', "oxy-ultimate"),
				'selector' 	=> ".current-menu-item > " . $selector . " svg, .current-menu-ancestor > " . $selector . " svg",
				'property' 	=> "color"
			]
		]);

		$arrow->borderSection(__('Border'), $selector, $this);
		$arrow->borderSection(__('Hover Border'), ".ou-slide-menu-item:hover > " . $selector, $this);
		$arrow->borderSection(__('Active Border'), ".ou-slide-menu-item.current-menu-item > " . $selector . ", .ou-slide-menu-item.current-menu-ancestor > " . $selector, $this);
	}

	function links() {
		$links = $this->addControlSection( 'links', __('Menu Items', "oxy-ultimate"), "assets/icon.png", $this );

		$selector = '.ou-slide-menu-item-link';

		$links->addStyleControl([
			'name' 		=> __('Wrapper Background Color'),
			'selector' 	=> 'nav, .ou-slide-menu-sub-menu',
			'property' 	=> "background-color",
			'default' 	=> '#ffffff'
		]);

		$spacing = $links->addControlSection( 'link_sp', __('Spacing', "oxy-ultimate"), "assets/icon.png", $this );
		$spacing->addPreset(
			"padding",
			"link_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$color = $links->addControlSection( 'lcolor_sec', __('Colors', "oxy-ultimate"), "assets/icon.png", $this );
		$color->addStyleControls([
			[
				'selector' 	=> $selector,
				'property' 	=> "background-color"
			],
			[
				'name' 		=> __('Background Color on Hover', "oxy-ultimate"),
				'selector' 	=> ".ou-slide-menu-item:hover > " . $selector,
				'property' 	=> "background-color"
			],
			[
				'name' 		=> __('Background Color of Current Item', "oxy-ultimate"),
				'selector' 	=> ".current-menu-item > " . $selector . ", .current-menu-ancestor > " . $selector,
				'property' 	=> "background-color"
			],
			[
				'name' 		=> __('Color on Hover', "oxy-ultimate"),
				'selector' 	=> ".ou-slide-menu-item:hover > " . $selector,
				'property' 	=> "color"
			],
			[
				'name' 		=> __('Color of Current Item', "oxy-ultimate"),
				'selector' 	=> ".current-menu-item > " . $selector . ", .current-menu-ancestor > " . $selector,
				'property' 	=> "color"
			]
		]);

		$links->typographySection(__('Typography'), $selector, $this);

		$links->borderSection(__('Border'), '.ou-slide-menu-item', $this);
		$links->borderSection(__('Hover Border'), ".ou-slide-menu-item:hover", $this);
		$links->borderSection(__('Active Border'), ".ou-slide-menu-item.current-menu-item, .ou-slide-menu-item.current-menu-ancestor", $this);
	}

	function backButton() {
		$back = $this->addControlSection( 'back_btn', __('Back Bar', "oxy-ultimate"), "assets/icon.png", $this );

		$selector = '.ou-menu-sub-item-back';
		$bar_selector = '.ou-slide-menu-back';

		$back->addOptionControl([
			'type' 	=> 'textfield',
			'name' 	=> __('Back Text', "oxy-ultimate"),
			'slug' 	=> 'back_text',
			'default' => __('Back', "oxy-ultimate")
		])->setParam('description', __('This text will show if a menu item have not title. It will work as a placeholder.', "oxy-ultimate") );

		$back->typographySection(__('Typography'), $selector, $this);

		$color = $back->addControlSection( 'bbcolor_sec', __('Colors', "oxy-ultimate"), "assets/icon.png", $this );
		$color->addStyleControls([
			[
				'selector' 	=> $bar_selector,
				'property' 	=> "background-color"
			],
			[
				'name' 		=> __('Background Color on Hover', "oxy-ultimate"),
				'selector' 	=> $bar_selector . ":hover",
				'property' 	=> "background-color"
			],
			[
				'name' 		=> __('Color on Hover', "oxy-ultimate"),
				'selector' 	=> $bar_selector . ":hover " . $selector,
				'property' 	=> "color"
			]
		]);

		$spacing = $back->addControlSection( 'bbtn_sp', __('Spacing', "oxy-ultimate"), "assets/icon.png", $this );
		$spacing->addPreset(
			"padding",
			"link_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$back->borderSection(__('Border'), $bar_selector, $this);
	}

	function backArrow() {
		$arrow = $this->addControlSection( 'barrow_sec', __('Back Arrow', "oxy-ultimate"), "assets/icon.png", $this );

		$selector = '.ou-slide-menu-back-arrow';

		$icon = $arrow->addControlSection( 'bicon_sec', __('Icon', "oxy-ultimate"), "assets/icon.png", $this );
		$icon->addOptionControl(
			array(
				"type" 		=> 'icon_finder',
				"name" 		=> __('Select Icon', "oxy-ultimate"),
				"slug" 		=> 'barrow_icon',
				"default" 	=> 'FontAwesomeicon-angle-left'
			)
		)->setParam('description', __("Click on Apply Params button and apply changes.", "oxy-ultimate"));

		$icon->addStyleControl([
			'name' 		=> __('Size', "oxy-ultimate"),
			'selector' 	=> $selector . " svg",
			'property' 	=> "width|height",
			"control_type" => 'slider-measurebox'
		])->setUnits('px', 'px,em,%')->setDefaultValue(25);

		$icon->addStyleControl([
			'selector' 	=> $selector,
			'property' 	=> "width",
			"control_type" => 'slider-measurebox'
		])->setUnits('px', 'px,em,%')->setDefaultValue(50);

		$color = $arrow->addControlSection( 'bclrs_sec', __('Color', "oxy-ultimate"), "assets/icon.png", $this );
		$color->addStyleControls([
			[
				'selector' 	=> $selector,
				'property' 	=> "background-color"
			],
			[
				'name' 		=> __('Background Color on Hover', "oxy-ultimate"),
				'selector' 	=> ".ou-slide-menu-back:hover " . $selector,
				'property' 	=> "background-color"
			],
			[
				'name' 		=> __('Icon Color', "oxy-ultimate"),
				'selector' 	=> $selector . " svg",
				'property' 	=> "color"
			],
			[
				'name' 		=> __('Icon Color on Hover', "oxy-ultimate"),
				'selector' 	=> ".ou-slide-menu-back:hover " . $selector . " svg",
				'property' 	=> "color"
			]
		]);

		$arrow->borderSection(__('Border'), $selector, $this);
		$arrow->borderSection(__('Hover Border'), ".ou-slide-menu-back:hover > " . $selector, $this);
	}

	function hoverAnimation() {
		$anim = $this->addControlSection( 'link_hvr', __('Hover Animation', "oxy-ultimate"), "assets/icon.png", $this );

		$anim->addStyleControl([
			'control_type' 	=> 'slider-measurebox',
			'selector' 		=> 'li a span, .ou-slide-menu-arrow, .ou-slide-menu-item-link, .ou-menu-sub-item-back, .ou-slide-menu-back',
			'property' 		=> 'transition-duration',
			'slug' 			=> 'hovr_link_td'
		])->setUnits('s', 'sec')->setRange(0,3,0.1);

		$anim->addStyleControl([
			'name' 			=> __('Slide from Left Side(translateX)', "oxy-ultimate"),
			'control_type' 	=> 'slider-measurebox',
			'selector' 		=> ' ',
			'property' 		=> '--link-span-translatex'
		])->setUnits("px","px")->setDefaultValue(10);
	}

	function config() {
		$config = $this->addControlSection( 'config_sec', __('Config', "oxy-ultimate"), "assets/icon.png", $this );

		$config->addOptionControl([
			'type' 	=> 'dropdown',
			'name' 	=> __( 'Source', "oxy-ultimate" ),
			'slug' 	=> 'sd_source_menu',
			'value'	=> ['wpmenu' => __('WP Menu', "oxy-ultimate"), 'tax' => __('Taxonomy', "oxy-ultimate")],
			'default' => 'wpmenu'
		]);

		$config->addOptionControl([
			'type' 	=> 'dropdown',
			'name' 	=> __( 'Menu', "oxy-ultimate" ),
			'slug' 	=> 'sd_menu',
			'value'	=> ouGetWPMeuns(),
			'default' => 'sel',
			'condition' => 'sd_source_menu=wpmenu'
		])->rebuildElementOnChange();

		$config->addOptionControl([
			'type' 	=> 'dropdown',
			'name' 	=> __( 'Select Taxonomy', "oxy-ultimate" ),
			'slug' 	=> 'sd_tax_name',
			'value'	=> ou_get_taxonomies(),
			'default' => 'category',
			'condition' => 'sd_source_menu=tax'
		])->rebuildElementOnChange();

		$taxDiv = $config->addCustomControl( 
			__('<hr />'), 
			'tax_divider'
		);
		$taxDiv->setParam('heading', 'Taxonomy Query');
		$taxDiv->setCondition('sd_source_menu=tax');

		$config->addOptionControl([
            'type'      => 'textfield',
            'name'      => __('Specific Categories Only(IDs)', 'oxy-ultimate'),
            'slug'      => 'include_ids',
            'condition' => 'sd_source_menu=tax'
		]);
		
		$config->addOptionControl([
            'type'      => 'textfield',
            'name'      => __('Exclude Specific Categories(IDs)', 'oxy-ultimate'),
            'slug'      => 'exclude_ids',
            'condition' => 'sd_source_menu=tax'
		]);

		$config->addOptionControl([
			"type" => 'buttons-list',
			"name" => 'Hide Empty',
			"slug" => 'hide_empty',
			"default" => "yes",
			'condition' => 'sd_source_menu=tax'
        ])->setValue(array( 'yes' => __('Yes'), 'no' => __('No') ))->rebuildElementOnChange();

        $config->addOptionControl(
			array(
                "type" => 'textfield',
                "name" => 'Child Of',
                "slug" => 'child_of',
				'condition' => 'sd_source_menu=tax'
            )
		)->rebuildElementOnChange();

        $config->addOptionControl(
			array(
                "type" => 'textfield',
				"name" => 'Limit',
				"slug" => 'limit',
				'condition' => 'sd_source_menu=tax'
            )
		)->rebuildElementOnChange();

        $config->addOptionControl(
			array(
                "type" => 'buttons-list',
                "name" => 'Order',
				"slug" => 'order',
				"default" => "ASC",
				'condition' => 'sd_source_menu=tax'
            )
		)->setValue(array( 'ASC' => 'ASC', 'DESC' => 'DESC'))->rebuildElementOnChange();

		$config->addOptionControl(
			array(
                "type" => 'dropdown',
                "name" => 'Order By',
				"slug" => 'orderby',
				'value' => array(
					'name' 			=> __('Name'), 
					'id' 			=> __('ID'), 
					'slug' 			=> __('Slug'), 
					'menu_order' 	=> __('Menu Order'), 
					'include' 		=> __('Include'),
					'count' 		=> __('Count')
				),
				"default" => "name",
				'condition' => 'sd_source_menu=tax'
            )
		)->rebuildElementOnChange();

		$divider = $config->addCustomControl( 
			__('<hr />'), 
			'divider'
		);
		$divider->setParam('heading', 'Animation Effect');
		$divider->setCondition('sd_source_menu=tax');

		$config->addOptionControl([
			'type' 	=> 'radio',
			'name' 	=> __( 'Effect' ),
			'slug' 	=> 'sd_menu_effect',
			'value'	=> array(
				'overlay' => __( 'Overlay', "oxy-ultimate" ),
				'push'    => __( 'Push', "oxy-ultimate" ),
			),
			'default' => 'overlay'
		])->rebuildElementOnChange();

		$config->addOptionControl([
			'type' 	=> 'dropdown',
			'name' 	=> __( 'Direction', "oxy-ultimate" ),
			'slug' 	=> 'sd_menu_direction',
			'value'	=> array(
				'left'   => __( 'Left', "oxy-ultimate" ),
				'right'  => __( 'Right', "oxy-ultimate" ),
				'top'    => __( 'Top', "oxy-ultimate" )
			),
			'default' => 'left'
		])->rebuildElementOnChange();

		$config->addOptionControl([
			'type' 	=> 'radio',
			'name' 	=> __( 'Auto Height', "oxy-ultimate" ),
			'slug' 	=> 'sd_menu_height',
			'value'	=> array(
				'auto' 	=> __( 'Yes', "oxy-ultimate" ),
				'full' 	=> __( 'No', "oxy-ultimate" )
			),
			'default' => 'auto'
		])->rebuildElementOnChange();

		$config->addStyleControl([
			'name' 			=> __('Transition Duration of Effect', "oxy-ultimate"),
			'control_type' 	=> 'slider-measurebox',
			'selector' 		=> '.ou-slide-menu-comp, .ou-slide-menu-sub-menu',
			'property' 		=> 'transition-duration',
			'slug' 			=> 'menu_effect_td'
		])->setUnits('s', 'sec')->setRange(0,3,0.1);
	}

	function menutitle() {
		$title = $this->addControlSection( 'menu_title', __('Menu Title', "oxy-ultimate"), "assets/icon.png", $this);

		$title->addCustomControl( 
			__('<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 13px;">This section is for WP Menu.</div>'), 
			'menu_note'
		)->setParam('heading', 'Note:');

		$selector = '.ou-sdmenu-title';

		$title->addOptionControl([
			'type' 	=> 'radio',
			'name' 	=> __('Display Menu Title', "oxy-ultimate"),
			'slug' 	=> 'display_menu_title',
			'value' => ['no' => __('No'), 'yes' => __('Yes')],
			'default' => 'no'
		])->rebuildElementOnChange();

		$title->addOptionControl([
			'type' 	=> 'dropdown',
			'name' 	=> __('Tag', "oxy-ultimate"),
			'slug' 	=> 'menu_title_tag',
			'value' => [
				'h1' => __('H1'), 
				'h2' => __('H2'),
				'h3' => __('H3'),
				'h4' => __('H4'),
				'h5' => __('H5'),
				'h6' => __('H6')
			],
			'default' => 'h4'
		])->rebuildElementOnChange();

		$title->addStyleControl([
			'selector' 	=> $selector,
			'property' 	=> "background-color"
		]);

		$spacing = $title->addControlSection( 'title_sp', __('Spacing', "oxy-ultimate"), "assets/icon.png", $this );
		$spacing->addPreset(
			"padding",
			"title_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$title->typographySection(__('Typography'), $selector, $this);

		$title->borderSection(__('Border'), $selector, $this);
	}

	function controls() {

		$this->config();

		$this->menutitle();

		$this->links();

		$this->subMenuArrow();

		$this->backButton();

		$this->backArrow();

		$this->hoverAnimation();
	}

	function render( $options, $defaults, $content ) {
		$datattr = array(); $sm_html = '';

		$menuSource = isset($options['sd_source_menu']) ? $options['sd_source_menu'] : 'wpmenu';

		$effect = isset( $options['sd_menu_effect'] ) ? $options['sd_menu_effect'] : 'overlay';
		$direction = isset( $options['sd_menu_direction'] ) ? $options['sd_menu_direction'] : 'left';
		$classes = 'ou-sliding-menu-effect-' . $effect;
		$classes .= ' ou-sliding-menu-direction-' . $direction;

		global $oxygen_svg_icons_to_load;

		$icon = isset( $options['arrow_icon'] ) ? esc_attr($options['arrow_icon']) : "FontAwesomeicon-angle-right";
		$backicon = isset( $options['barrow_icon'] ) ? esc_attr($options['barrow_icon']) : "FontAwesomeicon-angle-left";
		$oxygen_svg_icons_to_load[] = $icon;
		$oxygen_svg_icons_to_load[] = $backicon;

		$backText = wp_kses_post($options['back_text']);
		$datattr[] = 'data-back-text="' . $backText . '"';
		$datattr[] = 'data-back-icon="' . $backicon . '"';

		$height = isset($options['sd_menu_height']) ? $options['sd_menu_height'] : 'auto';
		$datattr[] = 'data-nav-height="' . $height . '"';

		/**
		 * WP Menu
		 */
		if( $menuSource == 'wpmenu' ) {
			$menu = isset( $options['sd_menu'] ) ? $options['sd_menu'] : 'sel';
			if( $menu == 'sel' || $menu == 'nomenu' ) {
				echo __('Select Menu', "oxy-ultimate");
				return;
			}

			$display_title = isset($options['display_menu_title']) ? $options['display_menu_title'] : 'no';
			if( $display_title == 'yes') {
				$tag = isset($options['menu_title_tag']) ? $options['menu_title_tag'] : 'h4';
				echo '<' . $tag . ' class="ou-sdmenu-title">'. wp_get_nav_menu_object($menu)->name . '</' . $tag .'>';
			}

			$sm_html = '<nav class="'. $classes .'" itemscope="" itemtype="https://schema.org/SiteNavigationElement" '. implode(" ", $datattr) .'>';

			$args = array(
				'echo'        => false,
				'menu'        => $menu,
				'menu_class'  => 'ou-slide-menu-comp',
				'menu_id'     => 'menu-' . $this->get_unique_index() . $options['selector'],
				'fallback_cb' => '__return_empty_string',
				'before'      => '<span class="ou-slide-menu-arrow" aria-expanded="false" aria-pressed="false"><svg class="sm-toggle-icon"><use xlink:href="#'. $icon .'"></use></svg></span>',
				'link_before' => '<span itemprop="name">',
				'link_after'  => '</span>',
				'container'   => '',
			);

			add_filter( 'nav_menu_link_attributes', array( $this, 'ou_sdmenu_link_attributes' ), 10, 4 );
			add_filter( 'nav_menu_submenu_css_class', array( $this, 'ou_sdmenu_submenu_css_class' ) );
			add_filter( 'nav_menu_css_class', array( $this, 'ou_sdmenu_css_class' ) );

			$sm_html .= wp_nav_menu( $args );

			remove_filter( 'nav_menu_link_attributes', array( $this, 'ou_sdmenu_link_attributes' ), 10, 4 );
			remove_filter( 'nav_menu_submenu_css_class', array( $this, 'ou_sdmenu_submenu_css_class' ) );
			remove_filter( 'nav_menu_css_class', array( $this, 'ou_sdmenu_css_class' ) );

			$sm_html .= '</nav>';
		}

		/**
		 * Taxonomy
		 */
		if( $menuSource == 'tax' ) {
			$sm_html = '<nav class="'. $classes .'" itemscope="" itemtype="https://schema.org/SiteNavigationElement" '. implode(" ", $datattr) .'>';
			$sm_html .= '<ul id="menu-' . $this->get_unique_index() . $options['selector'] .'" class="ou-slide-menu-comp">';

			$taxonomy = isset( $options['sd_tax_name'] ) ? $options['sd_tax_name'] : 'category';

			$args = array(
				'show_option_all'    => '',
				'style'              => 'list',
				'show_count'         => 0,
				'hide_empty'         => 1,
				'hierarchical'       => 1,
				'include'    		 => '',
				'exclude'    		 => '',
				'title_li'           => '',
				'show_option_none'   => '',
				'number'             => null,
				'echo'               => 0,
				'depth'              => 0,
				'current_category'   => 0,
				'pad_counts'         => 0,
				'taxonomy'           => $taxonomy,
				'walker'             => new OU_Sliding_Category_Walker
			);

			if( isset( $options['include_ids'] ) ) {
				$args['include'] = array_filter( array_map( 'trim', explode( ',', $options['include_ids'] ) ) );
			}

			if( isset( $options['exclude_ids'] ) ) {
				$args['exclude'] = array_filter( array_map( 'trim', explode( ',', $options['exclude_ids'] ) ) );
			}

			if( isset( $options['child_of'] ) ) {
				$args['child_of'] = absint( $options['child_of'] );
			}

			if( isset( $options['limit'] ) ) {
				$args['number'] = absint( $options['limit'] );
			}

			$args['arrow_icon'] = $icon;

			$args['hide_empty'] = ( isset( $options['hide_empty'] ) && $options['hide_empty'] == "yes" ) ? true : false;
			$args['orderby'] = isset( $options['orderby'] ) ? $options['orderby'] : "name";
			$args['order'] = isset( $options['order'] ) ? $options['order'] : "ASC";

			$sm_html .= wp_list_categories( $args );

			$sm_html .= '</ul></nav>';
		}

		echo $sm_html;

		if( $this->isBuilderEditorActive() ) {
			$this->ou_sdmenu_script();
			$this->El->builderInlineJS("setTimeout(function(){jQuery(window).ready(ouSlidingMenu);},10);");
		} else {
			if( ! $this->sdm_js_added ) {
				add_action( 'wp_footer', array($this, 'ou_sdmenu_script') );
				$this->El->footerJS("document.addEventListener( 'DOMContentLoaded',ouSlidingMenu);");
				$this->sdm_js_added = true;
			}
		}
	}

	function get_unique_index() {
		return $this->unique_index++;
	}

	function ou_sdmenu_link_attributes( $atts, $item, $args, $depth ) {
		$classes = $depth ? 'ou-slide-menu-item-link ou-slide-menu-sub-item-link' : 'ou-slide-menu-item-link';

		if ( in_array( 'current-menu-item', $item->classes ) ) {
			$classes .= ' ou-slide-menu-item-link-current';
		}

		if ( empty( $atts['class'] ) ) {
			$atts['class'] = $classes;
		} else {
			$atts['class'] .= ' ' . $classes;
		}

		$atts['itemprop'] = 'url';
		if( isset( $item->title ) ) {
			$atts['data-title'] = esc_attr( $item->title );
		}

		return $atts;
	}

	function ou_sdmenu_submenu_css_class( $classes ) {
		$classes[] = 'ou-slide-menu-sub-menu';

		return $classes;
	}

	function ou_sdmenu_css_class( $classes ) {
		$classes[] = 'ou-slide-menu-item';

		if ( in_array( 'menu-item-has-children', $classes ) ) {
			$classes[] = 'ou-slide-menu-item-has-children';
		}

		if ( in_array( 'current-menu-item', $classes ) ) {
			$classes[] = 'ou-slide-menu-item-current';
		}

		return $classes;
	}

	function customCSS( $original, $selector ) {
		$css = '';
		if ( ! $this->sdm_css_added ){
			$css .= file_get_contents(__DIR__.'/'.basename(__FILE__, '.php').'.css');
			$this->sdm_css_added = true;
		}

		$prefix = $this->El->get_tag();
		if( isset( $original[$prefix . '_menu_effect_td'] ) ) {
			$css .= $selector . '{--ou-link-opacity:' . $original[$prefix . '_menu_effect_td'] . 's}';
		}

		return $css;
	}

	function ou_sdmenu_script() {
		global $ou_constant;

		$baseJS = '';
		
		if( ! $ou_constant['base_js'] ) {
			$baseJS = ouGetBaseJS();
			$ou_constant['base_js'] = true;
		}
	?>
	<script type="text/javascript">
		<?php echo $baseJS . "\n"; ?>
		function ouSlidingMenu(){var e=document.querySelectorAll(".oxy-ou-sliding-menu"),t=99;e.forEach(e=>{var s=e.querySelector("nav"),i=e.querySelector(".ou-slide-menu-comp"),a=i.querySelectorAll("li.ou-slide-menu-item-has-children > .ou-slide-menu-item-link"),r=i.offsetHeight,u=s.getAttribute("data-nav-height"),l=s.getAttribute("data-back-icon"),n=s.getAttribute("data-back-text");e.classList.contains("ct-component")||(i.style.height=r+"px"),a.forEach(e=>{var s=e.nextElementSibling,a=e.previousElementSibling;if(s){s.insertAdjacentHTML("afterbegin",'<li class="menu-item ou-slide-menu-item ou-slide-menu-back" aria-expanded="false" aria-pressed="false" aria-hidden="true"><span class="ou-slide-menu-back-arrow"><svg><use xlink:href="#FontAwesomeicon-angle-left"></use></svg></span><a href="#" class="ou-menu-sub-item-back">Back</a></li>');var o=s.querySelector(".ou-slide-menu-back");o.querySelector("use").setAttribute("xlink:href","#"+l),heading=e.getAttribute("data-title"),null!=heading||"undefied"!=typeof heading?o.querySelector(".ou-menu-sub-item-back").innerText=heading:o.querySelector(".ou-menu-sub-item-back").innerText=n.toString()}["click","touchstart"].forEach(l=>{var n=function(e){e.preventDefault(),e.stopPropagation();var a="false"==this.getAttribute("aria-expanded")?"true":"false",r="false"==this.getAttribute("aria-pressed")?"true":"false",l="true"==this.getAttribute("aria-hidden")?"false":"true";this.setAttribute("aria-expanded",a),this.setAttribute("aria-pressed",r),this.setAttribute("aria-hidden",l),(s=this.closest("li").querySelectorAll(".ou-slide-menu-sub-menu")[0]).classList.add("ou-slide-menu-is-active"),OxyUltimate.getParents(this,"ul")[0].classList.add("ou-slide-menu-is-active-parent"),i.classList.contains("ou-slide-completed")||i.classList.add("ou-slide-completed"),s.closest("li").classList.add("ou-slide-menu-li"),s.style.zIndex=t++,null!=u&&"auto"==u?i.style.height=s.offsetHeight+"px":s.style.height="100%"};a.addEventListener(l,n),"#"===e.getAttribute("href")&&e.addEventListener(l,n),o.addEventListener(l,function(e){e.preventDefault(),e.stopPropagation();var t=jQuery(this).closest("ul"),s=t.parents("ul").first();s.find(".ou-slide-menu-arrow").attr("aria-expanded","false"),s.find(".ou-slide-menu-arrow").attr("aria-pressed","false"),s.find(".ou-slide-menu-arrow").attr("aria-hidden","true"),t.removeClass("ou-slide-menu-is-active"),s.removeClass("ou-slide-menu-is-active-parent"),t.closest("li").removeClass("ou-slide-menu-li"),t.removeAttr("z-index"),i.classList.contains("ou-slide-menu-is-active-parent")?i.style.height=s.height()+"px":(i.style.height=r+"px",i.classList.remove("ou-slide-completed"))});var d=document.querySelector(".ou-slide-menu-item-current");if(d){var c=d.closest(".ou-slide-menu-item-has-children.current-menu-ancestor");c&&(ancestors=OxyUltimate.getParents(c,".current-menu-ancestor"),ancestors.forEach(e=>{ulTag=e.closest("ul"),ulTag.classList.add("ou-slide-menu-is-active","ou-slide-menu-is-active-parent"),ulTag.style.zIndex=t++,e.classList.add('ou-slide-menu-li')}),d.closest("ul.ou-slide-menu-comp").classList.remove("ou-slide-menu-is-active"),c.querySelector(".ou-slide-menu-arrow").click())}})})})}
	</script>
	<?php
	}

	function enableFullPresets() {
		return true;
	}
}

new OUSlidingMenu();

class OU_Sliding_Category_Walker extends \Walker_Category {

	public function start_lvl( &$output, $depth = 0, $args = array() ) {
        if ( 'list' !== $args['style'] ) {
            return;
        }
        
        $output .= "<ul class='sub-menu ou-slide-menu-sub-menu'>\n";
    }

	public function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
		$cat_name = apply_filters(
			'list_cats',
			esc_attr( $category->name ),
			$category
		);

		// Don't generate an element if the category name is empty.
		if ( ! $cat_name ) {
			return;
		}

		if ( 'list' == $args['style'] ) {

			$arrow = '';
			$link = '<a href="' . esc_url( get_term_link( $category ) ) . '" class="ou-slide-menu-item-link" itemprop="url" data-title="' . esc_attr( $cat_name ) . '">';
			$link .= '<span itemprop="name">' . $cat_name . '</span></a>';

			$output .= "\t<li";
			$css_classes = array(
				'menu-item',
				'cat-item',
				'cat-item-' . $category->term_id,
				'ou-slide-menu-item'
			);

			$termchildren = get_term_children( $category->term_id, $category->taxonomy );

			if( count($termchildren) > 0 ) {
				$css_classes[] =  'menu-item-has-children';
				$css_classes[] =  'ou-slide-menu-item-has-children';

				$arrow = '<span class="ou-slide-menu-arrow" aria-expanded="false" aria-pressed="false"><svg class="sm-toggle-icon"><use xlink:href="#' . $args['arrow_icon'] .'"></use></svg></span>';
			}

			if ( ! empty( $args['current_category'] ) ) {
				$_current_category = get_term( $args['current_category'], $category->taxonomy );
				if ( $category->term_id == $args['current_category'] ) {
					$css_classes[] = 'current-menu-item ou-slide-menu-item-current';
				} elseif ( $category->term_id == $_current_category->parent ) {
					$css_classes[] = 'current-menu-ancestor';
				}
			}

			$css_classes = implode( ' ', apply_filters( 'sliding_category_css_class', $css_classes, $category, $depth, $args ) );

			$output .=  ' class="' . $css_classes . '"';
			$output .= ">$arrow\n$link\n";
		} else {
            $output .= "\t$link<br />\n";
        }
	}
}