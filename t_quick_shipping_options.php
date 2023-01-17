<?php
/*
Template Name: Quick Shipping Products
*/

	get_header();
?>
	<div class="dashboard-page">
		<div class="container-fluid">
			<div class="row lr-10">

				<?php get_template_part( 'template-parts/quick/order', 'quick-shipping-list' ); ?>
				<?php get_template_part( 'template-parts/quick/order', 'quick-shipping-inventory' ); ?>
			</div>
			<div class="row lr-10">
				<?php get_template_part( 'template-parts/quick/order', 'quick-shipping-options-completed' ); ?>
				<?php get_template_part( 'template-parts/quick/order', 'orders-with-ventilation' ); ?>
			</div>
		</div>
	</div>

		<?php
		get_footer();

