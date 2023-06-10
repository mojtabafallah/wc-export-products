<?php

namespace Mojtaba\WcExportProducts;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use WP_Query;

class Admin {
	public function __construct() {
		add_action( 'admin_menu', [ $this, 'init' ] );
	}

	public function init() {
		add_menu_page( 'خروجی گرفتن از محصولات',
			'خروجی گرفتن از محصولات',
			'manage_options',
			'wc-export-product',
			function () {
				$filename = date( 'Y-m-d-h-i-s', time() );
				if ( isset( $_POST['btn_export'] ) ) {

					if ( ! isset( $_POST['field_nonce_export_product'], $_POST['stock'] ) ) {
						wp_die( 'Error Validation' );
					}

					if ( ! wp_verify_nonce( $_POST['field_nonce_export_product'], 'wc-export-product' )
					     || !check_admin_referer( 'wc-export-product', 'field_nonce_export_product' ) ) {
						wp_die( 'Error Nonce' );
					}

					// Creates New Spreadsheet
					$spreadsheet = new Spreadsheet();
					$sheet       = $spreadsheet->getActiveSheet();

					$args = array(
						'post_type'      => 'product',
						'posts_per_page' => - 1
					);

					$loop = new WP_Query( $args );


					$sheet->setCellValue( 'A1', 'title' );
					$sheet->setCellValue( 'B1', 'sku' );
					$sheet->setCellValue( 'C1', 'stock' );
					$sheet->setCellValue( 'D1', 'image' );
					$sheet->setCellValue( 'E1', 'excerpt' );
					$sheet->setCellValue( 'F1', 'content' );

					$c = 2;
					while ( $loop->have_posts() ) : $loop->the_post();

						global $product;

						$title     = $product->get_title();

						$stock_qty = $product->get_stock_quantity();

						$sku       = $product->get_sku();

						$thumbnail = $product->get_image_id();

						$excerpt   = get_the_excerpt( $product->get_id() );
						if ($excerpt) $excerpt = true; else $excerpt =false;

						$content   = get_the_content( $product->get_id() );
					if ($content) $content = true; else $content =false;

						$sheet->setCellValue( 'A' . $c, $title );
						$sheet->setCellValue( 'B' . $c, $sku );
						$sheet->setCellValue( 'C' . $c, $stock_qty );
						$sheet->setCellValue( 'D' . $c, (bool) $thumbnail );
						$sheet->setCellValue( 'E' . $c,  $excerpt );
						$sheet->setCellValue( 'F' . $c,  $content );

                        $c++;

					endwhile;

					wp_reset_query();

					$writer = new Xlsx( $spreadsheet );

					$writer->save( PATH_PLUGIN . 'export/' . $filename . '.xlsx' );
				}

				?>
                <form action="" method="post">
					<?php echo wp_nonce_field( 'wc-export-product', 'field_nonce_export_product' ) ?>
                    <label for="stock">محصولات موجود</label>
                    <input type="radio" id="stock" name="stock" checked
                           value="in_stock">
                    <label for="stock">محصولات ناموجود</label>
                    <input type="radio" id="stock" name="stock"
                           value="out_stock">
                    <label for="stock">همه</label>
                    <input type="radio" id="stock" name="stock" value="all">

                    <input type="submit" class="button button-primary" name="btn_export" value="تایید">
                </form>
				<?php
			}
		);
	}

}