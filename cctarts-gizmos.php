<?php
/*
Plugin Name: CCTARTS Gizmos
Description: Enables custom functionality for the homepage.
Author: Russell Heimlich
Version: 0.1
 */
 function cctarts_homepage_boxes_gizmo_init() {

 	$front_page_id = get_option( 'page_on_front' );
 	if( is_admin() && isset( $_GET['post'] ) && isset( $_GET['action'] ) && $_GET['action'] == 'edit' ) {
 		if( $_GET['post'] != $front_page_id ) {
 			// Not editing the home page so we can bail...
 			return;
 		}
 	}

 	class CCTAHomepageBoxes extends WP_Gizmo {
 		// Which post_types should these Gizmos apply to?
 		function post_types() {
 				return array( 'page' );
 		}

 		public $add_new_label = 'Add New';

 		// Change properies for the metabox that is rendered.
 		function metabox() {
 			//Set the title, context, priority
 			return array(
 				'title' => 'Homepage Boxes',
 				'context' => 'normal'
 			);
 		}

 		// Define different types of Gizmo widgety thingies that appear in the Add New dropdown.
 		public $gizmo_types = array(
 			'box' => 'Box',
 		);

 		function render_box_fields($num, $data = NULL) {
 			?>

            <p>
				<label for="<?php echo $this->get_field_name('image');?>">Image</label>
				<input type="text" name="<?php echo $this->get_field_name('image');?>" id="<?php echo $this->get_field_name('image');?>" value="<?php esc_attr_e($data['image']);?>">

				<?php if( isset( $data['image'] ) && !empty( $data['image'] ) ) {
					$basename = basename( wp_get_attachment_url( $data['image'] ) );
					if( $basename ) {
						echo wp_get_attachment_image( $data['image'], 'medium' );

					}
				}
				?>
			</p>
			<p class="wp-media-buttons">
				<a class="button add_media gizmo-media" data-target-selector='input[name="<?php echo $this->get_field_name('image');?>"]' data-options='{"multiple": false }' data-return-property="id"><span class="wp-media-buttons-icon"></span> Select an Image</a>
			</p>

 			<p style="clear:left;">
 				<label for="<?php echo $this->get_field_name('title');?>">Title</label>
 				<input type="text" class="gizmo-title" name="<?php echo $this->get_field_name('title');?>" id="<?php echo $this->get_field_name('title');?>" value="<?php echo $data['title'];?>">
 			</p>

 			<p>
 				<label for="<?php echo $this->get_field_name('url');?>">URL</label>
 				<input type="url" name="<?php echo $this->get_field_name('url');?>" id="<?php echo $this->get_field_name('url');?>" value="<?php echo $data['url'];?>">
 			</p>

            <?php if( defined('DOING_AJAX') && DOING_AJAX ) { ?>
				<p>Save the page to make the text editor appear.</p>
				<input type="hidden" name="<?php echo $this->get_field_name('blurb');?>" value="<?php esc_attr_e( $data['blurb'] );?>">
			<?php
				return;
			}

			$settings = array(
				'textarea_name' => $this->get_field_name('blurb'),
				'teeny' => true,
                'media_buttons' => false,
			);
			$editor_id = preg_replace( '/[^a-z0-9]/', '', $this->get_field_name('blurb') );
			wp_editor( $data['blurb'], $editor_id, $settings );
 		}

        function validate_data( $data ) {
            $whitelist = array( 'image', 'title', 'url', 'blurb' );
            foreach( $whitelist as $val ) {
                if( !isset( $data[ $val ] ) ) {
                    $data[ $val ] = '';
                }
            }

            return $data;
        }

 		function render_box( $data, $count ) {
            $title = $data['title'];
            $url = $data['url'];
            $blurb = $data['blurb'];
            if( $title && $url ) {
                $title = '<a href="' . esc_url( $url ) . '">' . $title . '</a>';
            }

            $img = '';
            if( $img_id = intval( $data['image'] ) ) {
                $img_data = wp_get_attachment_image_src( $img_id );
                $img = '<img src="' . esc_url( $img_data[0] ) . '" alt="">';
            }
            if( $img && $url ) {
                $img = '<a href="' . esc_url( $url ) . '">' . $img . '</a>';
            }
 			?>
            <div class="homepage-box">
                <?php if( $img ) {
                    echo $img;
                } ?>
                <?php if( $title ): ?>
                    <h2 class="heading"><?php echo $title; ?></h2>
                <?php endif; ?>

                <?php if( $blurb ): ?>
                    <div class="blurb">
                        <?php echo apply_filters( 'the_content', $blurb ); ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php
 		}
 	}

 	register_gizmo( 'CCTAHomepageBoxes' );

 }
 add_action( 'gizmo_init', 'cctarts_homepage_boxes_gizmo_init' );
