<?php

add_action( 'after_setup_theme', 'oxyu_setup_oxy_ultimate', 50 );
function oxyu_setup_oxy_ultimate() {
	add_action( 'wp_enqueue_scripts', 'oxyu_load_scripts', 999 );

	add_image_size( 'acf-gallery', 300, 300, true );
	add_action('wp_head' , function(){
		echo "<style>.oxygen-oxy-ou_countdown-oucd_timezone .select2-container--default .select2-selection--single .select2-selection__rendered{color: #fff!important;}</style>";}
	);
}

function oxyu_load_scripts() {
	$active_components = oxyu_get_active_components();

	if( sizeof( (array) $active_components ) - 1 <= 0) {
		$active_components = array();
	}

	wp_register_style('ou-mfp-style', OXYU_URL . 'assets/css/mfp.css',array(),filemtime( OXYU_DIR . 'assets/css/mfp.css' ),'all');
	wp_register_style('ou-swiper-style', OXYU_URL . 'assets/css/swiper.min.css',array(),filemtime( OXYU_DIR . 'assets/css/swiper.min.css' ),'all');
	wp_register_script('ou-mfp-script', OXYU_URL . 'assets/js/jquery.magnificpopup.js',array(),filemtime( OXYU_DIR . 'assets/js/jquery.magnificpopup.js' ),true);
	wp_register_script('ou-swiper-script', OXYU_URL . 'assets/js/swiper.jquery.min.js',array(),filemtime( OXYU_DIR . 'assets/js/swiper.jquery.min.js' ),true);
	wp_register_script('ouacfg-slider-script', OXYU_URL . 'assets/js/ouacfg-slider.js',array(),filemtime( OXYU_DIR . 'assets/js/ouacfg-slider.js' ),true);

	if( in_array( 'ou-content-slider', $active_components ) ) {
		wp_register_script('oucnt-slider-script', OXYU_URL . 'assets/js/ou-content-slider.js',array(),filemtime( OXYU_DIR . 'assets/js/ou-content-slider.js' ),true);
	}

	if( in_array( 'ou-highlighted-heading', $active_components ) || in_array( 'ou-gallery', $active_components ) ) {
		wp_register_script('ou-waypoints-script', OXYU_URL . 'assets/js/jquery.waypoints.js',array('jquery-migrate'),filemtime( OXYU_DIR . 'assets/js/jquery.waypoints.js' ),true);
	}

	if( in_array( 'ou-gallery', $active_components ) ) {
		wp_register_script('ou-gallery-script', OXYU_URL . 'assets/js/acfgallerylightbox.js',array(),filemtime( OXYU_DIR . 'assets/js/acfgallerylightbox.js' ),true);
	}

	if( in_array( 'ou-highlighted-heading', $active_components ) ) {
		wp_register_script('ou-hlh-script', OXYU_URL . 'assets/js/highlightedheading.js',array(),filemtime( OXYU_DIR . 'assets/js/highlightedheading.js' ),true);
	}

	if( in_array( 'ou-ultimate-video', $active_components ) ) {
		wp_register_style('ouv-fancybox', OXYU_URL . 'assets/css/jquery.fancybox.min.css',array(),filemtime( OXYU_DIR . 'assets/css/jquery.fancybox.min.css' ),'all');
		wp_register_script('ouv-fancybox-script', OXYU_URL . 'assets/js/jquery.fancybox.min.js',array(),filemtime( OXYU_DIR . 'assets/js/jquery.fancybox.min.js' ),true);
		wp_register_script('ouv-frontend-script', OXYU_URL . 'assets/js/ouv.js',array(),filemtime( OXYU_DIR . 'assets/js/ouv.js' ),true);
	}

	if( isset($_GET['ct_builder']) ) {
		wp_enqueue_script(
			'ou-ctbuilder-scripts',
			OXYU_URL . 'assets/js/ou-ct-builder.js',
			array(),
			filemtime( OXYU_DIR . 'assets/js/ou-ct-builder.js' ),
			true
		);
	}
}

function oxyu_elements() {

	$active_components = oxyu_get_active_components();

	if( sizeof( (array) $active_components ) - 1 <= 0) {
		return;
	}

	foreach ( $active_components as $comp ) {
		$element_path = OXYU_DIR . 'elements/' . $comp . '/' . $comp . '.php';
		if ( file_exists( $element_path ) ) {
			include_once $element_path;
		}
	}
}
add_action( 'init', 'oxyu_elements', 20 );

add_action('oxygen_add_plus_sections', 'oxyu_register_add_plus_section' );
function oxyu_register_add_plus_section() {
	$brand_name = __( 'OxyUltimate', "oxy-ultimate" );

	$ouwl = get_option('ouwl');
	if( $ouwl ) {
		$brand_name = ! empty( $ouwl['plugin_name'] ) ? esc_html( $ouwl['plugin_name'] ) : $brand_name;
	}

	CT_Toolbar::oxygen_add_plus_accordion_section( "oxyultimate", $brand_name );
}

add_action('oxygen_add_plus_oxyultimate_section_content', 'oxyu_register_add_plus_subsections');
function oxyu_register_add_plus_subsections() {
	$data = getAllOUComps();
	if( ! empty( $data ) ) {
		if( oxyu_has_active_components( $data['Images'] ) ) {
			printf('<h2>%s</h2>', __( 'Images', 'oxy-ultimate') );
			do_action("oxygen_add_plus_oxyultimate_images");
		}

		if( oxyu_has_active_components( $data['Buttons'] ) ) {
			printf('<h2>%s</h2>', __( 'Buttons', 'oxy-ultimate') );
			do_action("oxygen_add_plus_oxyultimate_buttons");
		}

		if( oxyu_has_active_components( $data['Text'] ) ) {
			printf('<h2>%s</h2>', __( 'Text', 'oxy-ultimate') );
			do_action("oxygen_add_plus_oxyultimate_text");
		}

		if( oxyu_has_active_components( $data['Interactive'] ) ) {
			printf('<h2>%s</h2>', __( 'Interactive', 'oxy-ultimate') );
			do_action("oxygen_add_plus_oxyultimate_content");
		}

		if( oxyu_has_active_components( $data['Forms'] ) ) {
			printf('<h2>%s</h2>', __('Form', 'oxy-ultimate') );
			do_action("oxygen_add_plus_oxyultimate_form");
		}

		if( oxyu_has_active_components( $data['Menu'] ) ) {
			printf('<h2>%s</h2>', __('Menu', 'oxy-ultimate') );
			do_action("oxygen_add_plus_oxyultimate_menu");
		}
	}
}

function oxyu_get_active_components() {
	$active_components = '';
	if ( is_network_admin() ) {
		$active_components = get_site_option( '_ou_active_components' );
	} else {
		$active_components = get_option( '_ou_active_components' );
	}

	return $active_components;
}

function oxyu_has_active_components( $components ) {
	$active_components = oxyu_get_active_components();

	if( sizeof( (array) $active_components ) - 1 <= 0) {
		$active_components = array();
	}

	foreach ($components as $key => $value) {
		if( in_array( $key, $active_components ) ) {
			return true;
		}
	}

	return false;
}

function getAllOUComps() {
	$compsList = [
		'Buttons' 	=> [
			'ou-dual-button' 			=> __( "Dual Button", 'oxy-ultimate' ),
			'ou-hover-animated-button' 	=> __( "Hover Animated Button", 'oxy-ultimate' ),
		],
		'Forms' 		=> [
			'ou-comment-form' 			=> __( "Comment Form", 'oxy-ultimate' ),
			'ou-cf7' 					=> __( "Contact Form 7 Styler", 'oxy-ultimate' ),
			'ou-ff-styler' 				=> __( "Fluent Form Styler", 'oxy-ultimate' ),
			'ou-fforms-styler' 			=> __( "Formidable Forms Styler", 'oxy-ultimate' ),
			'ou-gf-styler' 				=> __( "Gravity Form Styler", 'oxy-ultimate' ),
			'ou-wpforms-styler' 		=> __( "WPForms Styler", 'oxy-ultimate' ),
			//'ou-wsform' 				=> __( "WS Form Styler", 'oxy-ultimate' ), //will work
		],
		'Interactive' 	=> [
			'ou-lightbox' 				=> __( "AJAX Lightbox", 'oxy-ultimate' ),
			//'ou-breadcrumbs' 			=> __( "Ultimate Breadcrumbs", 'oxy-ultimate' ), //will work
			'ou-classic-accordion' 		=> __( "Classic Accordion", 'oxy-ultimate' ),
			'ou-css-grid' 				=> __( "CSS Grid", "oxy-ultimate" ),
			'ou-content-slider' 		=> __( "Content Slider", 'oxy-ultimate' ),
			'ou-countdown' 				=> __( "Countdown", 'oxy-ultimate' ),
			'ou-dynamic-accordion' 		=> __( "Dynamic Accordion", 'oxy-ultimate' ),
			'ou-icon-list' 				=> __( "Icon List", 'oxy-ultimate' ),
			'ou-off-canvas' 			=> __( "Off Canvas", 'oxy-ultimate' ),
			'ou-show-more-less' 		=> __( "Show More / Less", 'oxy-ultimate' ),
			'ou-smoothscrolling' 		=> __( "Smooth Scrolling", 'oxy-ultimate' ),
			'ou-rating' 				=> __( "Star Rating", 'oxy-ultimate' ),
			'ou-tooltip' 				=> __( "Tooltip", 'oxy-ultimate' ),
			'ou-ultimate-video' 		=> __( "Ultimate Video", 'oxy-ultimate' )
		],
		'Images' 	=> [
			'ou-acf-imgacrd' 			=> __( "ACF Gallery Accordion", 'oxy-ultimate' ),				
			'ou-before-after-image' 	=> __( "Before After Image", 'oxy-ultimate' ),
			'ou-flex-gallery' 			=> __( "Flex Gallery", "oxy-ultimate" ),
			'ou-hotspot' 				=> __( "Hotspot", 'oxy-ultimate' ),
			'ou-image-panels' 			=> __( "Image Accordion", 'oxy-ultimate' ),
			'ou-ultimate-image' 		=> __( "Image Lightbox", 'oxy-ultimate' ),
			'ou-gallery-slider' 		=> __( "Gallery Slider", 'oxy-ultimate' ),
			'ou-gallery' 				=> __( "Ultimate Gallery", 'oxy-ultimate' ),
			'ou-image-mask' 			=> __( "Image Mask", 'oxy-ultimate' )
		],
		'Text' 		=> [
			'ou-animated-heading' 		=> __( "Animated Heading", 'oxy-ultimate' ),
			'ou-dual-color-text' 		=> __( "Dual Color Text", 'oxy-ultimate' ),
			'ou-fancy-heading' 			=> __( "Fancy Heading", 'oxy-ultimate' ),
			'ou-highlighted-heading' 	=> __( "Highlighted Heading", 'oxy-ultimate' )
		],
		'Menu' 		=> [
			'ou-acrd-menu' 				=> __('Accordion Menu', 'oxy-ultimate'),
			'ou-sliding-menu' 			=> __('Sliding Menu', 'oxy-ultimate'),
			//'ou-anim-burger' 			=> __('Animated Burger', 'oxy-ultimate'), //will work
		]
	];

	return $compsList;
}

add_shortcode('oxyultimate', 'oxyultimate_vsb_dynamic_shortcode');
function oxyultimate_vsb_dynamic_shortcode( $atts, $content = null ) {
	// validation will go here
	global $oxygen_VSB_Dynamic_Shortcodes;

	// replace single quotes in atts
	foreach($atts as $key => $item) {
		$atts[$key] = str_replace('__SINGLE_QUOTE__', "'", $item);
	}

	global $wp_query;
	global $oxy_vsb_use_query;

	if(isset($oxy_vsb_use_query) && is_object($oxy_vsb_use_query)) {

		$query = $oxy_vsb_use_query;

	} else {

		global $oxygen_preview_post_id;

		if(isset($oxygen_preview_post_id) && is_numeric($oxygen_preview_post_id)) {
			$query_vars = array('p' => $oxygen_preview_post_id, 'post_type' => 'any');
		}
		else {
			$query_vars = $wp_query->query_vars;	
		}

		$query = new WP_Query($query_vars);

		if(!is_page()) {
			$query->the_post();
		}
	}
	
	$handler = 'oxygen_'.$atts['data'];

	if( substr( $atts['data'], 0, 7 ) == "custom_" ) {
		$handler = 'oxygen_custom';
	}

	if (method_exists($oxygen_VSB_Dynamic_Shortcodes, $handler)) {

		$output = call_user_func(array($oxygen_VSB_Dynamic_Shortcodes, $handler), $atts);

	} else {

		return "No such function ".$handler;

	}

	if (isset($atts['link'])) {
		$link_handler = 'oxygen_'.$atts['link'];

	if (isset($link_handler) && method_exists($oxygen_VSB_Dynamic_Shortcodes, $link_handler)) {
		$link_output = call_user_func(array($oxygen_VSB_Dynamic_Shortcodes, $link_handler), $atts);

			if ($link_output) {
				$output = "<a href='".$link_output."'>".$output."</a>";
			} 
		} 
	}

	$output = apply_filters('oxygen_vsb_after_oxy_shortcode_render', $output);

	if(!isset($oxy_vsb_use_query) || !is_object($oxy_vsb_use_query)) {
		wp_reset_query();
	}

	return $output;
}

function ougssig( $el, $content ) {
	global $oxygen_signature;
	$signature = $oxygen_signature->generate_signature_shortcode_string( $el->get_tag() );
	$shortcode = str_replace('[oxyultimate', '[oxyultimate ' . $signature, $content);

	return $shortcode;
}

function ou_button_hover_effect( $effect ) {
	$hover_effect['none'] = '';
	$hover_effect['sweep_right'] = '.hover-effect-sweep_right .ou-button-effect:before {content: " ";-webkit-transform: scaleX(0);-moz-transform: scaleX(0);-o-transform: scaleX(0);-ms-transform: scaleX(0);transform: scaleX(0);-webkit-transform-origin: 0 50%;-moz-transform-origin: 0 50%;-o-transform-origin: 0 50%;-ms-transform-origin: 0 50%;transform-origin: 0 50%;}.hover-effect-sweep_right .ou-button-effect:hover:before {-webkit-transform: scaleX(1);-moz-transform: scaleX(1);-o-transform: scaleX(1);-ms-transform: scaleX(1);transform: scaleX(1);}';

	$hover_effect['sweep_left'] = '.hover-effect-sweep_left .ou-button-effect:before {content: "";-webkit-transform: scaleX(0);-moz-transform: scaleX(0);-o-transform: scaleX(0);-ms-transform: scaleX(0);transform: scaleX(0);-webkit-transform-origin: 100% 50%;-moz-transform-origin: 100% 50%;-o-transform-origin: 100% 50%;-ms-transform-origin: 100% 50%;transform-origin: 100% 50%;}.hover-effect-sweep_left .ou-button-effect:hover:before {-webkit-transform: scaleX(1);-moz-transform: scaleX(1);-o-transform: scaleX(1);-ms-transform: scaleX(1);transform: scaleX(1);}';

	$hover_effect['sweep_bottom'] = '.hover-effect-sweep_bottom .ou-button-effect:before {content: "";-webkit-transform: scaleY(0);-moz-transform: scaleY(0);-o-transform: scaleY(0);-ms-transform: scaleY(0);transform: scaleY(0);-webkit-transform-origin: 50% 0;-moz-transform-origin: 50% 0;-o-transform-origin: 50% 0;-ms-transform-origin: 50% 0;transform-origin: 50% 0;}.hover-effect-sweep_bottom .ou-button-effect:hover:before {-webkit-transform: scaleY(1);-moz-transform: scaleY(1);-o-transform: scaleY(1);-ms-transform: scaleY(1);transform: scaleY(1);}';

	$hover_effect['sweep_top'] = '.hover-effect-sweep_top .ou-button-effect:before {content: "";-webkit-transform: scaleY(0);-moz-transform: scaleY(0);-o-transform: scaleY(0);-ms-transform: scaleY(0);transform: scaleY(0);-webkit-transform-origin: 50% 100%;-moz-transform-origin: 50% 100%;-o-transform-origin: 50% 100%;-ms-transform-origin: 50% 100%;transform-origin: 50% 100%;}.hover-effect-sweep_top .ou-button-effect:hover:before {-webkit-transform: scaleY(1);-moz-transform: scaleY(1);-o-transform: scaleY(1);-ms-transform: scaleY(1);transform: scaleY(1);}';

	$hover_effect['bounce_right'] = '.hover-effect-bounce_right .ou-button-effect:before {content: "";-webkit-transform: scaleX(0);-moz-transform: scaleX(0);-o-transform: scaleX(0);-ms-transform: scaleX(0);transform: scaleX(0);-webkit-transform-origin: 0 50%;-moz-transform-origin: 0 50%;-o-transform-origin: 0 50%;-ms-transform-origin: 0 50%;transform-origin: 0 50%;}.hover-effect-bounce_right .ou-button-effect:hover:before {-webkit-transform: scaleX(1);-moz-transform: scaleX(1);-o-transform: scaleX(1);-ms-transform: scaleX(1);transform: scaleX(1);transition-timing-function: cubic-bezier(0.52, 1.64, 0.37, 0.66);}';

	$hover_effect['bounce_left'] = '.hover-effect-bounce_left .ou-button-effect:before {content: "";-webkit-transform: scaleX(0);-moz-transform: scaleX(0);-o-transform: scaleX(0);-ms-transform: scaleX(0);transform: scaleX(0);-webkit-transform-origin: 100% 50%;-moz-transform-origin: 100% 50%;-o-transform-origin: 100% 50%;-ms-transform-origin: 100% 50%;transform-origin: 100% 50%;}.hover-effect-bounce_left .ou-button-effect:hover:before {-webkit-transform: scaleX(1);-moz-transform: scaleX(1);-o-transform: scaleX(1);-ms-transform: scaleX(1);transform: scaleX(1);transition-timing-function: cubic-bezier(0.52, 1.64, 0.37, 0.66);}';

	$hover_effect['bounce_bottom'] = '.hover-effect-bounce_bottom .ou-button-effect:before {content: "";-webkit-transform: scaleY(0);-moz-transform: scaleY(0);-o-transform: scaleY(0);-ms-transform: scaleY(0);transform: scaleY(0);-webkit-transform-origin: 50% 0;-moz-transform-origin: 50% 0;-o-transform-origin: 50% 0;-ms-transform-origin: 50% 0;transform-origin: 50% 0;}.hover-effect-bounce_bottom .ou-button-effect:hover:before {-webkit-transform: scaleY(1);-moz-transform: scaleY(1);-o-transform: scaleY(1);-ms-transform: scaleY(1);transform: scaleY(1);transition-timing-function: cubic-bezier(0.52, 1.64, 0.37, 0.66);}';

	$hover_effect['bounce_top'] = '.hover-effect-bounce_top .ou-button-effect:before {content: "";-webkit-transform: scaleY(0);-moz-transform: scaleY(0);-o-transform: scaleY(0);-ms-transform: scaleY(0);transform: scaleY(0);-webkit-transform-origin: 50% 100%;-moz-transform-origin: 50% 100%;-o-transform-origin: 50% 100%;-ms-transform-origin: 50% 100%;transform-origin: 50% 100%;}.hover-effect-bounce_top .ou-button-effect:hover:before {-webkit-transform: scaleY(1);-moz-transform: scaleY(1);-o-transform: scaleY(1);-ms-transform: scaleY(1);transform: scaleY(1);transition-timing-function: cubic-bezier(0.52, 1.64, 0.37, 0.66);}';

	$hover_effect['sinh'] = '.hover-effect-sinh .ou-button-effect:before {content: "";-webkit-transform: scaleX(1);-moz-transform: scaleX(1);-o-transform: scaleX(1);-ms-transform: scaleX(1);transform: scaleX(1);-webkit-transform-origin: 50%;-moz-transform-origin: 50%;-o-transform-origin: 50%;-ms-transform-origin: 50%;transform-origin: 50%;  }.hover-effect-sinh .ou-button-effect:hover:before {-webkit-transform: scaleX(0);-moz-transform: scaleX(0);-o-transform: scaleX(0);-ms-transform: scaleX(0);transform: scaleX(0);}';

	$hover_effect['souh'] = '.hover-effect-souh .ou-button-effect:before {content: "";-webkit-transform: scaleX(0);-moz-transform: scaleX(0);-o-transform: scaleX(0);-ms-transform: scaleX(0);transform: scaleX(0);-webkit-transform-origin: 50%;-moz-transform-origin: 50%;-o-transform-origin: 50%;-ms-transform-origin: 50%;transform-origin: 50%;}.hover-effect-souh .ou-button-effect:hover:before {-webkit-transform: scaleX(1);-moz-transform: scaleX(1);-o-transform: scaleX(1);-ms-transform: scaleX(1);transform: scaleX(1);}';

	$hover_effect['sinv'] = '.hover-effect-sinv .ou-button-effect:before {content: "";-webkit-transform: scaleY(1);-moz-transform: scaleY(1);-o-transform: scaleY(1);-ms-transform: scaleY(1);transform: scaleY(1);-webkit-transform-origin: 50%;-moz-transform-origin: 50%;-o-transform-origin: 50%;-ms-transform-origin: 50%;transform-origin: 50%;}.hover-effect-sinv .ou-button-effect:hover:before {-webkit-transform: scaleY(0);-moz-transform: scaleY(0);-o-transform: scaleY(0);-ms-transform: scaleY(0);transform: scaleY(0);}';

	$hover_effect['souv'] = '.hover-effect-souv .ou-button-effect:before {content: "";-webkit-transform: scaleY(0);-moz-transform: scaleY(0);-o-transform: scaleY(0);-ms-transform: scaleY(0);transform: scaleY(0);-webkit-transform-origin: 50%;-moz-transform-origin: 50%;-o-transform-origin: 50%;-ms-transform-origin: 50%;transform-origin: 50%;}.hover-effect-souv .ou-button-effect:hover:before {-webkit-transform: scaleY(1);-moz-transform: scaleY(1);-o-transform: scaleY(1);-ms-transform: scaleY(1);transform: scaleY(1);}';

	return $hover_effect[ $effect ];
}

add_filter( 'oxy_base64_encode_options', 'ou_dynamic_data_fields');
function ou_dynamic_data_fields( $list ) {
	$list = array_merge($list, array('oxy-ou_uimage_img_url','oxy-ou_hl_heading_ouhlh_after_text','oxy-ou_baimg_heading_oubfi_before_image','oxy-ou_baimg_heading_oubfi_after_image','oxy-ou_baimg_heading_oubfi_before_label','oxy-ou_baimg_heading_oubfi_after_label','oxy-ou_animated_heading_ouah_before_text','oxy-ou_animated_heading_ouah_animated_text','oxy-ou_animated_heading_ouah_after_text','oxy-ou_animated_heading_text_link','oxy-ou_hl_heading_ouhlh_before_text','oxy-ou_hl_heading_ouhlh_hl_text','oxy-ou_dual_button_btn1content','oxy-ou_dual_button_btn2content','oxy-ou_dual_button_btn1_url','oxy-ou_dual_button_btn2_url','oxy-ou_ha_button_btn_text','oxy-ou_ha_button_btn_url','oxy-ou_fancy_heading_ou_fancy_text','oxy-ou_uimage_ou_imgurl','oxy-ou_image_ou_image_url','oxy-ou_image_img_alt','oxy-ou_panel_item_ou_panel_image','oxy-ou_panel_item_panel_link','oxy-ou_video_vurl','oxy-ou_video_image','oxy-ou_video_poster','oxy-ouli_additem_list_text','oxy-ouli_additem_url','oxy-ou_acf_gallery_page_id','oxy-ou_classic_acrd_ouacrd_title','oxy-ou_classic_acrd_sub_title','oxy-ou_hotspot_hp_image','oxy-ou_hotspot_img_alt','oxy-ou_show_more_less_more_text','oxy-ou_show_more_less_less_text','oxy-ou_image_mask_image','oxy-ou_image_mask_img_alt', 'oxy-ou_rating_rating_points')); 
        
    return $list;
}

function ouGetWPMeuns() {
	$get_menus = wp_get_nav_menus();
	$options = array();
	$options['sel'] = __('Select Menu', "oxy-ultimate");

	if ( $get_menus ) {

		foreach( $get_menus as $key => $menu ) {
			$options[ $menu->slug ] = $menu->name;
		}

	} else {
		$options['nomenu'] = __( 'No Menus Found', 'oxy-ultimate' );
	}

	return $options;
}

function ou_get_taxonomies() {
	$options 	= array( 'category' => 'Category' );
	$taxonomies = get_taxonomies( array( 'public' => true, '_builtin' => false ) );

	if ( ! empty( $taxonomies ) ) {

		foreach ( $taxonomies as $taxonomy ) {
			$options[$taxonomy] = get_taxonomy( $taxonomy )->labels->name;
		}
	}

	return $options;
}

function ouGetBaseJS() {
	$js_content = file_get_contents( OXYU_DIR . '/assets/js/oxyultimate.js' );

	return $js_content;
}

add_action('wp_ajax_ou_do_ajax_lightbox', 'ou_do_ajax_lightbox' );
add_action('wp_ajax_nopriv_ou_do_ajax_lightbox', 'ou_do_ajax_lightbox' );
function ou_do_ajax_lightbox() {
	check_ajax_referer( 'ou-ajax-lb-nonce', 'security' );

	ob_start();

	do_action( 'wp_enqueue_scripts' );

	global $wp_styles, $post_id, $OxygenConditions;
	if( ! is_a( $OxygenConditions, 'OxygenConditions' ) ) {
		require_once( CT_FW_PATH . "/includes/conditions.php");
		$OxygenConditions = new OxygenConditions();
	}

	foreach( $wp_styles->queue as $style ) {
		wp_dequeue_style($wp_styles->registered[$style]->handle);
	}

	if ( isset( $_POST['postID'] ) && isset( $_POST['template'] ) ) {

		$post_id = ($_POST['postID'] == 'builderprv') ? 'builderprv' : absint( $_POST['postID'] );
		$lb_tpl = absint( $_POST['template'] );

		if( $post_id != 'builderprv' )
			add_action( 'pre_get_posts', 'ou_rep_query_filter' );

		$lb_content = get_post_meta( $lb_tpl, "ct_builder_shortcodes", true );
		echo ct_do_shortcode( $lb_content );

	} else {
		echo __('Invalid Template.', "oxyultimate-woo");
	}

	wp_footer();

	wp_send_json( ob_get_clean() );

	if( $post_id != 'builderprv' )
		remove_action( 'pre_get_posts', 'ou_rep_query_filter' );

	wp_die();
}

function ou_rep_query_filter($query) {
	global $post_id;

	$query->set( 'post__in', [ $post_id ] );
	$query->set( 'no_found_rows', true );
	$query->set( 'posts_per_page', 1 );
}