<?php
/*
Template Name: Wholesale Orders
*/
get_header();
?>
	<div class="dashboard-page">
		<div class="container-fluid">
			<div class="row lr-10">
				<?php get_template_part( 'template-parts/wholesale/order', 'active' ); ?>
				<?php get_template_part( 'template-parts/wholesale/order', 'completed' ); ?>
			</div>
			<div class="row lr-10">
				<?php get_template_part( 'template-parts/wholesale/order', 'pending' ); ?>
				<?php get_template_part( 'template-parts/wholesale/order', 'color-match' ); ?>
			</div>
		</div>
	</div>

<?php
get_footer();
