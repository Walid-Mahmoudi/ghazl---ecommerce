<?php

$cartTotals = array(
	'oxy-ou_cart_totals' => array(
		json_decode(
			'{
				"name":"Default","slug": "default","options":{"original":{"oxy-ou_cart_totals_table-shop-table th_typography_font-size":"14","oxy-ou_cart_totals_table-shop-table td- table-shop-table td -woocommerce-Price-amount_typography_text-align":"right"}}
			}',
			true
		),

		json_decode(
			'{
				"name":"With Return to Shop Button","slug":"shop-button","options":{"original":{"oxy-ou_cart_totals_table-shop-table th_typography_font-size":"14","oxy-ou_cart_totals_table-shop-table td- table-shop-table td -woocommerce-Price-amount_typography_text-align":"right","oxy-ou_cart_totals_shop_button":"yes"}}
			}',
			true
		),

		json_decode(
			'{
				"name":"Without Heading","slug":"no-heading","options":{"original":{"oxy-ou_cart_totals_table-shop-table th_typography_font-size":"14","oxy-ou_cart_totals_table-shop-table td- table-shop-table td -woocommerce-Price-amount_typography_text-align":"right","oxy-ou_cart_totals_shop_button":"yes","oxy-ou_cart_totals_hide_heading":"Yes"}}
			}',
			true
		)
	)
);