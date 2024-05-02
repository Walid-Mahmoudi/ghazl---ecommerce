<?php

namespace Oxygen\OxyUltimate;

class OUCountdown extends \OxyUltimateEl
{
	public $has_js = true;
	public $css_added = false;
	public $include_file = false;
	private $countdown_js_code = array();

	function name() {
		return __( "Countdown", 'oxy-ultimate' );
	}

	function slug() {
		return "ou_countdown";
	}

	function oxyu_button_place() {
		return "content";
	}

	function icon() {
		return OXYU_URL . 'assets/images/elements/' . basename(__FILE__, '.php').'.svg';
	}

	function countDownGeneral() 
	{
		$config = $this->addControlSection('timer_config', __('Config'), 'assets/icon.png', $this );

		$config->addOptionControl(
			array(
				'type' 		=> 'radio',
				'name' 		=> __("Timer Type"),
				"slug" 		=> 'oucd_timertype',
				"value" 	=> [ 'fixed' => __("Fixed"),  "evergreen" => __("Evergreen") ],
				"default" 	=> 'fixed'
			)
		);
		$tz = $config->addCustomControl("
			<div class='oxygen-control'>
				<select name=\"oxy-ou_countdown_oucd_tz\" id=\"oxy-ou_countdown_oucd_tz\" 
					ng-init=\"initSelect2('oxy-ou_countdown_oucd_tz','Choose timezone...')\"
					ng-model=\"iframeScope.component.options[iframeScope.component.active.id]['model']['oucd_tz']\"
					ng-change=\"iframeScope.setOption(iframeScope.component.active.id,'oxy-ou_countdown_oucd_tz','oxy-ou_countdown_oucd_tz')\">".
					$this->oxyu_timezones('Asia/Kolkata') . "
				</select>
			</div>",
			'oucd_timezone'
		);
		$tz->setParam('heading', __('Time Zone'));

		$config->addOptionControl(
			array(
				'type' 		=> 'radio',
				'name' 		=> __("Date Time Source"),
				"slug" 		=> 'oucd_source',
				"value" 	=> [ 
					'default' 	=> __("Default"), 
					"acf" 		=> __("ACF"), 
					"acfopt" 	=> __("ACF Option Page", 'oxy-ultimate'), 
					'mb' 		=> __('Metabox', 'oxy-ultimate') 
				],
				"default" 	=> 'default'
			)
		);

		$acfField = $config->addOptionControl(
			array(
				'type' 		=> 'textfield',
				'name' 		=> __("Date/Time Picker Field Name"),
				"slug" 		=> 'oucd_acftimer',
				'condition' => 'oucd_source=acf||oucd_source=mb'
			)
		);
		$acfField->setParam('description', 'You will select the evergreen timer type, when you are using the ACF or Metabox Time Picker field type.');
		$acfField->rebuildElementOnChange();

		$acfopt = $config->addOptionControl(
			array(
				'type' 		=> 'textfield',
				'name' 		=> __("Date/Time Picker Field Name"),
				"slug" 		=> 'oucd_acfopt',
				'condition' => 'oucd_source=acfopt'
			)
		);
		$acfopt->setParam('description', 'Enter the field name of your ACF option page.');
		$acfopt->rebuildElementOnChange();

		$config->addOptionControl(
			array(
				'type' 		=> 'dropdown',
				'name' 		=> __("Year"),
				"slug" 		=> 'oucd_year',
				"value" 	=> $this->oxyu_year(),
				"default" 	=> '2020',
				'condition' => 'oucd_timertype=fixed&&oucd_source=default'
			)
		)->setParam('hide_wrapper_end', true);

		$config->addOptionControl(
			array(
				'type' 		=> 'dropdown',
				'name' 		=> __("Month"),
				"slug" 		=> 'oucd_mo',
				"value" 	=> $this->oxyu_month(),
				"default" 	=> '0',
				'condition' => 'oucd_timertype=fixed&&oucd_source=default'
			)
		)->setParam('hide_wrapper_start', true);

		$config->addOptionControl(
			array(
				'type' 		=> 'dropdown',
				'name' 		=> __("Day"),
				"slug" 		=> 'oucd_date',
				"value" 	=> $this->oxyu_date(),
				'condition' => 'oucd_source=default',
				"default" 	=> '0'
			)
		)->setParam('hide_wrapper_end', true);

		$config->addOptionControl(
			array(
				'type' 		=> 'dropdown',
				'name' 		=> __("Hour"),
				"slug" 		=> 'oucd_hour',
				"value" 	=> $this->oxyu_hour(),
				'condition' => 'oucd_source=default',
				"default" 	=> '0'
			)
		)->setParam('hide_wrapper_start', true);

		$config->addOptionControl(
			array(
				'type' 		=> 'dropdown',
				'name' 		=> __("Minutes"),
				"slug" 		=> 'oucd_minutes',
				"value" 	=> $this->oxyu_minSec('Minute'),
				'condition' => 'oucd_source=default',
				"default" 	=> '0'
			)
		)->setParam('hide_wrapper_end', true);

		$config->addOptionControl(
			array(
				'type' 		=> 'dropdown',
				'name' 		=> __("Seconds"),
				"slug" 		=> 'oucd_seconds',
				"value" 	=> $this->oxyu_minSec('Second'),
				'condition' => 'oucd_source=default',
				"default" 	=> '0'
			)
		)->setParam('hide_wrapper_start', true);
	}

	/******************************
	 * Label Section
	 *****************************/
	function countDownLabels() 
	{
		$labelSec = $this->addControlSection('labels_section', __('Labels'), 'assets/icon.png', $this );

		$showLb = $labelSec->addOptionControl(
			array(
				'type' 		=> 'radio',
				'name' 		=> __("Show Labels"),
				"slug" 		=> 'oucd_displb',
				"value" 	=> [ 'yes' => __("Yes"),  "no" => __("No") ],
				"default" 	=> 'yes'
			)
		);
		$showLb->rebuildElementOnChange();

		$labelSec->addOptionControl(
			array(
				"type" 		=> 'dropdown',
				"name" 		=> __('Labels Tag', "oxy-ultimate"),
				"slug" 		=> 'oucd_lbtag',
				"value" 	=> array(
					'h1' 	=> __('H1', "oxy-ultimate"),
					'h2' 	=> __('H2', "oxy-ultimate"),
					'h3' 	=> __('H3', "oxy-ultimate"),
					'h4' 	=> __('H4', "oxy-ultimate"),
					'h5' 	=> __('H5', "oxy-ultimate"),
					'h6' 	=> __('H6', "oxy-ultimate"),
					'div' 	=> __('DIV', "oxy-ultimate"),
					'p' 	=> __('P', "oxy-ultimate"),
				),
				"default" 	=> 'div',
				'condition' => 'oucd_displb=yes',
				"css" 		=> false
			)
		);

		$labelSec->addOptionControl(
			array(
				'type' 		=> 'radio',
				'name' 		=> __("Label Position"),
				"slug" 		=> 'label_position',
				"value" 	=> [ 
					'below' 	=> __( 'Below Digit', "oxy-ultimate" ),
					'above' 	=> __( 'Above Digit', "oxy-ultimate" ),
					'right' 	=> __( 'Right Side of Digit', "oxy-ultimate" ),
					'left' 		=> __( 'Left Side of Digit', "oxy-ultimate" ),	
				],
				"default" 	=> 'below'
			)
		)->rebuildElementOnChange();

		//* Year
		$yearSec = $labelSec->addControlSection('year_section', __('Year'), 'assets/icon.png', $this );
		$yearSec->addOptionControl(
			array(
				'type' 		=> 'radio',
				'name' 		=> __("Show Year"),
				"slug" 		=> 'oucd_dispyear',
				"value" 	=> [ 'Y' => __("Yes"),  "no" => __("No") ],
				"default" 	=> 'Y',
				'condition' => 'oucd_displb=yes'
			)
		)->rebuildElementOnChange();

		$yearSec->addOptionControl(
			array(
				'type' 		=> 'textfield',
				'name' 		=> __("Label in Singular"),
				"slug" 		=> 'oucd_yearlblsing',
				"value" 	=> 'Year',
				'condition' => 'oucd_dispyear=Y'
			)
		);

		$yearSec->addOptionControl(
			array(
				'type' 		=> 'textfield',
				'name' 		=> __("Label in Plural"),
				"slug" 		=> 'oucd_yearlblplur',
				"value" 	=> 'Years',
				'condition' => 'oucd_dispyear=Y'
			)
		);

		//* Month
		$moSec = $labelSec->addControlSection('mo_section', __('Month'), 'assets/icon.png', $this );
		$moSec->addOptionControl(
			array(
				'type' 		=> 'radio',
				'name' 		=> __("Show Month"),
				"slug" 		=> 'oucd_dispmo',
				"value" 	=> [ 'O' => __("Yes"),  "no" => __("No") ],
				"default" 	=> 'O',
				'condition' => 'oucd_displb=yes'
			)
		)->rebuildElementOnChange();

		$moSec->addOptionControl(
			array(
				'type' 		=> 'textfield',
				'name' 		=> __("Label in Singular"),
				"slug" 		=> 'oucd_molblsing',
				"value" 	=> 'Month',
				'condition' => 'oucd_dispmo=O'
			)
		);

		$moSec->addOptionControl(
			array(
				'type' 		=> 'textfield',
				'name' 		=> __("Label in Plural"),
				"slug" 		=> 'oucd_molblplur',
				"value" 	=> 'Months',
				'condition' => 'oucd_dispmo=O'
			)
		);

		//* Day
		$daySec = $labelSec->addControlSection('day_section', __('Day'), 'assets/icon.png', $this );
		$daySec->addOptionControl(
			array(
				'type' 		=> 'radio',
				'name' 		=> __("Show Day"),
				"slug" 		=> 'oucd_dispday',
				"value" 	=> [ 'D' => __("Yes"),  "no" => __("No") ],
				"default" 	=> 'D',
				'condition' => 'oucd_displb=yes'
			)
		)->rebuildElementOnChange();

		$daySec->addOptionControl(
			array(
				'type' 		=> 'textfield',
				'name' 		=> __("Label in Singular"),
				"slug" 		=> 'oucd_daylblsing',
				"value" 	=> 'Day',
				'condition' => 'oucd_dispday=D'
			)
		);

		$daySec->addOptionControl(
			array(
				'type' 		=> 'textfield',
				'name' 		=> __("Label in Plural"),
				"slug" 		=> 'oucd_daylblplur',
				"value" 	=> 'Days',
				'condition' => 'oucd_dispday=D'
			)
		);

		//* Hours
		$hrSec = $labelSec->addControlSection('hr_section', __('Hour'), 'assets/icon.png', $this );
		$hrSec->addOptionControl(
			array(
				'type' 		=> 'radio',
				'name' 		=> __("Show Hour"),
				"slug" 		=> 'oucd_disphr',
				"value" 	=> [ 'H' => __("Yes"),  "no" => __("No") ],
				"default" 	=> 'H',
				'condition' => 'oucd_displb=yes'
			)
		)->rebuildElementOnChange();

		$hrSec->addOptionControl(
			array(
				'type' 		=> 'textfield',
				'name' 		=> __("Label in Singular"),
				"slug" 		=> 'oucd_hrlblsing',
				"value" 	=> 'Hour',
				'condition' => 'oucd_disphr=H'
			)
		);

		$hrSec->addOptionControl(
			array(
				'type' 		=> 'textfield',
				'name' 		=> __("Label in Plural"),
				"slug" 		=> 'oucd_hrlblplur',
				"value" 	=> 'Hours',
				'condition' => 'oucd_disphr=H'
			)
		);

		//* Minutes
		$minSec = $labelSec->addControlSection('min_section', __('Minute'), 'assets/icon.png', $this );
		$minSec->addOptionControl(
			array(
				'type' 		=> 'radio',
				'name' 		=> __("Show Minutes"),
				"slug" 		=> 'oucd_dispmin',
				"value" 	=> [ 'M' => __("Yes"),  "no" => __("No") ],
				"default" 	=> 'M',
				'condition' => 'oucd_displb=yes'
			)
		)->rebuildElementOnChange();

		$minSec->addOptionControl(
			array(
				'type' 		=> 'textfield',
				'name' 		=> __("Label in Singular"),
				"slug" 		=> 'oucd_minlblsing',
				"value" 	=> 'Minute',
				'condition' => 'oucd_dispmin=M'
			)
		);

		$minSec->addOptionControl(
			array(
				'type' 		=> 'textfield',
				'name' 		=> __("Label in Plural"),
				"slug" 		=> 'oucd_minlblplur',
				"value" 	=> 'Minutes',
				'condition' => 'oucd_dispmin=M'
			)
		);

		//* Seconds
		$scndSec = $labelSec->addControlSection('scnd_section', __('Second'), 'assets/icon.png', $this );
		$scndSec->addOptionControl(
			array(
				'type' 		=> 'radio',
				'name' 		=> __("Show Seconds"),
				"slug" 		=> 'oucd_dispscnd',
				"value" 	=> [ 'S' => __("Yes"),  "no" => __("No") ],
				"default" 	=> 'S',
				'condition' => 'oucd_displb=yes'
			)
		)->rebuildElementOnChange();

		$scndSec->addOptionControl(
			array(
				'type' 		=> 'textfield',
				'name' 		=> __("Label in Singular"),
				"slug" 		=> 'oucd_scndlblsing',
				"value" 	=> 'Seconds',
				'condition' => 'oucd_dispscnd=S'
			)
		);

		$scndSec->addOptionControl(
			array(
				'type' 		=> 'textfield',
				'name' 		=> __("Label in Plural"),
				"slug" 		=> 'oucd_scndlblplur',
				"value" 	=> 'Seconds',
				'condition' => 'oucd_dispscnd=S'
			)
		);

		$spacing = $labelSec->addControlSection('spc_section', __('Spacing & Color'), 'assets/icon.png', $this );

		$spacing->addPreset(
			"padding",
			"lbls_padding",
			__("Padding"),
			'.ou-countdown-label-wrapper'
		)->whiteList();

		$spacing->addPreset(
			"margin",
			"lbls_margin",
			__("Margin"),
			'.ou-countdown-label-wrapper'
		)->whiteList();

		$spacing->addStyleControl([
			'name' 		=> __('Width'),
			'selector' 	=> '.ou-countdown-label-wrapper',
			'property' 	=> 'width',
			'slug' 		=> 'oucd_lblwidth',
			'control_type' 	=> 'slider-measurebox'
		])->setRange('0', '500', '10')->setUnits('px', 'px');

		$spacing->addStyleControl([
			'selector' 	=> '.ou-countdown-label-wrapper',
			'property' 	=> 'background-color',
			'slug' 		=> 'oucd_lblsbg'
		]);

		$labelSec->typographySection(__('Typography'), '.ou-countdown-label', $this );
		$labelSec->borderSection(__('Borders'), '.ou-countdown-label-wrapper', $this );
	}

	/******************************
	 * Action Section
	 *****************************/
	function countDownAction() 
	{
		$actSec = $this->addControlSection('action_section', __('Action'), 'assets/icon.png', $this );
		$actSec->addOptionControl(
			array(
				'type' 		=> 'radio',
				'name' 		=> __("Action after time expires", 'oxy-ultimate'),
				"slug" 		=> 'oucd_expaction',
				"value" 	=> array(
					'none' 		=> __( 'None', 'oxy-ultimate' ),
					'hide' 		=> __( 'Hide Timer', 'oxy-ultimate' ),
					'msg' 		=> __( 'Display Message', 'oxy-ultimate' ),
					'redirect' 	=> __( 'Redirect to URL', 'oxy-ultimate' )
				),
				"default"		=> 'none',
				"condition" 	=> 'oucd_timertype=fixed'
			)
		);

		$actSec->addOptionControl(
			array(
				'type' 		=> 'radio',
				'name' 		=> __("Action after time expires", 'oxy-ultimate'),
				"slug" 		=> 'evergreen_timer_action',
				"value" 	=> array(
					'none' 		=> __( 'None', 'oxy-ultimate' ),
					'hide' 		=> __( 'Hide Timer', 'oxy-ultimate' ),
					'reset'     => __( 'Reset Timer', 'oxy-ultimate' ),
					'msg' 		=> __( 'Display Message', 'oxy-ultimate' ),
					'redirect' 	=> __( 'Redirect to URL', 'oxy-ultimate' )
				),
				"default"		=> 'none',
				"condition" 	=> 'oucd_timertype=evergreen'
			)
		);

		$message = $actSec->addOptionControl(
			array(
				'type' 		=> 'textarea',
				'name' 		=> __("Message", 'oxy-ultimate'),
				'slug' 		=> 'expire_message'
			)
		);
		$message->setParam('ng_show', "(iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_countdown_oucd_expaction']=='msg'||iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_countdown_evergreen_timer_action']=='msg')");

		$redirect_link = $actSec->addCustomControl(
			'<div class="oxygen-file-input oxygen-option-default" ng-class="{\'oxygen-option-default\':iframeScope.isInherited(iframeScope.component.active.id, \'oxy-ou_countdown_redirect_link\')}">
				<input type="text" spellcheck="false" ng-model="iframeScope.component.options[iframeScope.component.active.id][\'model\'][\'oxy-ou_countdown_redirect_link\']" ng-model-options="{ debounce: 10 }" ng-change="iframeScope.setOption(iframeScope.component.active.id, iframeScope.component.active.name,\'oxy-ou_countdown_redirect_link\');iframeScope.checkResizeBoxOptions(\'oxy-ou_countdown_redirect_link\'); " class="ng-pristine ng-valid ng-touched" placeholder="http://">
				<div class="oxygen-set-link" data-linkproperty="url" data-linktarget="target" onclick="processOULink(\'oxy-ou_countdown_redirect_link\')">set</div>
				<div class="oxygen-dynamic-data-browse" ctdynamicdata data="iframeScope.dynamicShortcodesLinkMode" callback="iframeScope.ouDynamicOUCDRUrl">data</div>
			</div>
			',
			"redirect_link"
		);
		$redirect_link->setParam( 'heading', __('Redirect URL', 'oxy-ultimate') );
		$redirect_link->setParam('ng_show', "(iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_countdown_oucd_expaction']=='redirect'||iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_countdown_evergreen_timer_action']=='redirect')");

		$actSec->addOptionControl(
			array(
				"name" 			=> __('Target', "oxy-ultimate"),
				"slug" 			=> "redirect_link_target",
				"type" 			=> 'radio',
				"value" 		=> ["_self" => __("Same Window") , "_blank" => __("New Window")],
				"default"		=> "_self"
			)
		)->setParam('ng_show', "(iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_countdown_oucd_expaction']=='redirect'||iframeScope.component.options[iframeScope.component.active.id]['model']['oxy-ou_countdown_evergreen_timer_action']=='redirect')");
	}


	/******************************
	 * Separator Style
	 *****************************/

	function countDownSep() {
		$sepStyle = $this->addControlSection('style_section', __('Separator', "oxy-ultimate"), 'assets/icon.png', $this );

		$dspSep = $sepStyle->addOptionControl(
			array(
				'type' 		=> 'radio',
				'name' 		=> __("Display between blocks", "oxy-ultimate"),
				"slug" 		=> 'oucd_dispsep',
				"value" 	=> [ 'yes' => __("Yes"),  "no" => __("No") ],
				"default" 	=> 'no'
			)
		);
		$dspSep->rebuildElementOnChange();

		$sepType = $sepStyle->addOptionControl(
			array(
				'type' 		=> 'radio',
				'name' 		=> __("Type", "oxy-ultimate"),
				"slug" 		=> 'oucd_septype',
				"value" 	=> [ 'colon' => __("Colon"),  "line" => __("Line") ],
				"default" 	=> 'line',
				'condition' => 'oucd_dispsep=yes'
			)
		);
		//$sepType->setParam('description', 'Click on Apply Params button to see the output.');
		$sepType->rebuildElementOnChange();

		//* Colon properties
		$sepStyle->addStyleControls([
			[
				'name' 		=> __('Size'),
				'selector' 	=> '.ou-countdown-separator-colon .ou-countdown-digit-wrapper:after',
				'property' 	=> 'font-size',
				'slug' 		=> 'oucd_colonsize',
				'condition' => 'oucd_dispsep=yes&&oucd_septype=colon'
			],
			[
				'selector' 	=> '.ou-countdown-separator-colon .ou-countdown-digit-wrapper:after',
				'property' 	=> 'color',
				'slug' 		=> 'oucd_lineclr',
				'condition' => 'oucd_dispsep=yes&&oucd_septype=colon'
			]
		]);

		$postop = $sepStyle->addStyleControl([
			'name' 		=> __('Position Top'),
			'selector' 	=> '.ou-countdown-separator-colon .ou-countdown-digit-wrapper:after',
			'property' 	=> 'top',
			'control_type' 	=> 'measurebox',
			'condition' => 'oucd_dispsep=yes&&oucd_septype=colon'
		]);
		$postop->setRange('0', '100', '1');
		$postop->setUnits('%', 'px,%,em');
		$postop->setDefaultValue( '50' );
		$postop->setParam('hide_wrapper_end', true );

		$posright = $sepStyle->addStyleControl([
			'name' 		=> __('Position Right'),
			'selector' 	=> '.ou-countdown-separator-colon .ou-countdown-digit-wrapper:after',
			'property' 	=> 'right',
			'control_type' 	=> 'measurebox',
			'condition' => 'oucd_dispsep=yes&&oucd_septype=colon'
		]);
		$posright->setRange('0', '100', '1');
		$posright->setUnits('px', 'px,%,em');
		$posright->setDefaultValue( '-5' );
		$posright->setParam('hide_wrapper_start', true );

		//* Line properties
		$sepStyle->addStyleControls([
			[
				'name' 		=> __('Size'),
				'selector' 	=> '.ou-countdown-separator-line .ou-countdown-item:after',
				'property' 	=> 'border-width',
				'slug' 		=> 'oucd_linesize',
				'condition' => 'oucd_dispsep=yes&&oucd_septype=line'
			],
			[
				'selector' 	=> '.ou-countdown-separator-line .ou-countdown-item:after',
				'property' 	=> 'border-color',
				'slug' 		=> 'oucd_lineclr',
				'condition' => 'oucd_dispsep=yes&&oucd_septype=line'
			]
		]);

		$sepStyle->addOptionControl(
			array(
				'type' 		=> 'radio',
				'name' 		=> __("Hide on mobile", "oxy-ultimate"),
				"slug" 		=> 'oucd_hidesep',
				"value" 	=> [ 'yes' => __("Yes"),  "no" => __("No") ],
				"default" 	=> 'no',
				'condition' => 'oucd_dispsep=yes'
			)
		);
	}

	/******************************
	 * Digit Style
	 *****************************/
	function countDownDigits() {
		//* Digits
		$digit = $this->addControlSection('digit_section', __('Digit Styles', "oxy-ultimate"), 'assets/icon.png', $this );

		$digtag = $digit->addOptionControl(
			array(
				"type" 		=> 'dropdown',
				"name" 		=> __('Digit Tag', "oxy-ultimate"),
				"slug" 		=> 'oucd_dtag',
				"value" 	=> array(
					'h1' 	=> __('H1', "oxy-ultimate"),
					'h2' 	=> __('H2', "oxy-ultimate"),
					'h3' 	=> __('H3', "oxy-ultimate"),
					'h4' 	=> __('H4', "oxy-ultimate"),
					'h5' 	=> __('H5', "oxy-ultimate"),
					'h6' 	=> __('H6', "oxy-ultimate"),
					'div' 	=> __('DIV', "oxy-ultimate"),
					'p' 	=> __('P', "oxy-ultimate"),
				),
				"default" 	=> 'h3',
				"css" 		=> false
			)
		);
		$digtag->rebuildElementOnChange();

		$digit->addStyleControl([
			'selector' 	=> '.ou-countdown-digit-wrapper',
			'property' 	=> 'background-color',
			'slug' 		=> 'oucd_dgtbg'
		]);

		//* Spacing
		$dgtSp = $digit->addControlSection('dgtsp_section', __('Spacing', "oxy-ultimate"), 'assets/icon.png', $this );
		$dgtSp->addStyleControl([
			'name' 		=> __('Width'),
			'selector' 	=> '.ou-countdown-digit-wrapper',
			'property' 	=> 'width',
			'slug' 		=> 'oucd_dgtwidth',
			'control_type' 	=> 'slider-measurebox'
		])->setRange('0', '500', '10')->setUnits('px', 'px');

		$dgtSp->addStyleControl([
			'name' 		=> __('Height'),
			'selector' 	=> '.ou-countdown-digit-wrapper',
			'property' 	=> 'height|line-height',
			'slug' 		=> 'oucd_dgtheight',
			'control_type' 	=> 'slider-measurebox'
		])->setRange('0', '500', '10')->setUnits('px', 'px');

		$dgtSp->addPreset(
			"padding",
			"dgt_padding",
			__("Padding"),
			'.ou-countdown-digit'
		)->whiteList();

		$dgtSp->addPreset(
			"margin",
			"dgt_margin",
			__("Margin"),
			'.ou-countdown-digit-wrapper'
		)->whiteList();

		$digit->typographySection(__('Typography'), '.ou-countdown-digit', $this );
		$digit->borderSection(__('Borders'), '.ou-countdown-digit-wrapper', $this );
		$digit->boxShadowSection(__('Box Shadow'), '.ou-countdown-digit-wrapper', $this );
	}

	/******************************
	 * Block Style
	 *****************************/
	function countDownBlocks() {

		$blocks = $this->addControlSection('block_section', __('Block Styles', "oxy-ultimate"), 'assets/icon.png', $this );

		//* Spacing
		$blckSp = $blocks->addControlSection('blcksp_section', __('Spacing', "oxy-ultimate"), 'assets/icon.png', $this );

		$blckSp->addPreset(
			"padding",
			"block_padding",
			__("Padding"),
			'.ou-countdown-item'
		)->whiteList();

		$blckSp->addPreset(
			"margin",
			"block_margin",
			__("Margin"),
			'.ou-countdown-item'
		)->whiteList();

		$blckSp->addStyleControl([
			'name' 		=> __('Width'),
			'selector' 	=> '.ou-countdown-item',
			'property' 	=> 'width',
			'slug' 		=> 'oucd_blckwidth',
			'control_type' 	=> 'slider-measurebox'
		])->setRange('0', '500', '10')->setUnits('px', 'px');

		$blckSp->addStyleControl([
			'name' 		=> __('Height'),
			'selector' 	=> '.ou-countdown-item',
			'property' 	=> 'height',
			'slug' 		=> 'oucd_blckheight',
			'control_type' 	=> 'slider-measurebox'
		])->setRange('0', '500', '10')->setUnits('px', 'px');

		$blocks->addStyleControl([
			'selector' 	=> '.ou-countdown-item',
			'property' 	=> 'background-color',
			'slug' 		=> 'oucd_blckbg'
		]);

		$blocks->borderSection(__('Borders'), '.ou-countdown-item', $this );
		$blocks->boxShadowSection(__('Box Shadow'), '.ou-countdown-item', $this );
	}

	function controls() {

		$this->addCustomControl(
			__('<div class="oxygen-option-default" style="color: #c3c5c7; line-height: 1.3; font-size: 14px;">If changes are not applying on Builder editor, you will click on the <span style="color:#ff7171;">Apply Params</span> button to get the correct preview.</div>'), 
			'description'
		)->setParam('heading', 'Note:');

		
		$this->countDownGeneral();

		$this->countDownAction();
		
		$this->countDownLabels();

		$this->countDownDigits();

		$this->countDownBlocks();

		$this->countDownSep();
	}

	function render( $options, $content, $defaults ) {
		$uid = str_replace("-", "", $options['selector'] ) . get_the_ID();
		$sep = '';

		if ( isset( $options['oucd_dispsep'] ) && 'yes' == $options['oucd_dispsep'] ) { 
			$sep = ' ou-countdown-separator-' . $options['oucd_septype']; 
		}

		echo '<div class="ou-countdown countdown-'. $uid .' ou-countdown-'. $options['oucd_timertype'] .'-timer'.$sep.'"></div>';

		ob_start();
		
		$this->getCountdownScript( $options, $uid );

		$js = ob_get_clean();

		if ( isset( $_GET['oxygen_iframe'] ) || defined('OXY_ELEMENTS_API_AJAX') )
		{
			$this->oucd_enqueue_scripts();

			if ( 'evergreen' == $options['oucd_timertype'] ) { wp_enqueue_script('oucd-cookie-script'); }
			$this->El->builderInlineJS( $js );
		} else {
			
			add_action( 'wp_footer', array( $this, 'oucd_enqueue_scripts' ) );

			$this->countdown_js_code[] = $js;
			$this->El->footerJS( join('', $this->countdown_js_code) );
		}
	}

	function customCSS( $original, $selector ) {
		$css = '';
		if( ! $this->css_added ) {
			$css = file_get_contents(__DIR__.'/'.basename(__FILE__, '.php').'.css');
			$this->css_added = true;
		}

		$prefix = $this->El->get_tag();

		if( isset( $original[ $prefix . '_oucd_dispsep' ] ) && $original[ $prefix . '_oucd_dispsep' ] == 'yes' ) {
			if ( isset( $original[$prefix . '_oucd_blckspace'] ) && isset( $original[$prefix . '_oucd_septype'] ) && $original[$prefix . '_oucd_septype'] == 'line' ) {
				$css.= $selector . ' .ou-countdown-separator-line .ou-countdown-item { padding-left: '. ( $original[$prefix . '_oucd_blckspace'] / 2 )  .'px; padding-right: '. ( $original[$prefix . '_oucd_blckspace'] / 2 ) . 'px; }';
			}

			if ( isset( $original[$prefix . '_oucd_blckspace'] ) && isset( $original[$prefix . '_oucd_septype'] ) && $original[$prefix . '_oucd_septype'] == 'colon' ) {
				$css.= $selector . ' .ou-countdown-separator-colon .ou-countdown-digit-wrapper:after { right: -'. ( $original[$prefix . '_oucd_sepcolonw'] / 2 )  .'px; }';
			}
		}

		if ( isset( $original[$prefix . '_label_position'] ) && ( 'right' == $original[$prefix . '_label_position'] || 'left' == $original[$prefix . '_label_position'] ) ) {
			$css .= $selector . ' .ou-countdown-item.label-position-right,' . $selector . ' .ou-countdown-item.label-position-left {display: inline-flex; align-items: center;}';
			if ('right' == $original[$prefix . '_label_position'] ) {
				$css .= $selector . ' .ou-countdown-item.label-position-right{ direction: rtl; }';
			}

			if ('left' == $original[$prefix . '_label_position'] ) {
				$css .= $selector . ' .ou-countdown-item.label-position-left{ direction: ltr; }';
			}
		}

		if( isset( $original[ $prefix . '_oucd_hidesep' ] ) && $original[ $prefix . '_oucd_hidesep' ] == 'yes' ) {
			$css .= '@media only screen and ( max-width: 768px ) {
						' . $selector .' .ou-countdown-separator-colon .ou-countdown-item:after,
						' . $selector .' .ou-countdown-separator-line .ou-countdown-item:after {
							display: none;
						}
						' . $selector .' .ou-countdown-separator-line .ou-countdown-item,
						' . $selector .' .ou-countdown-separator-colon .ou-countdown-item {
							padding-left: ' . $original[$prefix . '_oucd_blckspace'] . 'px;
							padding-right: ' . $original[$prefix . '_oucd_blckspace'] . 'px;
						}
					}';
		}

		return $css;
	}

	function oxyu_timezones( $selected_zone ) {

		$continents = array( 'Africa', 'America', 'Antarctica', 'Arctic', 'Asia', 'Atlantic', 'Australia', 'Europe', 'Indian', 'Pacific' );

		$zonen = array();
		foreach ( timezone_identifiers_list() as $zone ) {
			$zone = explode( '/', $zone );
			if ( ! in_array( $zone[0], $continents ) ) {
				continue;
			}

			$exists    = array(
				0 => ( isset( $zone[0] ) && $zone[0] ),
				1 => ( isset( $zone[1] ) && $zone[1] ),
				2 => ( isset( $zone[2] ) && $zone[2] ),
			);
			$exists[3] = ( $exists[0] && 'Etc' !== $zone[0] );
			$exists[4] = ( $exists[1] && $exists[3] );
			$exists[5] = ( $exists[2] && $exists[3] );

			$zonen[] = array(
				'continent'   => ( $exists[0] ? $zone[0] : '' ),
				'city'        => ( $exists[1] ? $zone[1] : '' ),
				'subcity'     => ( $exists[2] ? $zone[2] : '' ),
				't_continent' => ( $exists[3] ? translate( str_replace( '_', ' ', $zone[0] ), 'continents-cities' ) : '' ), // @codingStandardsIgnoreLine
				't_city'      => ( $exists[4] ? translate( str_replace( '_', ' ', $zone[1] ), 'continents-cities' ) : '' ), // @codingStandardsIgnoreLine
				't_subcity'   => ( $exists[5] ? translate( str_replace( '_', ' ', $zone[2] ), 'continents-cities' ) : '' ), // @codingStandardsIgnoreLine
			);
		}
		usort( $zonen, '_wp_timezone_choice_usort_callback' );

		$structure = array();

		if ( empty( $selected_zone ) ) {
			$structure[] = '<option selected="selected" value="">' . __( 'Select a city', 'oxy-ultimate' ) . '</option>';
		}

		foreach ( $zonen as $key => $zone ) {
			// Build value in an array to join later
			$value = array( $zone['continent'] );

			if ( empty( $zone['city'] ) ) {
				// It's at the continent level (generally won't happen)
				$display = $zone['t_continent'];
			} else {
				// It's inside a continent group

				// Continent optgroup
				if ( ! isset( $zonen[ $key - 1 ] ) || $zonen[ $key - 1 ]['continent'] !== $zone['continent'] ) {
					$label       = $zone['t_continent'];
					$structure[] = '<optgroup label="' . esc_attr( $label ) . '">';
				}

				// Add the city to the value
				$value[] = $zone['city'];

				$display = $zone['t_city'];
				if ( ! empty( $zone['subcity'] ) ) {
					// Add the subcity to the value
					$value[]  = $zone['subcity'];
					$display .= ' - ' . $zone['t_subcity'];
				}
			}

			// Build the value
			$value    = join( '/', $value );
			$selected = '';
			if ( $value === $selected_zone ) {
				$selected = 'selected="selected" ';
			}
			$structure[] = '<option ' . $selected . 'value="' . esc_attr( $value ) . '">' . esc_html( $display ) . '</option>';

			// Close continent optgroup
			if ( ! empty( $zone['city'] ) && ( ! isset( $zonen[ $key + 1 ] ) || ( isset( $zonen[ $key + 1 ] ) && $zonen[ $key + 1 ]['continent'] !== $zone['continent'] ) ) ) {
				$structure[] = '</optgroup>';
			}
		}

		// Do UTC
		$structure[] = '<optgroup label="' . esc_attr__( 'UTC', 'oxy-ultimate' ) . '">';
		$selected    = '';
		if ( 'UTC' === $selected_zone ) {
			$selected = 'selected="selected" ';
		}
		$structure[] = '<option ' . $selected . 'value="' . esc_attr__( 'UTC', 'oxy-ultimate' ) . '">' . __( 'UTC', 'oxy-ultimate' ) . '</option>';
		$structure[] = '</optgroup>';

		return join( "\n", $structure );
	}

	function oxyu_date() {
		$date['0'] = __('Select');
		for ($i=0; $i < 32; $i++) { 
			
			if( $i < 10 ) 
				$i = '0' . $i;

			$date["{$i}"] = $i;
		}

		return $date;
	}

	function oxyu_month() {
		$month = [
			'0' => __('Select'),
			'01' => __('Jan'),
			'02' => __('Feb'),
			'03' => __('Mar'),
			'04' => __('Apr'),
			'05' => __('May'),
			'06' => __('Jun'),
			'07' => __('Jul'),
			'08' => __('Aug'),
			'09' => __('Sep'),
			'10' => __('Oct'),
			'11' => __('Nov'),
			'12' => __('Dec')
		];

		return $month;
	}

	function oxyu_year() {
		$year[0] = __('Select');
		for ($i=2021; $i < 2031; $i++) { 
			$year[$i] = $i;
		}

		return $year;
	}

	function oxyu_hour() {
		$hour[0] = __('Select');
		for ($i=0; $i < 24; $i++) { 
			
			if( $i < 10 ) 
				$i = '0' . $i;

			$hour["{$i}"] = $i;
		}

		return $hour;
	}

	function oxyu_minSec( $prefix ) {
		$min_sec[0] = __('Select');
		for ($i=0; $i <= 59; $i++) {
			
			if( $i < 10 ) 
				$i = '0' . $i;

			$min_sec["{$i}"] = $i;
		}

		return $min_sec;
	}
	
	/*function init() {
		$this->El->useAJAXControls();
		if ( isset( $_GET['oxygen_iframe'] ) ) {
			add_action( 'wp_footer', array( $this, 'oucd_enqueue_scripts' ) );
		}
	}*/

	function oucd_enqueue_scripts() {
		wp_enqueue_script(
			'oucd-plugin-script', 
			OXYU_URL . 'assets/js/jquery.plugin.js',
			array(),
			filemtime( OXYU_DIR . 'assets/js/jquery.plugin.js' ),
			true
		);
		
		wp_enqueue_script(
			'oucd-countdown-script', 
			OXYU_URL . 'assets/js/jquery.countdown.js',
			array(),
			filemtime( OXYU_DIR . 'assets/js/jquery.countdown.js' ),
			true
		);
		
		wp_enqueue_script(
			'oucd-cookie-script', 
			OXYU_URL . 'assets/js/jquery.cookie.min.js',
			array(),
			filemtime( OXYU_DIR . 'assets/js/jquery.cookie.min.js' )
		);
		
		wp_enqueue_script(
			'oucd-script', 
			OXYU_URL . 'assets/js/ou-countdown.js',
			array(),
			filemtime( OXYU_DIR . 'assets/js/ou-countdown.js' ),
			true
		);
	}

	function enablePresets() {
		return true;
	}

	function enableFullPresets() {
		return true;
	}

	function getACFDate( $date, $options ) {
		if( strpos( $date, '-') && strpos( $date, ':') ) {
			$dtArr= explode(" ", $date);
			$dateArr = explode("-", $dtArr[0]);
			$options['oucd_year'] = $dateArr[0];
			$options['oucd_mo'] = $dateArr[1];
			$options['oucd_date'] = $dateArr[2];

			$timeArr = explode(":", $dtArr[1]);
			$options['oucd_hour'] = $timeArr[0];
			$options['oucd_minutes'] = $timeArr[1];

			if( isset( $timeArr[2] ) )
				$options['oucd_seconds'] = $timeArr[2];
		} elseif( strpos( $date, '-') ) {
			$dateArr = explode("-", $date);
			$options['oucd_year'] = $dateArr[0];
			$options['oucd_mo'] = $dateArr[1];
			$options['oucd_date'] = $dateArr[2];
			$options['oucd_hour'] = 23;
			$options['oucd_minutes'] = 59;
		} elseif( strpos( $date, ':') ) {
			$timeArr = explode(":", $date);
			$options['oucd_date'] = '0';

			$options['oucd_hour'] = $timeArr[0];
			$options['oucd_minutes'] = $timeArr[1];

			if( isset( $timeArr[2] ) )
				$options['oucd_seconds'] = $timeArr[2];

			$options['oucd_timertype'] = 'evergreen';
		} elseif( strlen( $date ) == 8 ) {
			$options['oucd_year'] = substr($date, 0, 4);
			$options['oucd_mo'] = substr($date, 4, 2);
			$options['oucd_date'] = substr($date, 6, 2);
			$options['oucd_hour'] = 23;
			$options['oucd_minutes'] = 59;
		}

		return $options;
	}

	function getCountdownScript( $options, $uid ) {

		if ( ! empty( $options['oucd_tz'] ) ) {

			$time_zone_kolkata = new \DateTimeZone( 'Asia/Kolkata' );
			$time_zone         = new \DateTimeZone( $options['oucd_tz'] );

			$time_kolkata = new \DateTime( 'now', $time_zone_kolkata );

			$timeoffset = $time_zone->getOffset( $time_kolkata );

			$timeZone = $timeoffset / 3600;
		} else {
			$timeZone = 'NULL';
		}

		if( isset( $options['oucd_source'] ) && ( $options['oucd_source'] == 'acf' || $options['oucd_source'] == 'mb' ) ) {
			$date = get_post_meta( get_the_ID(), $options['oucd_acftimer'], true );
			$options = $this->getACFDate( $date, $options );
		}

		if( isset( $options['oucd_source'] ) && $options['oucd_source'] == 'acfopt' && function_exists('get_field') ) {
			$date = get_option( 'options_' . $options['oucd_acfopt'] );
			$options = $this->getACFDate( $date, $options );
		}

		$ftimer_labels = array(
			'year'		=> array(
				'singular'	=> ( isset( $options['oucd_yearlblsing'] ) && '' != $options['oucd_yearlblsing'] ) ? $options['oucd_yearlblsing'] : 'Year',
				'plural'	=> ( isset( $options['oucd_yearlblplur'] ) && '' != $options['oucd_yearlblplur'] ) ? $options['oucd_yearlblplur'] : 'Years'
			),
			'month'		=> array(
				
				'singular'	=> ( isset( $options['oucd_molblsing'] ) && '' != $options['oucd_molblsing'] ) ? $options['oucd_molblsing'] : 'Month',
				'plural'	=> ( isset( $options['oucd_molblplur'] ) && '' != $options['oucd_molblplur'] ) ? $options['oucd_molblplur'] : 'Months'
			),
			'day'		=> array(
				'singular'	=> ( isset( $options['oucd_daylblsing'] ) && '' != $options['oucd_daylblsing'] ) ? $options['oucd_daylblsing'] : 'Day',
				'plural'	=> ( isset( $options['oucd_daylblplur'] ) && '' != $options['oucd_daylblplur'] ) ? $options['oucd_daylblplur'] : 'Days',
			),
			'hour'		=> array(
				'singular'	=> ( isset( $options['oucd_hrlblsing'] ) && '' != $options['oucd_hrlblsing'] ) ? $options['oucd_hrlblsing'] : 'Hour',
				'plural'	=> ( isset( $options['oucd_hrlblplur'] ) && '' != $options['oucd_hrlblplur'] ) ? $options['oucd_hrlblplur'] : 'Hours',		
			),
			'minute'	=> array(
				'singular'	=> ( isset( $options['oucd_minlblsing'] ) && '' != $options['oucd_minlblsing']) ? $options['oucd_minlblsing'] : 'Minute',
				'plural'	=> ( isset( $options['oucd_minlblplur'] ) && '' != $options['oucd_minlblplur'] ) ? $options['oucd_minlblplur'] : 'Minutes'
			),
			'second'	=> array(
				'singular'	=> ( isset( $options['oucd_scndlblsing'] ) && '' != $options['oucd_scndlblsing'] ) ? $options['oucd_scndlblsing'] : 'Second',
				'plural'	=> ( isset( $options['oucd_scndlblplur'] ) && '' != $options['oucd_scndlblplur'] ) ? $options['oucd_scndlblplur'] : 'Seconds'
			)
		);

		if ( 'below' == $options['label_position'] ) { ?>
			var default_layout_<?php echo $uid; ?> = '';
			<?php if ( 'evergreen' != $options['oucd_timertype'] ) { ?>
			default_layout_<?php echo $uid; ?> += '{y<}'+ '<?php echo $this->ou_normal_view( $options, '{ynn}', '{yl}' ); ?>' +
				'{y>}';
			<?php } ?>

			default_layout_<?php echo $uid; ?> += '{o<}'+ '<?php echo $this->ou_normal_view( $options, '{onn}', '{ol}' ); ?>' +
				'{o>}'+
				'{d<}'+ '<?php echo $this->ou_normal_view( $options, '{dnn}', '{dl}' ); ?>' +
				'{d>}'+
				'{h<}'+ '<?php echo $this->ou_normal_view( $options, '{hnn}', '{hl}' ); ?>' +
				'{h>}'+
				'{m<}'+ '<?php echo $this->ou_normal_view( $options, '{mnn}', '{ml}' ); ?>' +
				'{m>}'+
				'{s<}'+ '<?php echo $this->ou_normal_view( $options, '{snn}', '{sl}' ); ?>' +
				'{s>}';

	<?php } elseif ( 'above' == $options['label_position'] ) { ?>

		var default_layout_<?php echo $uid; ?> = '';
		<?php if ( 'evergreen' != $options['oucd_timertype'] ) { ?>
		default_layout_<?php echo $uid; ?> += '{y<}' + '<?php echo $this->oucd_inside_above_countdown( $options, '{ynn}', '{yl}', '{y>}' ); ?>';
		<?php } ?>
		default_layout_<?php echo $uid; ?> += '{o<}' + '<?php echo $this->oucd_inside_above_countdown( $options, '{onn}', '{ol}', '{o>}' ); ?>' +
			'{d<}' + '<?php echo $this->oucd_inside_above_countdown( $options, '{dnn}', '{dl}', '{d>}' ); ?>' +
			'{h<}' + '<?php echo $this->oucd_inside_above_countdown( $options, '{hnn}', '{hl}', '{h>}' ); ?>' +
			'{m<}' + '<?php echo $this->oucd_inside_above_countdown( $options, '{mnn}', '{ml}', '{m>}' ); ?>' +
			'{s<}' + '<?php echo $this->oucd_inside_above_countdown( $options, '{snn}', '{sl}', '{s>}' ); ?>';

	<?php } elseif ('right' == $options['label_position'] || 'left' == $options['label_position'] ) { ?>

		var default_layout_<?php echo $uid; ?> = '';
		<?php if ( 'evergreen' != $options['oucd_timertype'] ) { ?>
		default_layout_<?php echo $uid; ?> += '{y<}' + '<?php echo $this->oucd_outside_countdown( $options, '{ynn}', '{yl}', '{y>}' ); ?>';
		<?php } ?>
		
		default_layout_<?php echo $uid; ?> += '{o<}' + '<?php echo $this->oucd_outside_countdown( $options, '{onn}', '{ol}', '{o>}' ); ?>' +
			'{d<}' + '<?php echo $this->oucd_outside_countdown( $options, '{dnn}', '{dl}', '{d>}' ); ?>' +
			'{h<}' + '<?php echo $this->oucd_outside_countdown( $options, '{hnn}', '{hl}', '{h>}' ); ?>' +
			'{m<}' + '<?php echo $this->oucd_outside_countdown( $options, '{mnn}', '{ml}', '{m>}' ); ?>' +
			'{s<}' + '<?php echo $this->oucd_outside_countdown( $options, '{snn}', '{sl}', '{s>}' ); ?>';
	<?php } else { ?>
		var default_layout_<?php echo $uid; ?> =  '';
		<?php if ( 'evergreen' != $options['oucd_timertype'] ) { ?>
		default_layout_<?php echo $uid; ?> +=  '{y<}'+ '<?php echo $this->ou_normal_view( $options, '{ynn}', '{yl}' ); ?>' + '{y>}';
		<?php } ?>
		default_layout_<?php echo $uid; ?> += '{o<}'+ '<?php echo $this->ou_normal_view( $options, '{onn}', '{ol}' ); ?>' +
		'{o>}'+
		'{d<}'+ '<?php echo $this->ou_normal_view( $options, '{dnn}', '{dl}' ); ?>' +
		'{d>}'+
		'{h<}'+ '<?php echo $this->ou_normal_view( $options, '{hnn}', '{hl}' ); ?>' +
		'{h>}'+
		'{m<}'+ '<?php echo $this->ou_normal_view( $options, '{mnn}', '{ml}' ); ?>' +
		'{m>}'+
		'{s<}'+ '<?php echo $this->ou_normal_view( $options, '{snn}', '{sl}' ); ?>' +
		'{s>}';
	<?php } ?>

	jQuery(document).ready(function($) {
		if( parseInt(window.location.href.toLowerCase().indexOf("?ct_builder")) === parseInt(36) && 'evergreen' == '<?php echo $options['oucd_timertype'] ?>' ) {
			$.removeCookie( "countdown-<?php echo $uid; ?>");
			$.removeCookie( "countdown-<?php echo $uid; ?>-currdate");
			$.removeCookie( "countdown-<?php echo $uid; ?>-day");
			$.removeCookie( "countdown-<?php echo $uid; ?>-hour");
			$.removeCookie( "countdown-<?php echo $uid; ?>-min");
			$.removeCookie( "countdown-<?php echo $uid; ?>-sec");
		}
		new OUCountdown({
			id: '<?php echo $uid; ?>',
			fixed_timer_action: '<?php echo $options['oucd_expaction']; ?>',
			evergreen_timer_action: '<?php echo $options['evergreen_timer_action']; ?>',
			timertype: '<?php echo $options['oucd_timertype']; ?>',
			<?php if ( 'evergreen' == $options['oucd_timertype'] ) { ?>
			timer_date: new Date(),
			<?php } else { ?>
			timer_date: new Date( "<?php if ( isset( $options['oucd_year'] ) ) { echo $options['oucd_year']; } ?>", "<?php if ( isset( $options['oucd_mo'] ) ) { echo $options['oucd_mo'] - 1; } ?>", "<?php if ( isset( $options['oucd_date'] ) ) { echo $options['oucd_date']; } ?>", "<?php if ( isset( $options['oucd_hour'] ) ) { echo $options['oucd_hour']; } ?>", "<?php if ( isset( $options['oucd_minutes'] ) ) { echo $options['oucd_minutes']; } ?>", "<?php if ( isset( $options['oucd_seconds'] ) ) { echo $options['oucd_seconds']; }?>" ),
			<?php } ?>
			timer_format: '<?php if ( isset( $options['oucd_dispyear'] ) && $options['oucd_dispyear'] != 'no' ) { echo $options['oucd_dispyear']; } ?><?php if ( isset( $options['oucd_dispmo'] ) && $options['oucd_dispmo'] != 'no' ) { echo $options['oucd_dispmo']; } ?><?php if ( isset( $options['oucd_dispday'] ) && $options['oucd_dispday'] != 'no'  ) { echo $options['oucd_dispday']; } ?><?php if ( isset( $options['oucd_disphr'] ) && $options['oucd_disphr'] != 'no' ) { echo $options['oucd_disphr']; } ?><?php if ( isset( $options['oucd_dispmin'] ) && $options['oucd_dispmin'] != 'no' ) { echo $options['oucd_dispmin']; } ?><?php if ( isset( $options['oucd_dispscnd'] ) && $options['oucd_dispscnd'] != 'no' ) { echo $options['oucd_dispscnd']; } ?>',
			timer_layout: default_layout_<?php echo $uid; ?>,
			timer_labels: '<?php echo $ftimer_labels['year']['plural']; ?>,<?php echo $ftimer_labels['month']['plural']; ?>,,<?php echo $ftimer_labels['day']['plural']; ?>,<?php echo $ftimer_labels['hour']['plural']; ?>,<?php echo $ftimer_labels['minute']['plural']; ?>,<?php echo $ftimer_labels['second']['plural']; ?>',
			timer_labels_singular: 	'<?php echo $ftimer_labels['year']['singular']; ?>,<?php echo $ftimer_labels['month']['singular']; ?>,,<?php echo $ftimer_labels['day']['singular']; ?>,<?php echo $ftimer_labels['hour']['singular']; ?>,<?php echo $ftimer_labels['minute']['singular']; ?>,<?php echo $ftimer_labels['second']['singular']; ?>',
			redirect_link_target: '<?php echo isset( $options['redirect_link_target'] ) ? $options['redirect_link_target'] : ''; ?>',
			redirect_link: '<?php echo isset( $options['redirect_link'] ) ? $options['redirect_link'] : ''; ?>',
			expire_message: '<?php echo isset( $options['expire_message'] ) ? preg_replace( '/\s+/', ' ', $options['expire_message'] ) : ''; ?>',
			evergreen_date_days: '<?php echo isset( $options['oucd_date'] ) ? $options['oucd_date'] : ''; ?>',
			evergreen_date_hours: '<?php echo isset( $options['oucd_hour'] ) ? $options['oucd_hour'] : ''; ?>',
			evergreen_date_minutes: '<?php echo isset( $options['oucd_minutes'] ) ? $options['oucd_minutes'] : ''; ?>',
			evergreen_date_seconds: '<?php echo isset( $options['oucd_seconds'] ) ? $options['oucd_seconds'] : ''; ?>',
			time_zone: '<?php echo $timeZone; ?>',
			<?php if ( isset( $options['oucd_expaction'] ) && 'msg' == $options['oucd_expaction'] ) { ?>
			timer_exp_text: '<div class="ou-countdown-expire-message"><?php echo ( '' != $options['expire_message'] ) ? preg_replace( '/\s+/', ' ', $options['expire_message'] ) : ''; ?></div>'
			<?php } ?>
		});
	});
	<?php
	}

	function ou_normal_view( $options, $str1, $str2 ) {

		ob_start();

		?><div class="ou-countdown-item"><div class="ou-countdown-digit-wrapper "><<?php echo $options['oucd_dtag']; ?> class="ou-countdown-digit "><?php echo $str1; ?></<?php echo $options['oucd_dtag']; ?>></div><?php if( 'yes' == $options['oucd_displb'] ) { ?><div class="ou-countdown-label-wrapper"><<?php echo $options['oucd_lbtag']; ?> class="ou-countdown-label "><?php echo $str2; ?></<?php echo $options['oucd_lbtag']; ?>></div><?php } ?></div><?php

		$html = ob_get_contents();
		
		ob_end_clean();

		return $html;
	}

	function oucd_inside_above_countdown( $options, $str1, $str2, $str3 ) {

		ob_start();

		?><div class="ou-countdown-item"><?php if( 'yes' == $options['oucd_displb'] ) { ?><div class="ou-countdown-label-wrapper"><<?php echo $options['oucd_lbtag']; ?> class="ou-countdown-label"><?php echo $str2; ?></<?php echo $options['oucd_lbtag']; ?>></div><?php } ?><div class="ou-countdown-digit-wrapper"><<?php echo $options['oucd_dtag']; ?> class="ou-countdown-digit"><?php echo $str1; ?></<?php echo $options['oucd_dtag']; ?>></div><?php echo $str3; ?></div><?php

		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	function oucd_outside_countdown( $options, $str1, $str2, $str3 ) {

		ob_start();

		?><div class="ou-countdown-item label-position-<?php echo $options['label_position']; ?>"><?php if( 'yes' == $options['oucd_displb'] ) { ?><div class="ou-countdown-label-wrapper"><<?php echo $options['oucd_lbtag']; ?> class="ou-countdown-label "><?php echo $str2; ?></<?php echo $options['oucd_lbtag']; ?>></div><?php } ?><div class="ou-countdown-digit-wrapper "><<?php echo $options['oucd_dtag']; ?> class="ou-countdown-digit "><?php echo $str1; ?></<?php echo $options['oucd_dtag']; ?>></div><?php echo $str3; ?></div><?php

		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}
}

new OUCountdown();