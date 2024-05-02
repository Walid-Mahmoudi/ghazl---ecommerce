<?php
if ( $order ) :

	do_action( 'woocommerce_before_thankyou', $order->get_id() );
	?>

	<?php if ( $order->has_status( 'failed' ) ) : ?>

		<div class="woocommerce-order">
			<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed"><?php esc_html_e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce' ); ?></p>

			<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions">
				<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button pay"><?php esc_html_e( 'Pay', 'woocommerce' ); ?></a>
				<?php if ( is_user_logged_in() ) : ?>
					<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="button pay"><?php esc_html_e( 'My account', 'woocommerce' ); ?></a>
				<?php endif; ?>
			</p>
		</div>

	<?php else : ?>

		<?php
			$template = ouwoo_oxygen_template_exist( 'ouwoo_template_thankyou' );

			if( $template ) {
				global $oxygen_vsb_css_files_to_load;
				
				if (!is_array($oxygen_vsb_css_files_to_load)){ 	
					$oxygen_vsb_css_files_to_load = array();
				}

				$oxygen_vsb_css_files_to_load[] = $template[0]->ID;

				
				$thank_you_page_content = get_post_meta( $template[0]->ID, "ct_builder_shortcodes", true );

				echo ct_do_shortcode( $thank_you_page_content );
			}
		?>

	<?php endif; ?>

<?php else : ?>

	<div class="woocommerce-order">
		<p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received"><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', esc_html__( 'Thank you. Your order has been received.', 'woocommerce' ), null ); ?></p>
	</div>

<?php endif; ?>