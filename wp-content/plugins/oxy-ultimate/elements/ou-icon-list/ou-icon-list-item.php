<?php

namespace Oxygen\OxyUltimate;

Class OUIconListItem extends \OxyUltimateEl {
	public $css_added = false;

	function name() {
		return __( "Add List Item", 'oxy-ultimate' );
	}

	function slug() {
		return "ouli_additem";
	}

	function tag() {
		return [ 'default' => 'li', 'choices' => 'li'];
	}

	function button_place() {
		return "ouiconli::comp";
	}

	function init() {
		$this->El->useAJAXControls();
		add_action("ct_toolbar_component_settings", function() {
		?>
			<label class="oxygen-control-label oxy-ouli_additem-elements-label"
				ng-if="isActiveName('oxy-ouli_additem')&&!hasOpenTabs('oxy-ouli_additem')" style="text-align: center; margin-top: 15px;">
				<?php _e("Available Elements","oxy-ultimate"); ?>
			</label>
			<div class="oxygen-control-row oxy-ouli_additem-elements"
				ng-if="isActiveName('oxy-ouli_additem')&&!hasOpenTabs('oxy-ouli_additem')">
				<?php do_action("oxygen_add_plus_ouiconli_comp"); ?>
			</div>
		<?php }, 20 );
	}

	function icon() {
		return OXYU_URL . 'assets/images/elements/ou-icon-list.svg';
	}

	function controls() {
		$text = $this->addCustomControl(
			'<div class="oxygen-input " ng-class="{\'oxygen-option-default\':iframeScope.isInherited(iframeScope.component.active.id, \'oxy-ouli_additem_list_text\')}">
				<input type="text" spellcheck="false" ng-model="iframeScope.component.options[iframeScope.component.active.id][\'model\'][\'oxy-ouli_additem_list_text\']" ng-model-options="{ debounce: 10 }" ng-change="iframeScope.setOption(iframeScope.component.active.id,\'oxy-ouli_additem\',\'oxy-ouli_additem_list_text\');" class="ng-pristine ng-valid ng-touched" ng-keydown="$event.keyCode === 13 && iframeScope.rebuildDOM(iframeScope.component.active.id)">
				<div class="oxygen-dynamic-data-browse ng-isolate-scope" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.ouDynamicOULICText">data</div>
			</div>',
			'li_text'
		);
		$text->setParam( 'heading', __('Text') );
		$text->setParam( 'base64', true );
		$text->setParam( 'description', __('You will hit on Enter button to see the text on the Builder Editor. Enter <span style="color:#ff7171;">&amp;apos;</span> for single quote.', "oxy-ultimate") );

		$txtTag = $this->addOptionControl(
			array(
				"type" 		=> 'dropdown',
				"name" 		=> __('Text Wrap By', "oxy-ultimate"),
				"slug" 		=> 'oulitxt_tag',
				"value" 	=> array(
					'h1' 	=> __('H1', "oxy-ultimate"),
					'h2' 	=> __('H2', "oxy-ultimate"),
					'h3' 	=> __('H3', "oxy-ultimate"),
					'h4' 	=> __('H4', "oxy-ultimate"),
					'h5' 	=> __('H5', "oxy-ultimate"),
					'h6' 	=> __('H6', "oxy-ultimate"),
					'div' 	=> __('div', "oxy-ultimate"),
					'p' 	=> __('p', "oxy-ultimate"),
					'span' 	=> __('span', "oxy-ultimate"),
				),
				"default" 	=> 'span',
				"css" 		=> false
			)
		);
		$txtTag->rebuildElementOnChange();

		$URL = $this->addCustomControl(
			'<div class="oxygen-file-input oxygen-option-default" ng-class="{\'oxygen-option-default\':iframeScope.isInherited(iframeScope.component.active.id, \'oxy-ouli_additem_url\')}">
				<input type="text" spellcheck="false" ng-model="iframeScope.component.options[iframeScope.component.active.id][\'model\'][\'oxy-ouli_additem_url\']" ng-model-options="{ debounce: 10 }" ng-change="iframeScope.setOption(iframeScope.component.active.id, iframeScope.component.active.name,\'oxy-ouli_additem_url\');iframeScope.checkResizeBoxOptions(\'oxy-ouli_additem_url\'); " class="ng-pristine ng-valid ng-touched" placeholder="http://">
				<div class="oxygen-set-link" data-linkproperty="url" data-linktarget="target" onclick="processOULink(\'oxy-ouli_additem_url\')">set</div>
				<div class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesLinkMode" callback="iframeScope.ouDynamicOULIUrl">data</div>
			</div>
			',
			"ouli_url"
		);
		$URL->setParam( 'heading', __('Link') );
		$URL->setParam( 'css', false );
		$URL->rebuildElementOnChange();

		$this->addOptionControl(
			array(
				"name" 			=> __('Target', "oxy-ultimate"),
				"slug" 			=> "link_target",
				"type" 			=> 'radio',
				"value" 		=> ["_self" => __("Same Window") , "_blank" => __("New Window")],
				"default"		=> "_self",
				'css' 			=> false
			)
		);

		$icon = $this->addControlSection('icon_section', __('Icon'), 'assets/icon.png', $this );
		$showIcon = $icon->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Display Icon', "oxy-ultimate"),
			"slug" 		=> 'ouli_show_icon',
			'value' 	=> ['yes' => __('Yes'), 'no' => __('No')],
			'default' 	=> 'yes',
			'css' 		=> false
		]);
		$showIcon->rebuildElementOnChange();

		$liicon = $icon->addOptionControl(
			array(
				"type" 			=> 'icon_finder',
				"name" 			=> __('Icon', "oxy-ultimate"),
				"slug" 			=> 'ouli_icon',
				"value" 		=> 'FontAwesomeicon-check',
				"default" 		=> 'FontAwesomeicon-check',
				'condition' 	=> 'ouli_show_icon=yes',
				'css' 			=> false
			)
		);
		$liicon->rebuildElementOnChange();

		/***************************
		Wrapper Style
		***************************/
		$liStyle = $this->addControlSection( 'lis_style', __('Wrapper Style'), 'assets/icon.png', $this );

		$liStyle->addCustomControl('<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 14px;">You can style the individual list item. It will override the general style.</div>', 'oulit_desc');

		$li_selector = 'div.li-inner-wrap';

		$liStyle->addStyleControls([
			[
				'selector' 	=> $li_selector,
				'property' 	=> 'background-color'
			],
			[
				'name' 		=> __('Hover Background Color'),
				'selector' 	=> $li_selector . ':hover',
				'property' 	=> 'background-color'
			],
			[
				'name' 		=> __('Hover Border Color'),
				'selector' 	=> $li_selector . ':hover',
				'property' 	=> 'border-color'
			]
		]);

		$liStyle->borderSection( __('Border'), $li_selector, $this );

		
		/***************************
		Text Style
		***************************/
		$textStyle = $this->addControlSection( 'oulitxts_style', __('Text Style'), 'assets/icon.png', $this );
		
		$textStyle->addCustomControl('<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 14px;">You can style the individual list item. It will override the general style.</div>', 'txt_desc');

		$selector = 'span.ou-list-text';

		$txtSpacing = $textStyle->addControlSection( 'litxt_spacing', __('Spacing'), 'assets/icon.png', $this );
		$txtSpacing->addPreset(
			"padding",
			"lictxt_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$txtSpacing->addPreset(
			"margin",
			"lictxt_margin",
			__("Margin"),
			$selector
		)->whiteList();

		$ttg = $textStyle->typographySection(__('Typography'), $selector . ', li div > a', $this );
		$ttg->addStyleControls([
			[
				'selector' 	=> $selector,
				'property' 	=> 'background-color'
			],
			[
				'name' 		=> __('Hover Background Color'),
				'selector' 	=> 'div:hover ' . $selector,
				'property' 	=> 'background-color'
			],
			[
				'name' 		=> __('Hover Text Color'),
				'selector' 	=> 'div:hover ' . $selector . ', li div:hover > a',
				'property' 	=> 'color'
			]
		]);


		/***************************
		Icon Style
		***************************/
		$iconStyle = $icon->addControlSection( 'ouicons_style', __('Style'), 'assets/icon.png', $this );
		$iconStyle->addCustomControl('<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 14px;">You can style the individual list item. It will override the general style.</div>', 'lic_desc');

		$selector ='span.ou-list-icon-wrap';
		$iconStyle->addPreset(
			"padding",
			"liiconsp_padding",
			__("Padding"),
			$selector
		)->whiteList();

		$iconStyle->addPreset(
			"margin",
			"liiconsp_margin",
			__("Margin"),
			$selector
		)->whiteList();

		$iconStyle->addStyleControl(
			array(
				"name" 			=> __('Icon Size', "oxy-ultimate"),
				"slug" 			=> "oulic_size",
				"selector" 		=> 'svg.ou-list-icon',
				"control_type" 	=> 'slider-measurebox',
				"value" 		=> '20',
				"property" 		=> 'width|height'
			)
		)
		->setRange(20, 80, 5)
		->setUnits("px", "px");

		$iconStyle->addStyleControls([
			[
				'selector' 		=> $selector,
				'property' 		=> 'width',
				"control_type" 	=> 'slider-measurebox',
				'units' 		=> 'px'
			],
			[
				'selector' 		=> $selector,
				'property' 		=> 'height',
				"control_type" 	=> 'slider-measurebox',
				'units' 		=> 'px'
			],
			[
				'selector' 		=> $selector,
				'property' 		=> 'line-height',
				'default' 		=> '2',
				'description' 	=> __('Using it, you can vertically center align the icon.')
			],
			[
				'selector' 		=> $selector,
				'property' 		=> 'background-color'
			],
			[
				'selector' 		=> $selector . ' svg',
				'property' 		=> 'color'
			],
			[
				'name' 		=> __('Hover Background Color'),
				'selector' 		=> 'div:hover ' . $selector,
				'property' 		=> 'background-color'
			],
			[
				'name' 		=> __('Hover Text Color'),
				'selector' 		=> 'div:hover ' . $selector . ' svg',
				'property' 		=> 'color'
			]
		]);

		$icon->borderSection( __('Border'), $selector, $this );

		$this->addTagControl();
	}

	function fetchDynamicData( $field ) {
		if( strstr( $field, 'oudata_') ) {
			$field = base64_decode( str_replace( 'oudata_', '', $field ) );
			$shortcode = ougssig( $this->El, $field );
			$field = do_shortcode( $shortcode );
		} elseif( strstr( $field, '[oxygen') ) {
			$shortcode = ct_sign_oxy_dynamic_shortcode(array($field));
			$field = do_shortcode($shortcode);
		}

		return $field;
	}

	function render( $options, $defaults, $content ) {
		if( isset( $options['list_text'] ) ) {

			echo '<div class="li-inner-wrap">';

			$liText = $this->fetchDynamicData( $options['list_text'] );

			if( isset( $options['url'] ) ) {
				$url = $this->fetchDynamicData( $options['url'] );

				echo '<a href="' . esc_url( $url ) . '" target="' . $options['link_target'] . '">';
			}

			if( isset( $options['ouli_show_icon']) && $options['ouli_show_icon'] === 'yes' && isset( $options['ouli_icon'] ) ) {
				global $oxygen_svg_icons_to_load;
				$oxygen_svg_icons_to_load[] = $options['ouli_icon'];

				$ouli_icon = '<svg id="' . $options['selector'] . '-li-icon" class="ou-list-icon"><use xlink:href="#' . $options['ouli_icon'] . '"></use></svg>';

				echo '<span class="ou-list-icon-wrap">' . $ouli_icon . '</span>';
			}

			echo '<' . $options["oulitxt_tag"] . ' class="ou-list-text">' . $liText . '</' . $options["oulitxt_tag"] . '>';

			if( isset( $options['url'] ) ) {
				echo '</a>';
			}

			echo '</div>';

		}
	}

	function customCSS( $original, $selector ) {
		$css = '';
		if( ! $this->css_added ) {
			$css .= '.ou-icon-list-ul li{display: flex; list-style-type: none; list-style: none; vertical-align: top;}
					.ou-icon-list-ul li div, li div a {display: flex; width: 100%; align-items: center;transition: all 0.1s ease;}
					.ou-icon-list-ul li:first-child{margin-left: 0;}.ou-icon-list-ul li:last-child{margin-right: 0; margin-bottom: 0;}
					.ou-list-icon-wrap,.ou-list-text{display:inline-block;float: left;}
					.ou-list-icon-wrap{margin-right: 10px;text-align:center;}
					.ou-list-icon-wrap svg{width: 20px; height: 20px; fill: currentColor;}';
			$this->css_added = true;
		}

		return $css;
	}
}

new OUIconListItem();