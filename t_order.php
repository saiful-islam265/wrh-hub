<?php
/*
Template Name: Orders
*/
get_header();
?>
	<div class="dashboard-page">
		<div class="container-fluid">
			<div class="row lr-10">
				<?php get_template_part( 'template-parts/order/order', 'all' ); ?>
				<?php get_template_part( 'template-parts/order/order', 'completed' ); ?>
			</div>
			<div class="row lr-10">
				<?php get_template_part( 'template-parts/order/order', 'pending' ); ?>
				<?php get_template_part( 'template-parts/order/order', 'color-match' ); ?>
			</div>
		</div>
	</div>

<?php
get_footer();
