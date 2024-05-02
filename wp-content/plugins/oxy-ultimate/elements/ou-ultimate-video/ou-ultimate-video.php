<?php

namespace Oxygen\OxyUltimate;

class OUVideo extends \OxyUltimateEl {
	
	public $has_js = true;
	public $css_added = false;

	function name() {
		return __( "Ultimate Video", 'oxy-ultimate' );
	}

	function slug() {
		return "ou_video";
	}

	function oxyu_button_place() {
		return "content";
	}

	function icon() {
		return CT_FW_URI . '/toolbar/UI/oxygen-icons/add-icons/video.svg';
	}

	function init() {
		$this->El->useAJAXControls();
		$this->enableNesting();
	}

	function ouVideoConfig() {
		$config = $this->addControlSection('video_section', __('Video URL'), 'assets/path', $this );

		$source = $config->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Source', 'oxy-ultimate'),
			'slug' 		=> 'video_source',
			'value' 	=> [
				'yt' => __('YouTube'), 
				'vm' => __('Vimeo'), 
				'dm' => __('Dailymotion'), 
				'el' => __('External Link'),//'sh' => __('Self Hosted')
			],
			'default' 	=> 'yt',
			'css' 		=> false
		]);

		$url = $config->addCustomControl(
			'<div class="oxygen-file-input oxygen-option-default" ng-class="{\'oxygen-option-default\':iframeScope.isInherited(iframeScope.component.active.id, \'oxy-ou_video_vurl\')}">
				<input type="text" spellcheck="false" ng-model="iframeScope.component.options[iframeScope.component.active.id][\'model\'][\'oxy-ou_video_vurl\']" ng-model-options="{ debounce: 10 }" ng-change="iframeScope.setOption(iframeScope.component.active.id, iframeScope.component.active.name,\'oxy-ou_video_vurl\');iframeScope.checkResizeBoxOptions(\'oxy-ou_video_vurl\'); " class="ng-pristine ng-valid ng-touched" placeholder="https://">
				<div class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesLinkMode" callback="iframeScope.ouDynamicUVUrl">data</div>
			</div>
			',
			"vurl"
		);
		$url->setParam( 'heading', __('Video URL') );

		$startTime = $config->addOptionControl([
			'type' 	=> 'slider-measurebox',
			'name' 	=> __('Start Time'),
			'slug' 	=> 'start_time',
			'css' 	=> false
		]);
		$startTime->setParam('description', __('Specify a start time'));
		$startTime->setUnits('s', 'sec');

		$endTime = $config->addOptionControl([
			'type' 	=> 'slider-measurebox',
			'name' 	=> __('End Time'),
			'slug' 	=> 'end_time',
			'css' 	=> false
		]);
		$endTime->setParam('description', __('Specify a end time'));
		$endTime->setUnits('s', 'sec');
		$endTime->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_video_video_source']!='vm'&&iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_video_video_source']!='dm'");

		$aratio = $config->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Aspect Ratio', 'oxy-ultimate'),
			'slug' 		=> 'aspect_ratio',
			'value' 	=> [
				'11' 		=> __('1:1'), 
				'32' 		=> __('3:2'), 
				'43' 		=> __('4:3'), 
				'169' 		=> __('16:9'),
				'219' 		=> __('21:9')
			],
			'default' 	=> '169',
			'css' 		=> false
		]);

		$config->addOptionControl([
			'type' 		=> 'textfield',
			'name' 		=> __('Aria Label(for Accessibility)'),
			'slug' 		=> 'aria_label'
		]);
	}

	function ouVideoOptions() {
		$options = $this->addControlSection('control_section', __('Video Options'), 'assets/path', $this );

		$autoPlay = $options->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Auto Play', 'oxy-ultimate'),
			'slug' 		=> 'autoplay',
			'value' 	=> [
				'yes' 		=> __('Yes'), 
				'no' 		=> __('No')
			],
			'default' 	=> 'no',
			'css' 		=> false
		]);

		$mute = $options->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Mute', 'oxy-ultimate'),
			'slug' 		=> 'mute',
			'value' 	=> [
				'yes' 		=> __('Yes'), 
				'no' 		=> __('No')
			],
			'default' 	=> 'no',
			'css' 		=> false
		]);

		$loop = $options->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Loop', 'oxy-ultimate'),
			'slug' 		=> 'loop',
			'value' 	=> [
				'yes' 		=> __('Yes'), 
				'no' 		=> __('No')
			],
			'default' 	=> 'no',
			'css' 		=> false
		]);
		$loop->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_video_video_source']!='dm'");

		$controls = $options->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Player Controls', 'oxy-ultimate'),
			'slug' 		=> 'controls',
			'value' 	=> [
				'yes' 		=> __('Show'), 
				'no' 		=> __('Hide')
			],
			'default' 	=> 'show',
			'css' 		=> false
		]);
		$controls->setParam('description', __("Show or Hide the Player Controls, such as Play/Pause, Volume, etc.") );
		$controls->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_video_video_source']!='vm'");

		$sugVideo = $options->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Suggested Videos', 'oxy-ultimate'),
			'slug' 		=> 'rel',
			'value' 	=> [
				'' 		=> __('Current Video Channel'), 
				'any' 		=> __('Any Video')
			],
			'default' 	=> '',
			'css' 		=> false
		]);
		$sugVideo->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_video_video_source']=='yt'");

		$modestbranding = $options->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Modest Branding', 'oxy-ultimate'),
			'slug' 		=> 'modestbranding',
			'value' 	=> [
				'yes' 		=> __('Yes'), 
				'no' 		=> __('No')
			],
			'default' 	=> 'no',
			'css' 		=> false
		]);
		$modestbranding->setParam('description', __("This option lets you use a YouTube player that does not show a YouTube logo. Note that a small YouTube text label will still display in the upper-right corner of a paused video when the user's mouse pointer hovers over the player.") );
		$modestbranding->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_video_video_source']=='yt'");

		$yt_privacy = $options->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Privacy Mode', 'oxy-ultimate'),
			'slug' 		=> 'yt_privacy',
			'value' 	=> [
				'yes' 		=> __('Yes'), 
				'no' 		=> __('No')
			],
			'default' 	=> 'no',
			'css' 		=> false
		]);
		$yt_privacy->setParam('description', __("When you turn on privacy mode, YouTube won't store information about visitors on your website unless they play the video.") );
		$yt_privacy->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_video_video_source']=='yt'");

		$vimeo_title = $options->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Intro Title', 'oxy-ultimate'),
			'slug' 		=> 'vimeo_title',
			'value' 	=> [
				'show' 		=> __('Show'), 
				'hide' 		=> __('Hide')
			],
			'default' 	=> 'show',
			'css' 		=> false
		]);
		$vimeo_title->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_video_video_source']=='vm'");

		$vimeo_portrait = $options->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Intro Portrait', 'oxy-ultimate'),
			'slug' 		=> 'vimeo_portrait',
			'value' 	=> [
				'show' 		=> __('Show'), 
				'hide' 		=> __('Hide')
			],
			'default' 	=> 'show',
			'css' 		=> false
		]);
		$vimeo_portrait->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_video_video_source']=='vm'");

		$vimeo_byline = $options->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Intro Byline', 'oxy-ultimate'),
			'slug' 		=> 'vimeo_byline',
			'value' 	=> [
				'show' 		=> __('Show'), 
				'hide' 		=> __('Hide')
			],
			'default' 	=> 'show',
			'css' 		=> false
		]);
		$vimeo_byline->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_video_video_source']=='vm'");

		$info = $options->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Show Info', 'oxy-ultimate'),
			'slug' 		=> 'showinfo',
			'value' 	=> [
				'show' 		=> __('Show'), 
				'hide' 		=> __('Hide')
			],
			'default' 	=> 'show',
			'css' 		=> false
		]);
		$info->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_video_video_source']=='dm'");

		$logo = $options->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Logo', 'oxy-ultimate'),
			'slug' 		=> 'logo',
			'value' 	=> [
				'show' 		=> __('Show'), 
				'hide' 		=> __('Hide')
			],
			'default' 	=> 'show',
			'css' 		=> false
		]);
		$logo->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_video_video_source']=='dm'");

		$color = $options->addOptionControl([
			'type' 		=> 'colorpicker',
			'name' 		=> __('Controls Color', 'oxy-ultimate'),
			'slug' 		=> 'control_color',
			'css' 		=> false
		]);
		$color->setParam('ng_show', "(iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_video_video_source']=='vm'||iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_video_video_source']=='dm')");

		$poster = $options->addCustomControl(
			"<div class=\"oxygen-control  not-available-for-classes not-available-for-media\">			
				<div class=\"oxygen-file-input\">
					<input type=\"text\" spellcheck=\"false\" ng-change=\"iframeScope.setOption(iframeScope.component.active.id,'ct_image','oxy-ou_video_poster'); iframeScope.parseImageShortcode()\" ng-class=\"{'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, 'oxy-ou_video_poster')}\" ng-model=\"iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_video_poster']\" ng-model-options=\"{ debounce: 10 }\" class=\"ng-pristine ng-valid oxygen-option-default ng-touched\">
					<div class=\"oxygen-file-input-browse\" data-mediatitle=\"Select Image\" data-mediabutton=\"Select Image\" data-mediaproperty=\"oxy-ou_video_poster\" data-mediatype=\"mediaUrl\" data-returnvalue=\"url\">browse</div>
					<div class=\"oxygen-dynamic-data-browse ng-isolate-scope\" ctdynamicdata data=\"iframeScope.dynamicShortcodesImageMode\" callback=\"iframeScope.ouDynamicUVPoster\">data</div>
				</div>
			</div>",
			'image'
		);
		$poster->setParam('heading', __('Poster') );
		$poster->setParam('css', false );
		$poster->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_video_video_source']=='el'");
		$poster->rebuildElementOnChange();
	}
	
	function ouVideoImage() {
		$videoImg = $this->addControlSection('vimage_section', __('Video Image'), 'assets/path', $this );

		$customImg = $videoImg->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Add Custom Image', 'oxy-ultimate'),
			'slug' 		=> 'custom_img',
			'value' 	=> [
				'yes' 		=> __('Yes'), 
				'no' 		=> __('No')
			],
			'default' 	=> 'no',
			'css' 		=> false
		]);

		$imgURL = $videoImg->addCustomControl(
			"<div class=\"oxygen-control  not-available-for-classes not-available-for-media\">			
				<div class=\"oxygen-file-input\">
					<input type=\"text\" spellcheck=\"false\" ng-change=\"iframeScope.setOption(iframeScope.component.active.id,'ct_image','oxy-ou_video_image'); iframeScope.parseImageShortcode()\" ng-class=\"{'oxygen-option-default':iframeScope.isInherited(iframeScope.component.active.id, 'oxy-ou_video_image')}\" ng-model=\"iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_video_image']\" ng-model-options=\"{ debounce: 10 }\" class=\"ng-pristine ng-valid oxygen-option-default ng-touched\">
					<div class=\"oxygen-file-input-browse\" data-mediatitle=\"Select Image\" data-mediabutton=\"Select Image\" data-mediaproperty=\"oxy-ou_video_image\" data-mediatype=\"mediaUrl\" data-returnvalue=\"url\">browse</div>
					<div class=\"oxygen-dynamic-data-browse ng-isolate-scope\" ctdynamicdata data=\"iframeScope.dynamicShortcodesImageMode\" callback=\"iframeScope.ouDynamicUVImage\">data</div>
				</div>
			</div>",
			'image'
		);
		$imgURL->setParam('heading', __('Image') );
		$imgURL->setParam('css', false );
		$imgURL->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_video_custom_img']=='yes'");
		$imgURL->rebuildElementOnChange();

		$playIcon = $videoImg->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Display Custom Play Icon', 'oxy-ultimate'),
			'slug' 		=> 'play_icon',
			'value' 	=> [
				'show' 		=> __('Show'), 
				'hide' 		=> __('Hide')
			],
			'default' 	=> 'no',
			'css' 		=> false
		]);
		$playIcon->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_video_custom_img']=='yes'");

		$lightbox = $videoImg->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Enable Lightbox', 'oxy-ultimate'),
			'slug' 		=> 'video_lightbox',
			'value' 	=> [
				'yes' 		=> __('Yes'), 
				'no' 		=> __('No')
			],
			'default' 	=> 'no',
			'css' 		=> false
		]);
		$lightbox->setParam('ng_show', "iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_video_custom_img']=='yes'");

		$videoImg->addOptionControl([
			'type' 		=> 'radio',
			'name' 		=> __('Call to Action Type'),
			'slug' 		=> 'action_type',
			'value' 	=> [ 'default' => __('Default'), 'custom' => __('Custom Button') ],
			'default' 	=> 'default',
			'condition' => 'video_lightbox=yes',
			'css' 		=> false
		])->rebuildElementOnChange();

		$piStyle = $videoImg->addControlSection('pi_section', __('Play Icon Style'), 'assets/path', $this );

		$psize = $piStyle->addStyleControl([
			'name' 			=> __('Size'),
			'selector' 		=> '.ou-video-play-btn svg',
			'property' 		=> 'width|height',
			'slug' 			=> 'pi_size',
			'control_type' 	=> 'slider-measurebox'
		]);
		$psize->setUnits('px', 'px');
		$psize->setRange( '20', '100', '10');
		$psize->rebuildElementOnChange();

		$piStyle->addStyleControls([
			[
				'selector' 		=> '.ou-video-play-btn',
				'property' 		=> 'background-color',
				'slug' 			=> 'pi_bgclr',
			],
			[
				'name' 			=> __('Hover Background Color'),
				'selector' 		=> '.ou-video-play-btn:hover',
				'property' 		=> 'background-color',
				'slug' 			=> 'pi_hbgclr',
			],
			[
				'selector' 		=> '.ou-video-play-btn svg',
				'name' 			=> __('Color'),
				'property' 		=> 'fill',
				'control_type' 	=> 'colorpicker',
				'slug' 			=> 'pi_clr',
			],
			[
				'name' 			=> __('Hover Color'),
				'selector' 		=> '.ou-video-play-btn:hover svg',
				'property' 		=> 'fill',
				'control_type' 	=> 'colorpicker',
				'slug' 			=> 'pi_hclr',
			]
		]);

		$piBorder = $videoImg->borderSection( __('Play Icon Border', "oxy-ultimate"), '.ou-video-play-btn', $this );
		$piBorder->addStyleControl([
			'name' 			=> __('Hover Border Color'),
			'selector' 		=> '.ou-video-play-btn:hover',
			'property' 		=> 'border-color',
			'slug' 			=> 'pi_hbrdclr',
		]);

		$lgboxStyle = $videoImg->addControlSection('lightbox_section', __('Lightbox Style'), 'assets/path', $this );

		$lgboxStyle->addStyleControls([
			[
				'selector' 		=> '.fancybox-bg',
				'property' 		=> 'background-color',
				'slug' 			=> 'fb_bgclr',
			],
			[
				'selector' 		=> '.fancybox-is-open .fancybox-bg',
				'property' 		=> 'opacity',
				'slug' 			=> 'fb_opacity',
			]
		]);

		$closeBtn = $videoImg->addControlSection('lightbox_closebtn', __('Close Button'), 'assets/path', $this );

		$closeBtn->addStyleControls([
			[
				'selector' 		=> '.fancybox-close-small',
				'name' 			=> __('Background Color'),
				'property' 		=> 'background-color',
				'slug' 			=> 'fbc_bgclr',
			],
			[
				'name' 			=> __('Close Button Hover Color'),
				'selector' 		=> '.fancybox-close-small:hover',
				'property' 		=> 'background-color',
				'slug' 			=> 'fbc_bghclr',
			],
			[
				'selector' 		=> '.fancybox-close-small',
				'name' 			=> __('Color'),
				'property' 		=> 'color',
				'slug' 			=> 'fbc_clr',
			],
			[
				'name' 			=> __('Hover Color'),
				'selector' 		=> '.fancybox-close-small:hover',
				'property' 		=> 'color',
				'slug' 			=> 'fbc_hclr',
			]
		]);

		$fbcwh = $closeBtn->addStyleControl([
			'name' 			=> __('Button Wrapper Width & Height'),
			'selector' 		=> '.fancybox-close-small',
			'property' 		=> 'width|height',
			'slug' 			=> 'fbc_wh',
			'control_type' 	=> 'slider-measurebox'
		]);
		$fbcwh->setUnits('px', 'px');
		$fbcwh->setRange( '20', '100', '10');
		$fbcwh->setDefaultValue(44);

		$fbcsize = $closeBtn->addStyleControl([
			'name' 			=> __('Button Size'),
			'selector' 		=> '.fancybox-close-small svg',
			'property' 		=> 'width|height',
			'slug' 			=> 'fbc_size',
			'control_type' 	=> 'slider-measurebox'
		]);
		$fbcsize->setUnits('px', 'px');
		$fbcsize->setRange( '20', '100', '10');

		$fbcpost = $closeBtn->addStyleControl([
			'name' 			=> __('Button Position Top'),
			'selector' 		=> '.fancybox-close-small',
			'property' 		=> 'top',
			'slug' 			=> 'fbc_postop',
			'control_type' 	=> 'slider-measurebox'
		]);
		$fbcpost->setUnits('px', 'px');
		$fbcpost->setRange( '-100', '100', '5');
		$fbcpost->setDefaultValue(-24);

		$fbcposr = $closeBtn->addStyleControl([
			'name' 			=> __('Button Position Right'),
			'selector' 		=> '.fancybox-close-small',
			'property' 		=> 'right',
			'slug' 			=> 'fbc_posright',
			'control_type' 	=> 'slider-measurebox'
		]);
		$fbcposr->setUnits('px', 'px');
		$fbcposr->setRange( '-100', '100', '5');
		$fbcposr->setDefaultValue(-24);
	}

	function controls() {
		$this->ouVideoConfig();

		$this->ouVideoOptions();

		$this->ouVideoImage();
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
		$videoURL = $this->get_video_url( $options );
		if( ! empty( $videoURL ) )
		{
			$videoHTML =  $this->get_video_content( $options, $videoURL );
			$aspect_ratio = isset($options['aspect_ratio']) ? $options['aspect_ratio'] : '169';
			$uid = str_replace( ".", "", uniqid( 'ouv-', true ) );
			
			if( ! empty( $videoHTML ) ) 
			{
				//ouv' . $options['selector'] . '-' . get_the_ID() . ' 

				if( isset( $options['action_type'] ) && $options['action_type'] !== 'default' ) :
					echo '<div class="ou-video-wrapper ou-video-lightbox oxy-inner-content '. $uid . ' ouv-ar-' . $aspect_ratio .'">';
					if ( ! defined('OXY_ELEMENTS_API_AJAX') ) :
						wp_enqueue_style('ouv-fancybox');
						wp_enqueue_script('ouv-fancybox-script');
						wp_enqueue_script('ouv-frontend-script');
						$this->El->inlineJS("
							jQuery(document).ready(function($) {
								new OUVideo({
									id: '". $uid ."',
									type: '".$options['video_source']."',
									aspectRatio: '".$aspect_ratio ."',
									lightbox: true,
									overlay: false,
									selector: '". $options['selector'] . "'
								});
							});
						");
					endif;
				?>
					<div class="ou-video-image-overlay">
						<?php 
							if( $content ) {
								if( function_exists('do_oxygen_elements') )
									echo do_oxygen_elements( $content );
								else
									echo do_shortcode( $content );
							}

							if( $this->has_lightbox( $options ) ) {
								echo '<script type="text/html" class="ou-video-lightbox-content">';
								echo '<div class="ou-video-container"><div class="ou-aspect-ratio"><button data-fancybox-close="" class="fancybox-close-small" title="Close"><svg viewBox="0 0 32 32"><path d="M10,10 L22,22 M22,10 L10,22"></path></svg></button>';
								echo $videoHTML;
								echo '</div></div>';
								echo '</script>';
							}
						?>
					</div>
				<?php
					echo '</div>';
				else:
					echo '<div class="ou-video-wrapper '. $uid . ' ouv-ar-' . $aspect_ratio .'">';
					echo '<div class="ou-aspect-ratio">';			

					if ( $this->has_image_overlay( $options ) ) 
					{
						$overlay_attrs['class'] = 'ou-video-image-overlay';
						if( isset( $options['image'] ) ) {
							$overlayImage = $this->fetchDynamicData( $options['image'] );
							$overlay_attrs['style'] = 'background-image: url(' . esc_url( $overlayImage ) . ');';
						}

						if ( $this->has_lightbox( $options ) ) 
						{
							if ( ! defined('OXY_ELEMENTS_API_AJAX') ) :
								wp_enqueue_style('ouv-fancybox');
								wp_enqueue_script('ouv-fancybox-script');
								wp_enqueue_script('ouv-frontend-script');
								$this->El->inlineJS("
									jQuery(document).ready(function($) {
										new OUVideo({
											id: '". $uid ."',
											type: '".$options['video_source']."',
											aspectRatio: '".$aspect_ratio ."',
											lightbox: true,
											overlay: false,
											selector: '". $options['selector'] . "'
										});
									});
								");
							endif;
						} else {
							if ( ! defined('OXY_ELEMENTS_API_AJAX') ) :
								wp_enqueue_script('ouv-frontend-script');
								$this->El->inlineJS("
									jQuery(document).ready(function($) {
										new OUVideo({
											id: '". $uid ."',
											type: '".$options['video_source']."',
											aspectRatio: '".$aspect_ratio ."',
											lightbox: false,
											overlay: true,
										});
									});
								");
							endif;

							echo $videoHTML;
						}
					?>
						<div <?php echo $this->ou_video_attributes( $overlay_attrs ); ?>>
							<?php 
								if( $this->has_lightbox( $options ) ) {
									echo '<script type="text/html" class="ou-video-lightbox-content">';
									echo '<div class="ou-video-container"><div class="ou-aspect-ratio"><button data-fancybox-close="" class="fancybox-close-small" title="Close"><svg viewBox="0 0 32 32"><path d="M10,10 L22,22 M22,10 L10,22"></path></svg></button>';
									echo $videoHTML;
									echo '</div></div>';
									echo '</script>';
								}
							?>
							<?php if ( 'show' === $options['play_icon'] ) { ?>
								<div class="ou-video-play-btn" role="button">
									<?php echo file_get_contents(__DIR__.'/play-button.svg' ); ?>
									<span class="ou-screen-only"><?php _e( 'Play Video', 'oxy-ultimate' ); ?></span>
								</div>
							<?php } ?>
						</div>
					<?php
					} else {

						echo $videoHTML;

					}
					echo '</div></div>';
				endif;
			}
		}
	}

	function has_image_overlay( $options ) {
		if( isset( $options['custom_img'] ) && $options['custom_img'] == 'yes' && isset( $options['image'] ) )
			return true;
		else
			return false;
	}

	function has_lightbox( $options ) {
		if( isset( $options['video_lightbox'] ) && $options['video_lightbox'] == 'yes' )
			return true;
		else
			return false;
	}

	function ou_video_attributes( array $attributes ) {
		$rendered_attributes = array();

		foreach ( $attributes as $attribute_key => $attribute_values ) {
			if ( is_array( $attribute_values ) ) {
				$attribute_values = implode( ' ', $attribute_values );
			}

			$rendered_attributes[] = sprintf( '%1$s="%2$s"', $attribute_key, esc_attr( $attribute_values ) );
		}

		return implode( ' ', $rendered_attributes );
	}

	function ou_render_external_video( $options, $videoURL ) {
		$video_params = array();

		foreach ( array( 'autoplay', 'loop' ) as $option_name ) {
			if ( 'yes' === $options[$option_name] ) {
				$video_params[ $option_name ] = '';
				if ( 'autoplay' == $option_name ) {
					$video_params['webkit-playsinline'] = '';
					$video_params['playsinline'] = '';
				}
			}
		}

		if ( 'yes' === $options['controls'] ) {
			$video_params['controls'] = '';
		}

		if ( 'yes' === $options['mute'] ) {
			$video_params['muted'] = 'muted';
		}

		if ( isset( $options['poster'] ) ) {
			$video_params['poster'] = esc_url( $this->fetchDynamicData( $options['poster'] ) );
			$video_params['preload'] = 'none';
		}
		?>
		<video class="ou-video-player ou-video-iframe" src="<?php echo esc_url( $videoURL ); ?>" <?php echo $this->ou_video_attributes( $video_params ); ?>></video>
		<?php
	}

	function ou_get_embed_params( $options, $videoURL ) {
		$params = array();

		if ( 'yes' === $options['autoplay'] ) {
			$params['autoplay'] = '1';
		}

		$params_dictionary = array();

		if ( 'yt' === $options['video_source'] ) {
			$params_dictionary = array(
				'loop',
				'controls',
				'mute',
				'rel',
				'modestbranding',
			);

			if ( 'yes' === $options['loop'] ) {
				$video_properties = $this->ou_get_video_properties( $videoURL );

				$params['playlist'] = $video_properties['video_id'];
			}

			if( isset( $options['start_time'] ) )
				$params['start'] = $options['start_time'];

			if( isset( $options['end_time'] ) )
				$params['end'] = $options['end_time'];

			$params['wmode'] = 'opaque';
		} elseif ( 'vm' === $options['video_source'] ) {
			$params_dictionary = array(
				'loop',
				'mute' 				=> 'muted',
				'vimeo_title' 		=> 'title',
				'vimeo_portrait' 	=> 'portrait',
				'vimeo_byline' 		=> 'byline',
			);

			if( isset( $options['control_color'] ) )
				$params['color'] = str_replace( '#', '', $options['control_color'] );

			$params['autopause'] = '0';
		} elseif ( 'dm' === $options['video_source'] ) {
			$params_dictionary = array(
				'controls',
				'mute',
				'showinfo' 	=> 'ui-start-screen-info',
				'logo' 		=> 'ui-logo',
			);

			if( isset( $options['control_color'] ) )
				$params['ui-highlight'] = str_replace( '#', '', $options['control_color'] );

			if( isset( $options['start_time'] ) )
				$params['start'] = $options['start_time'];

			$params['endscreen-enable'] = '0';
		}

		foreach ( $params_dictionary as $key => $param_name ) {
			$setting_name = $param_name;

			if ( is_string( $key ) ) {
				$setting_name = $key;
			}

			$setting_value = 'yes' === $options[$setting_name] ? '1' : '0';

			$params[ $param_name ] = $setting_value;
		}

		return $params;
	}

	function ou_get_embed_options( $options, $videoURL ) {
		$embed_options = array();

		if ( 'yt' === $options['video_source'] ) {
			$embed_options['privacy'] = 'yes' === $options['yt_privacy'];
		} elseif ( 'vm' === $options['video_source'] && isset( $options['start_time'] ) ) {
			$embed_options['start'] = $options['start_time'];
		}

		if( isset( $options['aria_label'] ) ) {
			$embed_options['aria-label'] = esc_attr( $options['aria_label'] );
		}

		return $embed_options;
	}

	function ou_get_embed_html( /*$settings,*/ $video_url, $embed_url_params = array(), $options = array(), $frame_attributes = array() ) {
		$default_frame_attributes = array(
			'class' => 'ou-video-iframe',
			'allowfullscreen',
			'allow'	=> 'autoplay',
		);

		$video_embed_url = $this->ou_get_embed_url( $video_url, $embed_url_params, $options );
		if ( ! $video_embed_url ) {
			return null;
		}
		if ( ! $this->has_image_overlay( $options ) || $this->has_lightbox( $options ) ) {
			$default_frame_attributes['src'] = $video_embed_url;
		} else {
			$default_frame_attributes['data-src'] = $video_embed_url;
		}

		$frame_attributes = array_merge( $default_frame_attributes, $frame_attributes );

		$attributes_for_print = array();

		foreach ( $frame_attributes as $attribute_key => $attribute_value ) {
			$attribute_value = esc_attr( $attribute_value );

			if ( is_numeric( $attribute_key ) ) {
				$attributes_for_print[] = $attribute_value;
			} else {
				$attributes_for_print[] = sprintf( '%1$s="%2$s"', $attribute_key, $attribute_value );
			}
		}

		$attributes_for_print = implode( ' ', $attributes_for_print );

		$iframe_html = "<iframe $attributes_for_print></iframe>";

		/** This filter is documented in wp-includes/class-oembed.php */
		return apply_filters( 'oembed_result', $iframe_html, $video_url, $frame_attributes );
	}

	function ou_get_embed_url( $video_url, array $embed_url_params = array(), array $options = array() ) {
		$video_properties = $this->ou_get_video_properties( $video_url );

		if ( ! $video_properties ) {
			return null;
		}

		$embed_patterns = array(
			'youtube' 		=> 'https://www.youtube{NO_COOKIE}.com/embed/{VIDEO_ID}?feature=oembed',
			'vimeo' 		=> 'https://player.vimeo.com/video/{VIDEO_ID}#t={TIME}',
			'dailymotion' 	=> 'https://dailymotion.com/embed/video/{VIDEO_ID}',
		);

		$embed_pattern = $embed_patterns[ $video_properties['provider'] ];

		$replacements = array(
			'{VIDEO_ID}' => $video_properties['video_id'],
		);

		if ( 'youtube' === $video_properties['provider'] ) {
			$replacements['{NO_COOKIE}'] = ! empty( $options['privacy'] ) ? '-nocookie' : '';
		} elseif ( 'vimeo' === $video_properties['provider'] ) {
			$time_text = '';

			if ( ! empty( $options['start'] ) ) {
				$time_text = date( 'H\hi\ms\s', $options['start'] );
			}

			$replacements['{TIME}'] = $time_text;
		}

		$embed_pattern = str_replace( array_keys( $replacements ), $replacements, $embed_pattern );

		return add_query_arg( $embed_url_params, $embed_pattern );
	}

	function ou_get_video_properties( $video_url ) {
		$provider_regex = array(
			'youtube' => '/^.*(?:youtu\.be\/|youtube(?:-nocookie)?\.com\/(?:(?:watch)?\?(?:.*&)?vi?=|(?:embed|v|vi|user)\/))([^\?&\"\'>]+)/',
			'vimeo' => '/^.*vimeo\.com\/(?:[a-z]*\/)*([‌​0-9]{6,11})[?]?.*/',
			'dailymotion' => '/^.*dailymotion.com\/(?:video|hub)\/([^_]+)[^#]*(#video=([^_&]+))?/',
		);

		foreach ( $provider_regex as $provider => $match_mask ) {
			preg_match( $match_mask, $video_url, $matches );

			if ( $matches ) {
				return array(
					'provider' => $provider,
					'video_id' => $matches[1],
				);
			}
		}

		return null;
	}

	function get_video_content( $options, $videoURL ) {
		$video_html = '';

		if ( 'el' === $options['video_source'] ) {
			ob_start();

			$this->ou_render_external_video( $options, $videoURL );

			$video_html = ob_get_clean();
		} else {
			$embed_params = $this->ou_get_embed_params( $options, $videoURL );

			$embed_options = $this->ou_get_embed_options( $options, $videoURL );

			$video_html = $this->ou_get_embed_html( /*$settings,*/ $videoURL, $embed_params, $options, $embed_options );
		}

		return $video_html;
	}

	function get_video_url( $options ) {
		$videoType = $options['video_source'];

		if( $videoType == 'el' || $videoType == 'sh' ) {
			return $this->ou_get_external_link( $options );
		} elseif( isset( $options['vurl'] ) ) {
			return $this->fetchDynamicData( $options['vurl'] );
		} else {
			return;
		}
	}

	function ou_get_external_link( $options ) {

		if ( 'el' === $options['video_source'] && isset( $options['vurl'] )) {
			$video_url = $this->fetchDynamicData( $options['vurl'] );
		}

		if ( empty( $video_url ) ) {
			return false;
		}

		if ( isset( $options['start_time'] ) || isset( $options['end_time'] ) ) {
			$video_url .= '#t=';
		}

		if ( isset( $options['start_time'] ) ) {
			$video_url .= $options['start_time'];
		}

		if ( isset( $options['end_time'] ) ) {
			$video_url .= ',' . $options['end_time'];
		}

		return $video_url;
	}

	function customCSS( $original, $selector ) {
		$css = '';
		if( ! $this->css_added ) {
			$css = file_get_contents(__DIR__.'/'.basename(__FILE__, '.php').'.css');
			$this->css_added = true;
		}

		$prefix = $this->El->get_tag();
		
		if( isset( $original[$prefix .'_pi_size'] ) ) {
			$css .= $selector . ' .ou-video-play-btn{padding: calc( ' . $original[$prefix .'_pi_size'] . 'px / 1.5 );}';
		}

		if( isset( $original[$prefix . '_video_lightbox'] ) && $original[$prefix . '_video_lightbox'] == 'yes' ) {
			$ratio = isset( $original[$prefix . '_aspect_ratio'] ) ? $original[$prefix . '_aspect_ratio'] : '169' ;
			$selc = str_replace('#', '', $selector );
			//$css .= '.fancybox-ouv' . $selc .' .ouv-ar-'. $ratio . ',.fancybox-is-open .fancybox-content{background: none; width: 100%;height: 100%;}';

			if( isset( $original[$prefix . '_fb_bgclr'] ) ) {
				$css .= '.fancybox' . $selc . '.fancybox-is-open .fancybox-bg{background:' . $original[$prefix . '_fb_bgclr'] . ';}';
			}

			if( isset( $original[$prefix . '_fb_opacity'] ) ) {
				$css .= '.fancybox' . $selc . '.fancybox-is-open .fancybox-bg{opacity: '.$original[$prefix . '_fb_opacity'].';}';
			}

			if( isset( $original[$prefix . '_fbc_bgclr'] ) ) {
				$css .= '.fancybox' . $selc . ' .fancybox-content .ou-aspect-ratio .fancybox-close-small{background-color: '.$original[$prefix . '_fbc_bgclr'].';}';
			}

			if( isset( $original[$prefix . '_fbc_bghclr'] ) ) {
				$css .= '.fancybox' . $selc . ' .fancybox-content .ou-aspect-ratio .fancybox-close-small:hover{background-color: '.$original[$prefix . '_fbc_bghclr'].';}';
			}

			if( isset( $original[$prefix . '_fbc_clr'] ) ) {
				$css .= '.fancybox' . $selc . ' .fancybox-content .ou-aspect-ratio .fancybox-close-small{color: '.$original[$prefix . '_fbc_clr'].';}';
			}

			if( isset( $original[$prefix . '_fbc_hclr'] ) ) {
				$css .= '.fancybox' . $selc . ' .fancybox-content .ou-aspect-ratio .fancybox-close-small:hover{color: '.$original[$prefix . '_fbc_hclr'].';}';
			}

			if( isset( $original[$prefix . '_fbc_size'] ) ) {
				$css .= '.fancybox' . $selc . ' .fancybox-content .ou-aspect-ratio .fancybox-close-small svg{width: '.$original[$prefix . '_fbc_size'].'px;height:'.$original[$prefix . '_fbc_size'].'px;}';
			}

			if( isset( $original[$prefix . '_fbc_wh'] ) ) {
				$css .= '.fancybox' . $selc . ' .fancybox-content .ou-aspect-ratio .fancybox-close-small{width: '.$original[$prefix . '_fbc_wh'].'px;height:'.$original[$prefix . '_fbc_wh'].'px;}';
			}

			if( isset( $original[$prefix . '_fbc_postop'] ) ) {
				$css .= '.fancybox' . $selc . ' .fancybox-content .ou-aspect-ratio .fancybox-close-small{top: '.$original[$prefix . '_fbc_postop'].'px;}';
			}

			if( isset( $original[$prefix . '_fbc_posright'] ) ) {
				$css .= '.fancybox' . $selc . ' .fancybox-content .ou-aspect-ratio .fancybox-close-small{right: '.$original[$prefix . '_fbc_posright'].'px;}';
			}
		}

		return $css;
	}
}

new OUVideo();