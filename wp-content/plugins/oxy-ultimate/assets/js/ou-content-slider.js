!function(n){(OUContentSlider=function(e){this.id=e.id,this.compClass="."+e.id,this.elements="",this.slidesPerView=e.slidesPerView,this.slidesPerColumn=e.slidesPerColumn,this.slidesToScroll=e.slidesToScroll,this.settings=e,this.swipers={},"undefined"==typeof Swiper?n(window).on("load",n.proxy(function(){"undefined"!=typeof Swiper&&this._init()},this)):this._init();var t=new CustomEvent("ouContentSlider",{detail:{swiper:this.swipers.main}});document.querySelector("."+e.id).dispatchEvent(t)}).prototype={id:"",compClass:"",elements:"",slidesPerView:{},slidesToScroll:{},settings:{},swipers:{},_init:function(){var e,t;this.elements={mainSwiper:this.compClass},this.elements.swiperSlide=n(this.elements.mainSwiper).find(".swiper-slide"),this._getSlidesCount()<=1||(e=this._getSwiperOptions(),this.swipers.main=new Swiper(this.elements.mainSwiper,e.main),n((t=this).compClass).on("mouseenter",function(e){t.settings.pause_on_hover&&t.swipers.main.autoplay.stop()}),n(this.compClass).on("mouseleave",function(e){t.settings.pause_on_hover&&t.swipers.main.autoplay.start()}))},_getEffect:function(){return this.settings.effect},_getSlidesCount:function(){return this.elements.swiperSlide.length},_getInitialSlide:function(){return this.settings.initialSlide},_getSpaceBetween:function(){var e=this.settings.spaceBetween.desktop,e=parseInt(e);return e=isNaN(e)?10:e},_getSpaceBetweenTablet:function(){var e=this.settings.spaceBetween.tablet,e=parseInt(e);return e=isNaN(e)?this._getSpaceBetween():e},_getSpaceBetweenLandscape:function(){var e=this.settings.spaceBetween.landscape,e=parseInt(e);return e=isNaN(e)?this._getSpaceBetweenTablet():e},_getSpaceBetweenPortrait:function(){var e=this.settings.spaceBetween.portrait,e=parseInt(e);return e=isNaN(e)?this._getSpaceBetweenLandscape():e},_getSlidesPerView:function(){var e=this.slidesPerView.desktop;return Math.min(this._getSlidesCount(),+e)},_getSlidesPerViewTablet:function(){var e=this.slidesPerView.tablet;return(e=""!==e&&0!==e?e:this.slidesPerView.desktop)||"coverflow"!==this.settings.type?Math.min(this._getSlidesCount(),+e):Math.min(this._getSlidesCount(),3)},_getSlidesPerViewLandscape:function(){var e=this.slidesPerView.landscape;return(e=""!==e&&0!==e?e:this._getSlidesPerViewTablet())||"coverflow"!==this.settings.type?Math.min(this._getSlidesCount(),+e):Math.min(this._getSlidesCount(),3)},_getSlidesPerViewPortrait:function(){var e=this.slidesPerView.portrait;return(e=""!==e&&0!==e?e:this._getSlidesPerViewLandscape())||"coverflow"!==this.settings.type?Math.min(this._getSlidesCount(),+e):Math.min(this._getSlidesCount(),3)},_getSlidesPerColumn:function(){return this.slidesPerColumn.desktop},_getSlidesPerColumnTablet:function(){var e=this.slidesPerColumn.tablet;return(e=""!==e&&0!==e?e:this.slidesPerColumn.desktop)||"coverflow"!==this.settings.type?e:Math.min(this._getSlidesCount(),1)},_getSlidesPerColumnLandscape:function(){var e=this.slidesPerColumn.landscape;return(e=""!==e&&0!==e?e:this._getSlidesPerColumnTablet())||"coverflow"!==this.settings.type?e:Math.min(this._getSlidesCount(),1)},_getSlidesPerColumnPortrait:function(){var e=this.slidesPerColumn.portrait;return(e=""!==e&&0!==e?e:this._getSlidesPerColumnLandscape())||"coverflow"!==this.settings.type?e:Math.min(this._getSlidesCount(),1)},_getSlidesToScroll:function(e){return"slide"===this._getEffect()?(e=this.slidesToScroll[e],Math.min(this._getSlidesCount(),+e||1)):1},_getSlidesToScrollDesktop:function(){return this._getSlidesToScroll("desktop")},_getSlidesToScrollTablet:function(){return this._getSlidesToScroll("tablet")},_getSlidesToScrollLandscape:function(){return this._getSlidesToScroll("landscape")},_getSlidesToScrollPortrait:function(){return this._getSlidesToScroll("portrait")},_getSwiperOptions:function(){var e=this.settings.breakpoint.desktop,t=this.settings.breakpoint.tablet,s=this.settings.breakpoint.landscape,i=(portrait_breakpoint=this.settings.breakpoint.portrait,compClass=this.compClass,{navigation:{prevEl:n(compClass).closest(".oxy-ou-content-slider").find(".ou-swiper-button-prev")[0],nextEl:n(compClass).closest(".oxy-ou-content-slider").find(".ou-swiper-button-next")[0]},pagination:{el:compClass+" .swiper-pagination",type:this.settings.pagination,dynamicBullets:this.settings.dynamicBullets,clickable:!0},grabCursor:!1,effect:this._getEffect(),initialSlide:this._getInitialSlide(),slidesPerView:this._getSlidesPerView(),slidesPerColumn:this._getSlidesPerColumn(),slidesPerColumnFill:"row",slidesPerGroup:this._getSlidesToScrollDesktop(),spaceBetween:this._getSpaceBetween(),centeredSlides:this.settings.centered,loop:this.settings.loop,speed:this.settings.speed,autoHeight:this.settings.autoHeight,breakpoints:{}});return this.settings.isBuilderActive&&"edit"==this.settings.builderPreview&&(i.simulateTouch=!1,i.shortSwipes=!1,i.longSwipes=!1,i.preventClicks=!1,i.preventClicksPropagation=!1,i.preventInteractionOnTransition=!0,i.touchStartPreventDefault=!1),this.settings.isBuilderActive||!1===this.settings.autoplay_speed||(i.autoplay={delay:this.settings.autoplay_speed,disableOnInteraction:!!this.settings.pause_on_interaction}),"cube"!==this._getEffect()&&"fade"!==this._getEffect()&&(i.breakpoints[e]={slidesPerView:this._getSlidesPerView(),slidesPerColumn:this._getSlidesPerColumn(),slidesPerGroup:this._getSlidesToScrollDesktop(),spaceBetween:this._getSpaceBetween()},i.breakpoints[t]={slidesPerView:this._getSlidesPerViewTablet(),slidesPerColumn:this._getSlidesPerColumnTablet(),slidesPerGroup:this._getSlidesToScrollTablet(),spaceBetween:this._getSpaceBetweenTablet()},i.breakpoints[s]={slidesPerView:this._getSlidesPerViewLandscape(),slidesPerColumn:this._getSlidesPerColumnLandscape(),slidesPerGroup:this._getSlidesToScrollLandscape(),spaceBetween:this._getSpaceBetweenLandscape()},i.breakpoints[portrait_breakpoint]={slidesPerView:this._getSlidesPerViewPortrait(),slidesPerColumn:this._getSlidesPerColumnPortrait(),slidesPerGroup:this._getSlidesToScrollPortrait(),spaceBetween:this._getSpaceBetweenPortrait()}),"cube"==this._getEffect()&&(i.cubeEffect={shadow:!0,slideShadows:!0,shadowOffset:20,shadowScale:.94}),"coverflow"===this.settings.type&&(i.effect="coverflow"),{main:i}},_onElementChange:function(e){0===e.indexOf("width")&&this.swipers.main.onResize(),0===e.indexOf("spaceBetween")&&this._updateSpaceBetween(this.swipers.main,e)},_updateSpaceBetween:function(e,t){var s,i=this._getSpaceBetween(),t=t.match("space_between_(.*)");t?(s={tablet:this.settings.breakpoint.tablet,landscape:this.settings.breakpoint.landscape,portrait:this.settings.breakpoint.portrait},e.params.breakpoints[s[t[1]]].spaceBetween=i):e.originalParams.spaceBetween=i,e.params.spaceBetween=i,e.onResize()}}}(jQuery);