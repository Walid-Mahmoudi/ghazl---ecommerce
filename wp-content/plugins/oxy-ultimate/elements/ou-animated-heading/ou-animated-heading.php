<?php
namespace Oxygen\OxyUltimate;

Class OUAnimatedHeading extends \OxyUltimateEl {
	public $has_js = true;
	public $css_added = false;
	private $animh_js = '';

	function name() {
		return __( "Animated Heading", 'oxy-ultimate' );
	}

	function slug() {
		return "ou_animated_heading";
	}

	function oxyu_button_place() {
		return "text";
	}

	function icon() {
		return OXYU_URL . 'assets/images/elements/' . basename(__FILE__, '.php').'.svg';
	}

	function controls() {
		$this->addOptionControl(
			array(
				"name" 			=> __('Preview', "oxy-ultimate"),
				"slug" 			=> "anihm_preview",
				"type" 			=> 'radio',
				"value" 		=> ["yes" => __("Enable") , "no" => __("Disable")],
				"default"		=> "yes"
			)
		)->rebuildElementOnChange();

		$ahtag = $this->addOptionControl(
			array(
				"type" 		=> 'dropdown',
				"name" 		=> __('Heading Tag', "oxy-ultimate"),
				"slug" 		=> 'ouah_tag',
				"value" 	=> array(
					'h1' => __('H1', "oxy-ultimate"),
					'h2' => __('H2', "oxy-ultimate"),
					'h3' => __('H3', "oxy-ultimate"),
					'h4' => __('H4', "oxy-ultimate"),
					'h5' => __('H5', "oxy-ultimate"),
					'h6' => __('H6', "oxy-ultimate"),
					'div' => __('DIV', "oxy-ultimate"),
				),
				"default" 	=> 'h2',
				"css" 		=> false
			)
		);
		$ahtag->rebuildElementOnChange();

		$ahbt = $this->addOptionControl(
			array(
				"type" 		=> 'textfield',
				"name" 		=> __('Before Text', "oxy-ultimate"),
				"slug" 		=> 'ouah_before_text',
				"base64" 	=> true
			)
		);
		$ahbt->setParam('dynamicdatacode', '<div class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.ouDynamicAHBText">data</div>');
		$ahbt->setParam("description", __("This text will be placed before the animated text.", "oxy-ultimate"));
		$ahbt->rebuildElementOnChange();

		$ahanmt = $this->addOptionControl(
			array(
				"type" 		=> 'textfield',
				"name" 		=> __('Animated Text', "oxy-ultimate"),
				"slug" 		=> 'ouah_animated_text',
				"value" 	=> __("Animated|Rotating", "oxy-ultimate"),
				"base64" 	=> true
			)
		);
		$ahanmt->setParam('dynamicdatacode', '<div class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.ouDynamicAHText">data</div>');
		$ahanmt->setParam("description", __("Use pipeline('|') separator to add the multiple text.", "oxy-ultimate"));
		$ahanmt->rebuildElementOnChange();

		$ahaft = $this->addOptionControl(
			array(
				"type" 		=> 'textfield',
				"name" 		=> __('After Text', "oxy-ultimate"),
				"slug" 		=> 'ouah_after_text',
				"base64" 	=> true
			)
		);
		$ahaft->setParam('dynamicdatacode', '<div class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesContentMode" callback="iframeScope.ouDynamicAHFText">data</div>');
		$ahaft->setParam("description", __("This text will be placed after the animated text.", "oxy-ultimate"));
		$ahaft->rebuildElementOnChange();


		$link = $this->addControlSection( "anim_link", __('Link'), 'assets/icon.png', $this );
		$textLink = $link->addCustomControl(
			'<div class="oxygen-file-input oxygen-option-default" ng-class="{\'oxygen-option-default\':iframeScope.isInherited(iframeScope.component.active.id, \'oxy-ou_animated_heading_text_link\')}">
				<input type="text" spellcheck="false" ng-model="iframeScope.component.options[iframeScope.component.active.id][\'model\'][\'oxy-ou_animated_heading_text_link\']" ng-model-options="{ debounce: 10 }" ng-change="iframeScope.setOption(iframeScope.component.active.id, iframeScope.component.active.name,\'oxy-ou_animated_heading_text_link\');iframeScope.checkResizeBoxOptions(\'oxy-ou_animated_heading_text_link\'); " class="ng-pristine ng-valid ng-touched" placeholder="http://">
				<div class="oxygen-set-link" data-linkproperty="url" data-linktarget="target" onclick="processOULink(\'oxy-ou_animated_heading_text_link\')">set</div>
				<div class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesLinkMode" callback="iframeScope.ouDynamicAHLUrl">data</div>
			</div>
			',
			"text_link"
		);
		$textLink->setParam('heading', __('Animated Text Link URL', "oxy-ultimate"));

		$link->addOptionControl(
			array(
				"name" 			=> __('Target', "oxy-ultimate"),
				"slug" 			=> "link_target",
				"type" 			=> 'radio',
				"value" 		=> ["_self" => __("Same Window") , "_blank" => __("New Window")],
				"default"		=> "_self"
			)
		);


		$anim = $this->addControlSection( "anim_speed", __('Animation'), 'assets/icon.png', $this );
		$anim_type = $anim->addOptionControl(
			array(
				'type' 		=> 'dropdown',
				'name' 		=> __('Type' , "oxy-ultimate"),
				'slug' 		=> 'ouah_animtype',
				'value' 	=> array(
					"clip" 			=> __("Clip", 'oxy-ultimate'),
					"loading-bar" 	=> __("Loading Bar", 'oxy-ultimate'),		
					"push" 			=> __("Push", 'oxy-ultimate'),
					"rotate-1" 		=> __("Rotate 1", 'oxy-ultimate'),
					"rotate-2" 		=> __("Rotate 2", 'oxy-ultimate'),
					"rotate-3" 		=> __("Rotate 3", 'oxy-ultimate'),
					"scale" 		=> __("Scale", 'oxy-ultimate'),
					"slide" 		=> __("Slide", 'oxy-ultimate'),	
					"type" 			=> __("Typing", 'oxy-ultimate'),
					"zoom" 			=> __("Zoom", 'oxy-ultimate')
				),
				'default' 	=> "clip",
				"css" 		=> false
			)
		);
		$anim_type->rebuildElementOnChange();

		//* Clip Animatio
		$anim->addStyleControl([
			'selector' 		=> '.cd-headline.clip .cd-words-wrapper::after', 
			'property' 		=> 'background-color',
			'name' 			=> __('Vertical Line Color', "oxy-ultimate"),
			'condition' 	=> 'ouah_animtype=clip'
		]);
		$anim->addStyleControl([
			'selector' 		=> '.cd-headline.clip .cd-words-wrapper::after', 
			'property' 		=> 'width',
			'name' 			=> __('Vertical Line Width', "oxy-ultimate"),
			'condition' 	=> 'ouah_animtype=clip',
			'control_type' 	=> 'slider-measurebox'
		])->setUnits('px', 'px,em');
		

		//*Type Animation
		$anim->addStyleControl([
			'selector' 		=> '.cd-headline.type .cd-words-wrapper::after', 
			'property' 		=> 'background-color',
			'name' 			=> __('Vertical Line Color(Blinking Line)', "oxy-ultimate"),
			'condition' 	=> 'ouah_animtype=type'
		]);
		$anim->addStyleControl([
			'selector' 		=> '.cd-headline.type .cd-words-wrapper::after', 
			'property' 		=> 'width',
			'name' 			=> __('Vertical Bar Width(Blinking Line)', "oxy-ultimate"),
			'condition' 	=> 'ouah_animtype=type',
			'control_type' 	=> 'slider-measurebox'
		])->setUnits('px', 'px,em');


		//* Loading Bar
		$anim->addStyleControl([
			'selector' 		=> '.cd-headline.loading-bar .cd-words-wrapper::after', 
			'property' 		=> 'background', 
			'control_type' 	=> 'colorpicker',
			'name' 			=> __('Loading Bar Color', "oxy-ultimate"),
			'condition' 	=> 'ouah_animtype=loading-bar'
		]);

		$anim->addStyleControl([
			'selector' 		=> '.cd-headline.loading-bar .cd-words-wrapper::after', 
			'property' 		=> 'height', 
			'control_type' 	=> 'slider-measurebox',
			'slug' 			=> 'lbar_height',
			'name' 			=> __('Loading Bar Width', "oxy-ultimate"),
			'condition' 	=> 'ouah_animtype=loading-bar',
			'unit' 			=> 'px',
			'value' 		=> '3'
		]);

		$anim->addOptionControl(
			array(
				"type" 		=> 'textfield',
				"name" 		=> __('Delay', "oxy-ultimate"),
				"slug" 		=> 'ouah_animdelay',
				"value" 	=> 2500,
				"css" 		=> false
			)
		);

		$lbardelay = $anim->addOptionControl(
			array(
				"type" 		=> 'textfield',
				"name" 		=> __('Delay for Loading Bar Type', "oxy-ultimate"),
				"slug" 		=> 'ouah_lbardelay',
				"value" 	=> 3800,
				"css" 		=> false
			)
		);
		$lbardelay->setParam("description", __("This is for loading bar animaition.", "oxy-ultimate"));

		$lettersdelay = $anim->addOptionControl(
			array(
				"type" 		=> 'textfield',
				"name" 		=> __('Delay for Letters Type', "oxy-ultimate"),
				"slug" 		=> 'ouah_lettersdelay',
				"value" 	=> 50,
				"css" 		=> false
			)
		);
		$lettersdelay->setParam("description", __("This is for 'type', 'rotate-2', 'rotate-3', 'scale' animaition.", "oxy-ultimate"));

		$typedelay = $anim->addOptionControl(
			array(
				"type" 		=> 'textfield',
				"name" 		=> __('Delay for Typing Animation', "oxy-ultimate"),
				"slug" 		=> 'ouah_typedelay',
				"value" 	=> 150,
				"css" 		=> false
			)
		);
		$typedelay->setParam("description", __("This is for typing animaition.", "oxy-ultimate"));

		$seldelay = $anim->addOptionControl(
			array(
				"type" 		=> 'textfield',
				"name" 		=> __('Selection Duration', "oxy-ultimate"),
				"slug" 		=> 'ouah_seldelay',
				"value" 	=> 500,
				"css" 		=> false
			)
		);
		$seldelay->setParam("description", __("This is for typing animaition.", "oxy-ultimate"));

		$clipdur = $anim->addOptionControl(
			array(
				"type" 		=> 'textfield',
				"name" 		=> __('Clip Duration', "oxy-ultimate"),
				"slug" 		=> 'ouah_clipdur',
				"value" 	=> 600,
				"css" 		=> false
			)
		);
		$clipdur->setParam("description", __("This is for clip animaition.", "oxy-ultimate"));

		$clipdelay = $anim->addOptionControl(
			array(
				"type" 		=> 'textfield',
				"name" 		=> __('Clip Delay', "oxy-ultimate"),
				"slug" 		=> 'ouah_clipdelay',
				"value" 	=> 1500,
				"css" 		=> false
			)
		);
		$clipdelay->setParam("description", __("This is for clip animaition.", "oxy-ultimate"));

		$this->typographySection(__("Fonts for Headline", "oxy-ultimate"), ".baft-text", $this);
		$animTextTG = $this->typographySection(__("Fonts for Animated Text", "oxy-ultimate"), ".cd-words-wrapper *", $this);
		$animTextTG->addStyleControl(['selector' => 'a.anim-text-link:hover', 'property' => 'color', 'name' => __('Hover Color')]);
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

	function render( $options, $content, $default ) {
		if( ! empty($options["ouah_animated_text"])) {
			$linkOpenTag = $linkCloseTag = '';

			if( isset( $options["ouah_before_text"] ) ) {
				$btext = $this->fetchDynamicData( $options["ouah_before_text"] );
			}

			if( isset( $options["ouah_after_text"] ) ) {
				$aftext = $this->fetchDynamicData( $options["ouah_after_text"] );
			}

			$animated_text = $this->fetchDynamicData( $options["ouah_animated_text"] );
			if( strpos($animated_text, "|") > 0 ) {
				$animated_text = explode( '|', $animated_text );
				$animated_text = implode("</b><b>", $animated_text);
			}
			
			$class = "";
			if( in_array($options["ouah_animtype"], ['type', 'rotate-2', 'rotate-3', 'scale']))
				$class .= "letters ";

			if( defined('OXY_ELEMENTS_API_AJAX') && OXY_ELEMENTS_API_AJAX && $options['anihm_preview'] == 'no' )
				$class .= $options["ouah_animtype"] . '-no';
			else
				$class .= 'cd-headline ' . $options["ouah_animtype"];

			if( $options["ouah_animtype"] == "clip")
				$class .= ' is-full-width';

			$beforeText = !empty($options["ouah_before_text"]) ? '<span class="baft-text">' . $btext . '</span> ' : '';
			$afterText = !empty($options["ouah_after_text"]) ? ' <span class="baft-text">' . $aftext . '</span>' : '';

			if( isset( $options['text_link'] ) ) {
				$url = $options['text_link'];
				if( strstr( $url, 'oudata_' ) ) {
					$url = base64_decode( str_replace( 'oudata_', '', $url ) );
					$shortcode = ougssig( $this->El, $url );
					$editable_link = esc_url( do_shortcode( $shortcode ) );
				} else {
					$editable_link = esc_url( $url );
				}

				$linkOpenTag = '<a href="' . $editable_link . '" target="' . $options['link_target'] . '" class="anim-text-link">';
				$linkCloseTag = '</a>';
			}

			$data = 'data-id="' . $options['selector'] . '"';
			$data .= ' data-animationDelay="' . $options['ouah_animdelay'] . '"';
			$data .= ' data-barAnimationDelay="' . $options['ouah_lbardelay'] . '"';
			$data .= ' data-lettersDelay="' . $options['ouah_lettersdelay'] . '"';
			$data .= ' data-typeLettersDelay="' . $options['ouah_typedelay'] . '"';
			$data .= ' data-selectionDuration="' . $options['ouah_seldelay'] . '"';
			$data .= ' data-revealDuration="' . $options['ouah_clipdur'] . '"';
			$data .= ' data-revealAnimationDelay="' . $options['ouah_clipdelay'] . '"';

			printf(
				'<%1$s class="%5$s" %8$s>%2$s<span class="cd-words-wrapper waiting">%6$s<b class="is-visible">%3$s</b>%7$s</span>%4$s</%1$s>', 
				$options["ouah_tag"], 
				$beforeText,
				$animated_text,
				$afterText,
				$class,
				$linkOpenTag,
				$linkCloseTag,
				$data
			);

			if( defined('OXY_ELEMENTS_API_AJAX') || isset($_GET['oxygen_iframe']) ) {
				$this->anim_enqueue_scripts();

				$this->El->builderInlineJS("
					jQuery(document).ready(function(){
						new OUAnimatedHeadlines({ id: '%%ELEMENT_ID%%', animationDelay: %%ouah_animdelay%%, barAnimationDelay: %%ouah_lbardelay%%, lettersDelay: %%ouah_lettersdelay%%, typeLettersDelay: %%ouah_typedelay%%, selectionDuration: %%ouah_seldelay%%, revealDuration: %%ouah_clipdur%%, revealAnimationDelay: %%ouah_clipdelay%% });});"
				);
			} else {
				$this->animh_js = "jQuery(document).ready(function($){
					$('.cd-headline').each(function(){
						new OUAnimatedHeadlines({ 
							id: $(this).attr('data-id'), 
							animationDelay: $(this).attr('data-animationDelay'), 
							barAnimationDelay: $(this).attr('data-barAnimationDelay'), 
							lettersDelay: $(this).attr('data-lettersDelay'), 
							typeLettersDelay: $(this).attr('data-typeLettersDelay'), 
							selectionDuration: $(this).attr('data-selectionDuration'), 
							revealDuration: $(this).attr('data-revealDuration'), 
							revealAnimationDelay: $(this).attr('data-revealAnimationDelay') 
						});
					});
				});" . "\n";
				add_action( 'wp_footer', array( $this, 'anim_enqueue_scripts' ) );
				$this->El->footerJS( $this->animh_js );
			}
		}
	}

	function customCSS($original, $selector) {
		$css = '';

		if( ! $this->css_added ) {
			$this->css_added = true;
			$css.= file_get_contents(__DIR__.'/'.basename(__FILE__, '.php').'.css');
		}

		return $css;
	}

	function anim_enqueue_scripts() {
		wp_enqueue_script('ou-anh-script', OXYU_URL . 'assets/js/oxy-animh.js',array(),filemtime( OXYU_DIR . 'assets/js/oxy-animh.js' ),true);
	}
}

new OUAnimatedHeading();