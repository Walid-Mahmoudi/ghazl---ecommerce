<?php

namespace Oxygen\OxyUltimate;

class OUAccordionMenu extends \OxyUltimateEl {
	public $am_css_added = false;
	public $am_js_added = false;

	public $menu_id_index = 1;

	function name() {
		return __( "Accordion Menu", "oxy-ultimate" );
	}

	function slug() {
		return "ou_accordion_menu";
	}

	function oxyu_button_place() {
		return "menu";
	}

	function config() {
		$config = $this->addControlSection( 'config_sec', __('Config', "oxy-ultimate"), "assets/icon.png", $this );

		$config->addOptionControl([
			'type' 	=> 'dropdown',
			'name' 	=> __( 'Source', "oxy-ultimate" ),
			'slug' 	=> 'acrd_source_menu',
			'value'	=> ['wpmenu' => __('WP Menu', "oxy-ultimate"), 'tax' => __('Taxonomy', "oxy-ultimate")],
			'default' => 'wpmenu'
		]);

		$config->addOptionControl([
			'type' 	=> 'dropdown',
			'name' 	=> __( 'Menu', "oxy-ultimate" ),
			'slug' 	=> 'acrd_menu',
			'value'	=> ouGetWPMeuns(),
			'default' => 'sel',
			'condition' => 'acrd_source_menu=wpmenu'
		])->rebuildElementOnChange();

		$config->addOptionControl([
			'type' 	=> 'dropdown',
			'name' 	=> __( 'Select Taxonomy', "oxy-ultimate" ),
			'slug' 	=> 'acrd_tax_name',
			'value'	=> ou_get_taxonomies(),
			'default' => 'category',
			'condition' => 'acrd_source_menu=tax'
		])->rebuildElementOnChange();

		$config->addOptionControl([
            'type'      => 'textfield',
            'name'      => __('Specific Categories Only(IDs)', 'oxy-ultimate'),
            'slug'      => 'include_ids',
            'condition' => 'acrd_source_menu=tax'
		]);
		
		$config->addOptionControl([
            'type'      => 'textfield',
            'name'      => __('Exclude Specific Categories(IDs)', 'oxy-ultimate'),
            'slug'      => 'exclude_ids',
            'condition' => 'acrd_source_menu=tax'
		]);

		$config->addOptionControl([
			"type" => 'buttons-list',
			"name" => 'Hide Empty',
			"slug" => 'hide_empty',
			"default" => "yes",
			'condition' => 'acrd_source_menu=tax'
        ])->setValue(array( 'yes' => __('Yes'), 'no' => __('No') ))->rebuildElementOnChange();

        $config->addOptionControl(
			array(
                "type" => 'textfield',
                "name" => 'Child Of',
                "slug" => 'child_of',
				'condition' => 'acrd_source_menu=tax'
            )
		)->rebuildElementOnChange();

        $config->addOptionControl(
			array(
                "type" => 'textfield',
				"name" => 'Limit',
				"slug" => 'limit',
				'condition' => 'acrd_source_menu=tax'
            )
		)->rebuildElementOnChange();

        $config->addOptionControl(
			array(
                "type" => 'buttons-list',
                "name" => 'Order',
				"slug" => 'order',
				"default" => "ASC",
				'condition' => 'acrd_source_menu=tax'
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
				'condition' => 'acrd_source_menu=tax'
            )
		)->rebuildElementOnChange();
	}

	function menutitle() {
		$title = $this->addControlSection( 'menu_title', __('Menu Title', "oxy-ultimate"), "assets/icon.png", $this);

		$selector = '.ou-acrd-menu-title';

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

	function menuitems() {
		$links = $this->addControlSection( 'links', __('Menu Items', "oxy-ultimate"), "assets/icon.png", $this );

		$selector = '.ou-menu-item-text';

		$spacing = $links->addControlSection( 'link_sp', __('Spacing', "oxy-ultimate"), "assets/icon.png", $this );
		$spacing->addPreset(
			"padding",
			"link_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$color = $links->addControlSection( 'item_color', __('Colors', "oxy-ultimate"), "assets/icon.png", $this );
		$color->addStyleControls([
			[
				'selector' 	=> '.menu-item a',
				'property' 	=> "background-color"
			],
			[
				'name' 		=> __('Background Color on Hover', "oxy-ultimate"),
				'selector' 	=> ".menu-item a:hover",
				'property' 	=> "background-color"
			],
			[
				'name' 		=> __('Background Color of Current Item', "oxy-ultimate"),
				'selector' 	=> ".current-menu-item > a, .current-menu-ancestor > a",
				'property' 	=> "background-color"
			],
			[
				'name' 		=> __('Color on Hover', "oxy-ultimate"),
				'selector' 	=> ".menu-item a:hover " . $selector,
				'property' 	=> "color"
			],
			[
				'name' 		=> __('Color of Current Item', "oxy-ultimate"),
				'selector' 	=> ".current-menu-item > a " . $selector . ", .current-menu-ancestor > a " . $selector,
				'property' 	=> "color"
			]
		]);

		$links->typographySection(__('Typography'), $selector, $this);

		$links->borderSection(__('Border'), '.menu-item a', $this);
		$links->borderSection(__('Hover Border'), ".menu-item a:hover", $this);
		$links->borderSection(__('Active Border'), ".current-menu-item > a, .current-menu-ancestor > a", $this);
	}

	function submenuitems() {
		$submenu = $this->addControlSection( 'submenu_sec', __('Sub Menu', "oxy-ultimate"), "assets/icon.png", $this );

		$submenu->addOptionControl([
			'type' 	=> 'radio',
			'name' 	=> __( 'Always closed on page load', "oxy-ultimate" ),
			'slug' 	=> 'close_sub_menu',
			'value'	=> ['no' => __('No'), 'Yes' => __('Yes')],
			'default' => 'no'
		]);

		$selector = '.ou-menu-item-text';

		$color = $submenu->addControlSection( 'sbitem_color', __('Colors', "oxy-ultimate"), "assets/icon.png", $this );
		$color->addStyleControls([
			[
				'selector' 	=> '.sub-menu,.sub-menu .menu-item a',
				'property' 	=> "background-color"
			],
			[
				'name' 		=> __('Background Color on Hover', "oxy-ultimate"),
				'selector' 	=> ".sub-menu .menu-item a:hover",
				'property' 	=> "background-color"
			],
			[
				'name' 		=> __('Background Color of Current Item', "oxy-ultimate"),
				'selector' 	=> ".sub-menu .current-menu-item > a,.sub-menu .current-menu-ancestor > a",
				'property' 	=> "background-color"
			],
			[
				'name' 		=> __('Color on Hover', "oxy-ultimate"),
				'selector' 	=> ".sub-menu .menu-item a:hover " . $selector,
				'property' 	=> "color"
			],
			[
				'name' 		=> __('Color of Current Item', "oxy-ultimate"),
				'selector' 	=> ".sub-menu .current-menu-item > a " . $selector . ", .sub-menu .current-menu-ancestor > a " . $selector,
				'property' 	=> "color"
			]
		]);

		$submenu->typographySection(__('Typography'), ".sub-menu " . $selector, $this);

		$txtIndent = $submenu->addOptionControl([
			'type' 		=> 'slider-measurebox',
			'name' 		=> __('Text Indent'),
			'slug' 		=> 'text_indent',
			'default' 	=> 10
		]);
		$txtIndent->setUnits('px','px');
		$txtIndent->setParam('description', __('Click on Apply Params button and apply changes.', "oxy-ultimate"));

		$submenu_td = $submenu->addOptionControl([
			'type' 	=> 'slider-measurebox',
			'name' 	=> __('Transition Duration for Sliding Effect', "oxy-ultimate"),
			'slug' 	=> 'submenu_td'
		]);
		$submenu_td->setRange('0','4000','50');
		$submenu_td->setUnits('ms','ms');
		$submenu_td->setDefaultValue( 400 );
		$submenu_td->setParam('description', __('Click on Apply Params button and apply changes.', "oxy-ultimate"));

		$spacing = $submenu->addControlSection( 'submenu_sp', __('Spacing', "oxy-ultimate"), "assets/icon.png", $this );
		$spacing->addPreset(
			"padding",
			"submenu_padding",
			__("Padding"),
			".sub-menu " . $selector
		)->whiteList();
	}

	function arrow() {
		$arrow = $this->addControlSection( 'arrow_sec', __('Arrow', "oxy-ultimate"), "assets/icon.png", $this );

		$selector = '.ou-menu-items-arrow';

		$icon = $arrow->addControlSection( 'icon_sec', __('Icon', "oxy-ultimate"), "assets/icon.png", $this );
		$icon->addOptionControl(
			array(
				"type" 		=> 'icon_finder',
				"name" 		=> __('Select Icon', "oxy-ultimate"),
				"slug" 		=> 'arrow_icon',
				"default" 	=> 'FontAwesomeicon-angle-down'
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
				'selector' 	=> "a:hover " . $selector,
				'property' 	=> "background-color",
				'slug' 		=> 'arrow_hover_bg'
			],
			[
				'name' 		=> __('Background Color of Current Item', "oxy-ultimate"),
				'selector' 	=> ".current-menu-item > a " . $selector . ", .current-menu-ancestor > a " . $selector . ',.ou-menu-items-arrow.acrd-menu-open',
				'property' 	=> "background-color",
				'slug' 		=> 'current_menu_item_arrow_bg'
			],
			[
				'name' 		=> __('Icon Color', "oxy-ultimate"),
				'selector' 	=> $selector . " svg",
				'property' 	=> "color"
			],
			[
				'name' 		=> __('Icon Color on Hover', "oxy-ultimate"),
				'selector' 	=> "a:hover " . $selector . " svg",
				'property' 	=> "color",
				'slug' 		=> 'arrow_hover_color'
			],
			[
				'name' 		=> __('Icon Color of Current Item', "oxy-ultimate"),
				'selector' 	=> ".current-menu-item > a " . $selector . " svg, .current-menu-ancestor > a " . $selector . " svg,.ou-menu-items-arrow.acrd-menu-open svg",
				'property' 	=> "color",
				'slug' 		=> 'current_menu_item_arrow_color'
			]
		]);

		$arrow->borderSection(__('Border'), $selector, $this);
		$arrow->borderSection(__('Hover Border'), "a:hover " . $selector, $this);
		$arrow->borderSection(__('Active Border'), ".current-menu-item > a " . $selector . ", .current-menu-ancestor > a " . $selector . ',.ou-menu-items-arrow.acrd-menu-open', $this);

		$effect = $arrow->addControlSection( 'effect', __('Effect', "oxy-ultimate"), "assets/icon.png", $this );

		$effect->addStyleControl([
			"control_type" 	=> 'slider-measurebox',
			"name" 			=> __('Initial Rotate Value', "oxy-ultimate"),
			'selector' 		=> '.ou-menu-items-arrow svg',
			'property' 		=> 'transform:rotate'
		])->setUnits('deg','deg')->setRange('-180','180');

		$effect->addOptionControl([
			'type' 	=> 'buttons-list',
			'name' 	=> __('Animation Type', "oxy-ultimate"),
			'slug' 	=> 'anim_type'
		])->setValue([
			'rotate' 	=> __('Rotate'),
			'flip' 		=> __('Vertical Flip', "oxy-ultimate")
		])->setValueCSS([
			'flip' => '.ou-menu-items-arrow.acrd-menu-open svg{transform:rotateX(-180deg)}'
		])->setDefaultValue('rotate');

		$effect->addStyleControl([
			"control_type" 	=> 'slider-measurebox',
			"name" 			=> __('Rotate When Sub Menu Open'),
			'selector' 		=> '.ou-menu-items-arrow.acrd-menu-open svg',
			'property' 		=> 'transform:rotate'
		])->setUnits('deg','deg')->setRange('-180','180')->setCondition('anim_type=rotate');

		$effect->addStyleControl([
			"control_type" 	=> 'slider-measurebox',
			'selector' 		=> '.ou-menu-items-arrow svg',
			'property' 		=> 'transition-duration'
		])->setUnits('s','sec')->setRange('0','2','0.1')->setDefaultValue('0.4');
	}

	function hoverAnimation() {
		$anim = $this->addControlSection( 'link_hvr', __('Hover Animation', "oxy-ultimate"), "assets/icon.png", $this );

		$anim->addStyleControl([
			'control_type' 	=> 'slider-measurebox',
			'selector' 		=> '.menu-item a, .ou-menu-items-arrow, .ou-menu-item-text',
			'property' 		=> 'transition-duration',
			'slug' 			=> 'hovr_link_td'
		])->setUnits('s', 'sec')->setRange(0,3,0.1);

		$anim->addStyleControl([
			'name' 			=> __('Slide from Left Side(translateX)', "oxy-ultimate"),
			'control_type' 	=> 'slider-measurebox',
			'selector' 		=> ' ',
			'property' 		=> '--menu-item-translatex'
		])->setUnits("px","px")->setDefaultValue(10);
	}

	function controls() {
		$this->config();

		$this->menutitle();

		$this->menuitems();

		$this->submenuitems();

		$this->arrow();

		$this->hoverAnimation();
	}

	function render( $options, $defaults, $content ) {
		$datattr = array();

		global $oxygen_svg_icons_to_load;

		$icon = isset( $options['arrow_icon'] ) ? esc_attr($options['arrow_icon']) : "FontAwesomeicon-angle-down";
		$oxygen_svg_icons_to_load[] = $icon;
		$datattr[] = 'data-sub-menu-icon="' . $icon . '"';

		$text_indent = isset( $options['text_indent'] ) ? intval($options['text_indent']) : 10;
		$submenu_td = isset( $options['submenu_td'] ) ? intval($options['submenu_td']) : 400;
		$datattr[] = 'data-toggle-duration="' . intval($submenu_td) . '"';
		$datattr[] = 'data-closed-submenu="' . $options['close_sub_menu'] . '"';

		$menuSource = isset($options['acrd_source_menu']) ? $options['acrd_source_menu'] : 'wpmenu';
		if( $menuSource == 'wpmenu' ) {
			$acrd_menu = isset( $options['acrd_menu'] ) ? $options['acrd_menu'] : 'sel';
			if( $acrd_menu == 'sel' || $acrd_menu == 'nomenu' ) {
				echo __('Select Menu', "oxy-ultimate");
				return;
			}

			$display_title = isset($options['display_menu_title']) ? $options['display_menu_title'] : 'no';
			if( $display_title == 'yes') {
				$tag = isset($options['menu_title_tag']) ? $options['menu_title_tag'] : 'h4';
				echo '<' . $tag . ' class="ou-acrd-menu-title">'. wp_get_nav_menu_object($acrd_menu)->name . '</' . $tag .'>';
			}

			$args = array(
				'echo'        => false,
				'menu'        => $acrd_menu,
				'menu_class'  => 'ou-acrd-menu-items',
				'menu_id'     => 'menu-' . $this->get_menu_id() . $options['selector'],
				'link_before' => '<span itemprop="name" class="ou-menu-item-text">',
				'link_after'  => '</span>',
				'container'   => '',
				'text_indent' => $text_indent
			);

			add_filter( 'nav_menu_item_args', array( $this, 'ou_acrd_menu_items_args'), 10, 3 );
			add_filter( 'nav_menu_link_attributes', array( $this, 'ou_acrdmenu_link_attributes' ), 10, 4 );

			$menu = '<nav itemscope="" itemtype="https://schema.org/SiteNavigationElement" '. implode(" ", $datattr) .'>';
			$menu .= wp_nav_menu( $args );
			$menu .= '</nav>';

			echo $menu;

			remove_filter( 'nav_menu_link_attributes', array( $this, 'ou_acrdmenu_link_attributes' ), 10, 4 );
			remove_filter( 'nav_menu_item_args', array( $this, 'ou_acrd_menu_items_args'), 10, 3 );
		}

		/**
		 * Taxonomy
		 */
		if( $menuSource == 'tax' ) {
			$menu = '<nav itemscope="" itemtype="https://schema.org/SiteNavigationElement" '. implode(" ", $datattr) .'>';
			$menu .= '<ul id="menu-' . $this->get_menu_id() . $options['selector'] .'" class="ou-acrd-menu-items">';

			$taxonomy = isset( $options['acrd_tax_name'] ) ? $options['acrd_tax_name'] : 'category';

			$args = array(
				'show_option_all'    => '',
				'style'              => 'list',
				'show_count'         => 0,
				'hide_empty'         => 1,
				'hierarchical'       => 1,
				'title_li'           => '',
				'show_option_none'   => '',
				'number'             => null,
				'echo'               => 0,
				'depth'              => 0,
				'current_category'   => 0,
				'pad_counts'         => 0,
				'taxonomy'           => $taxonomy,
				'text_indent' 		 => $text_indent,
				'walker'             => new OU_Accordion_Category_Walker,
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

			$args['hide_empty'] = ( isset( $options['hide_empty'] ) && $options['hide_empty'] == "yes" ) ? true : false;
			$args['orderby'] = isset( $options['orderby'] ) ? $options['orderby'] : "name";
			$args['order'] = isset( $options['order'] ) ? $options['order'] : "ASC";

			$menu .= wp_list_categories( $args );

			$menu .= '</ul></nav>';

			echo $menu;
		}

		if( $this->isBuilderEditorActive() ) {
			$this->ou_accordion_script();
			$this->El->builderInlineJS("jQuery(window).ready(ouAccordionMenu);");
		} else {
			if( ! $this->am_js_added ) {
				add_action( 'wp_footer', array($this, 'ou_accordion_script') );
				$this->El->footerJS("document.addEventListener( 'DOMContentLoaded', ouAccordionMenu );");
				$this->am_js_added = true;
			}
		}
	}

	function customCSS( $original, $selector ) {
		$css = '';

		if( ! $this->am_css_added ) {
			$css .= '.oxy-ou-accordion-menu {
					display: block;
					min-height: 40px;
					width: 100%;

					--menu-item-translatex: 10px;
				}
				.oxy-ou-accordion-menu a,
				.oxy-ou-accordion-menu a:hover {
					text-decoration: none;
					position: relative;
					z-index: 10;
				}
				.oxy-ou-accordion-menu a:hover > span.ou-menu-item-text {
					-webkit-transform: translateX(var(--menu-item-translatex));
					-ms-transform: translateX(var(--menu-item-translatex));
					transform: translateX(var(--menu-item-translatex));
				}
				.oxy-ou-accordion-menu ul {
					margin: 0;
					padding: 0;
				}
				.oxy-ou-accordion-menu .menu-item,
				.oxy-ou-accordion-menu .ou-acrd-menu-title {
					display: inline-block;
					width: 100%;
				}
				.oxy-ou-accordion-menu .menu-item a {
					border-bottom: 1px solid rgba(172, 170, 170, 0.2);
					display: flex;
					-ms-flex-align: stretch;
					align-items: stretch;
					width: 100%;
				}
				.oxy-ou-accordion-menu .ou-menu-item-text {
					color: #333;
					display: inline-block;
					-ms-flex-positive: 1;
					flex-grow: 1;
					padding: 10px 12px;
					overflow: hidden;

					-ms-transform: translateX(0);
					-webkit-transform: translateX(0);
					transform: translateX(0);
					-ms-transition: transform 0.4s linear;
					-webkit-transition: transform 0.4s linear;
					transition: transform 0.4s linear;
				}
				.oxy-ou-accordion-menu .ou-menu-items-arrow {
					border-left: 1px solid rgba(172, 170, 170, 0.2);
					color: #aaa;
					cursor: pointer;
					width: 50px;
					display: flex;
					align-items: center;
					justify-content: center;
					position: relative;
    				z-index: 2;
				}
				.oxy-ou-accordion-menu .menu-item a,
				.oxy-ou-accordion-menu .ou-menu-items-arrow {
					-ms-transition: all 0.4s linear;
					-webkit-transition: all 0.4s linear;
					transition: all 0.4s linear;
				}
				.oxy-ou-accordion-menu .ou-menu-items-arrow svg {
					width: 25px;
					height: 25px;
					fill: currentColor;

					-ms-transition: transform 0.4s ease;
					-webkit-transition: transform 0.4s ease;
					transition: transform 0.4s ease;
				}
				.oxy-ou-accordion-menu .ou-menu-items-arrow.acrd-menu-open svg {
					transform: rotate(-180deg);
				}
				.oxy-ou-accordion-menu a:hover,
				.oxy-ou-accordion-menu .current-menu-item > a,
				.oxy-ou-accordion-menu .current-menu-ancestor > a {
					background-color: #f3f3f3;
				}
				.oxy-ou-accordion-menu a:hover .ou-menu-items-arrow,
				.oxy-ou-accordion-menu .current-menu-item > a .ou-menu-items-arrow,
				.oxy-ou-accordion-menu .current-menu-ancestor > a .ou-menu-items-arrow,
				.oxy-ou-accordion-menu .ou-menu-items-arrow.acrd-menu-open {
					background-color: #f7f7f7;
				}
				.oxy-ou-accordion-menu .sub-menu {
					display: none;
					transition-timing-function: linear;
				}
				.oxy-ou-accordion-menu .ou-acrd-menu-title {
					background-color: #f9f9f9;
					text-align: center;
					font-size: 18px;
					padding: 10px 12px;
				}
				';

			$this->am_css_added = true;
		}

		return $css;
	}

	function ou_accordion_script() {
		global $ou_constant;

		$baseJS = '';
		
		if( ! $ou_constant['base_js'] ) {
			$baseJS = ouGetBaseJS();
			$ou_constant['base_js'] = true;
		}
	?>
		<script type="text/javascript">
			<?php echo $baseJS . "\n"; ?>
			function ouAccordionMenu() {
				var acrdmenus = document.querySelectorAll('.oxy-ou-accordion-menu'),
					downButton = '<span class="ou-menu-items-arrow" aria-expanded="false" aria-pressed="false" aria-hidden="true"><svg class="acrdm-toggle-icon"><use xlink:href="#FontAwesomeicon-angle-down"></use></svg></span>',
					intval = null;
				acrdmenus.forEach(( acrdmenu ) => {
					var nav = acrdmenu.querySelector('nav'),
						arrow = nav.getAttribute('data-sub-menu-icon'),
						duration = nav.getAttribute('data-toggle-duration'),
						closedSubmenu = nav.getAttribute('data-closed-submenu')||'no',
						links = nav.querySelectorAll('.menu-item-has-children > a');

					links.forEach((link) => {
						if( typeof arrow != "undefined" || arrow != null ) {
							link.insertAdjacentHTML( 'beforeend', downButton );

							var arrowBtn = link.querySelector('.ou-menu-items-arrow');
							arrowBtn.querySelector('use').setAttribute('xlink:href', '#' + arrow);
							arrowBtn.setAttribute('aria-label', 'Sub Menu of ' + link.getAttribute('data-title'));
						}

						["click", "touchstart"].forEach((event) => {
							var expandSubMenuItems = function(e) {
								e.stopPropagation();
								e.preventDefault();

								var expended = this.getAttribute("aria-expanded") == 'false' ? 'true' : 'false'; 
								var pressed = this.getAttribute("aria-pressed") == 'false' ? 'true' : 'false';
								var hidden = this.getAttribute("aria-hidden") == 'true' ? 'false' : 'true'; 

								this.setAttribute( "aria-expanded", expended );
								this.setAttribute( "aria-pressed", pressed );
								this.setAttribute( "aria-hidden", hidden );

								var hasChild = this.closest('li.menu-item-has-children'),
									subMenuItem = hasChild.querySelectorAll('.sub-menu')[0];

								OxyUltimate.slideToggle(subMenuItem, parseInt(duration));

								if( hidden == "false" ) {
									this.classList.add('acrd-menu-open');
								} else {
									this.classList.remove('acrd-menu-open');
								}
							}

							var expandHashLinkSubMenuItems = function (e) {
								e.stopPropagation();
								e.preventDefault();

								arrowBtn = this.querySelector('.ou-menu-items-arrow');
								arrowBtn.click();
							}

							if( link.getAttribute('href') === "#" ) {
								link.addEventListener(event,expandHashLinkSubMenuItems);
							}

							arrowBtn.addEventListener(event,expandSubMenuItems);
						});
					});

					currentItems = nav.querySelectorAll('.current-menu-item');
					if(currentItems && closedSubmenu == 'no') {
						currentItems.forEach((currentItem) => {
							//var ancestors = OxyUltimate.getParents(currentItem, '.sub-menu');
							var ancestors = OxyUltimate.getParents(currentItem, '.current-menu-ancestor, .menu-item-has-children');
							if(ancestors) {
								ancestors.forEach((ancestor) => {
									/*OxyUltimate.slideToggle(ancestor, 700);*/
									link = ancestor.querySelector('a');
									arrowBtn = link.closest('.menu-item-has-children > a').querySelector('.ou-menu-items-arrow');
									arrowBtn.click();
								});
							}
						});
					}
				});
			}
		</script>
	<?php
	}

	function ou_acrd_menu_items_args( $args, $item, $depth ) {
		$rp_item = '<span style="margin-left:' . ( $depth * $args->text_indent ). 'px;"';

		$args->link_before = str_replace( '<span', $rp_item, $args->link_before );

		return $args;
	}

	function ou_acrdmenu_link_attributes( $atts, $item, $args, $depth ) {
		$atts['itemprop'] = 'url';
		if( isset( $item->title ) ) {
			$atts['data-title'] = esc_attr( $item->title );
		}

		return $atts;
	}

	function get_menu_id() {
		return $this->menu_id_index++;
	}

	function enableFullPresets() {
		return true;
	}
}

new OUAccordionMenu();

class OU_Accordion_Category_Walker extends \Walker_Category {

	public function start_lvl( &$output, $depth = 0, $args = array() ) {
        if ( 'list' !== $args['style'] ) {
            return;
        }
 
        $output .= "<ul class='sub-menu'>\n";
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
			$link = '<a href="' . esc_url( get_term_link( $category ) ) . '" itemprop="url" data-title="' . esc_attr( $cat_name ) . '">';
			$link .= '<span itemprop="name" class="ou-menu-item-text" style="margin-left:' . ( $depth * $args['text_indent'] ). 'px;">' . $cat_name . '</span></a>';

			$output .= "\t<li";
			$css_classes = array(
				'menu-item',
				'cat-item',
				'cat-item-' . $category->term_id
			);

			$termchildren = get_term_children( $category->term_id, $category->taxonomy );

			if( count($termchildren) > 0 ) {
				$css_classes[] = 'menu-item-has-children';
			}

			if ( ! empty( $args['current_category'] ) ) {
				$_current_category = get_term( $args['current_category'], $category->taxonomy );
				if ( $category->term_id == $args['current_category'] ) {
					$css_classes[] = 'current-menu-item';
				} elseif ( $category->term_id == $_current_category->parent ) {
					$css_classes[] = 'current-menu-ancestor';
				}
			}

			$css_classes = implode( ' ', apply_filters( 'acrd_category_css_class', $css_classes, $category, $depth, $args ) );

			$output .= ' class="' . $css_classes . '"';
			$output .= ">$arrow\n$link\n";
		} else {
            $output .= "\t$link<br />\n";
        }
	}
}
