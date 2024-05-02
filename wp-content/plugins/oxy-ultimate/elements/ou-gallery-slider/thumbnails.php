<?php if ( $options['ouacfg_sldtype'] == 'slideshow' && sizeof($images) > 1) : $thumb_size = isset( $options['thumb_size'] ) ? $options['thumb_size'] : 'thumbnail'; ?>
<div class="ou-thumbnails-swiper swiper-container ou-thumbs-ratio-<?php echo $options['thumb_ratio']; ?>">
	<div class="swiper-wrapper">
		<?php foreach( $images as $i => $imageID ) : ?>
			<?php
				if( is_array( $imageID ) )
					$thumb = wp_get_attachment_image_src( $imageID[0], $thumb_size );
				else
					$thumb = wp_get_attachment_image_src( $imageID, $thumb_size );
			?>
			<div class="swiper-slide">
				<div class="ouacfg-slider-thumb" style="background-image:url(<?php echo $thumb[0]; ?>)"></div>
			</div>
		<?php endforeach; ?>
	</div>
</div>
<?php endif; ?>