<?php
/* require __DIR__ . '/../vendor/autoload.php';

use Automattic\WooCommerce\Client;

$woocommerce = new Client(
  'https://staging-hoodsly.kinsta.cloud/',
  'ck_12f5137eaaff4a056a7fce7a24819a1ca5d85e9a',
  'cs_968bd4d7f2b01bb9f7795d50fce212cfe333828c',
  [
    'version' => 'wc/v3',
  ]
); */
?>
<div class="col-xl-4">
	<div class="dashboard-page__order">
		<div class="dashboard-page__order-header">
			<div class="navbar-header">
				<h6 class="title"><?php esc_html_e( 'Quick Shipping Inventory', 'hoodslyhub' ); ?></h6>
			</div>

			<div class="navbar-collapse">
				<ul class="navbar-nav ml-auto">
					<li><a href="#" class="icon-setting-sliders"></a></li>
				</ul>
			</div>
		</div>
		<div class="dashboard-page__order-body">

			<table class="table has--custom-color">
				<thead>
				<tr>
					<th scope="col"><?php esc_html_e( 'Stock', 'hoodslyhub' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Product', 'hoodslyhub' ); ?></th>
					<th scope="col"><?php esc_html_e( 'Size', 'hoodslyhub' ); ?></th>
				</tr>
				</thead>
				<tbody id="stock_inventory" >
						<tr class="hide_after" style="background-color: #ffffff1a;">
							<td>
								<div class="loadingio-spinner-spinner-jcq5xlo2ig">
									<div class="ldio-wdqegidkzlf">
										<div></div>
										<div></div>
										<div></div>
										<div></div>
										<div></div>
										<div></div>
										<div></div>
										<div></div>
										<div></div>
										<div></div>
										<div></div>
										<div></div>
									</div>
								</div>
							</td>
						</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
