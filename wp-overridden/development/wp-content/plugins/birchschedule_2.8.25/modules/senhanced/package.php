<?php

birch_ns( 'birchschedule.senhanced', function( $ns ) {

		global $birchschedule;

		birch_defn( $ns, 'init', function() use( $ns, $birchschedule ) {

				add_action( 'init', array( $ns, 'wp_init' ) );

				add_action( 'admin_init', array( $ns, 'wp_admin_init' ) );

				add_action( 'birchschedule_view_services_render_service_info_after',
					array( $ns, 'render_advanced_settings' ), 20 );
			} );

		birch_defn( $ns, 'wp_init', function() use( $ns, $birchschedule ) {

				add_filter( 'birchschedule_model_get_service_timeslot',
					array( $ns, 'get_service_timeslot' ), 20, 2 );
			} );

		birch_defn( $ns, 'wp_admin_init', function() use( $ns, $birchschedule ) {

				add_action( 'birchschedule_view_services_save_post_after',
					array( $ns, 'save_service_data' ) );
			} );

		birch_defn( $ns, 'get_timeslot_options', function() {

				$timeslot_options = array();

				for ( $i = 5; $i <= 60; $i += 5 ) {
					$timeslot_options[$i] = $i . __( ' minutes', 'birchschedule' );
				}

				return $timeslot_options;
			} );

		birch_defn( $ns, 'save_service_data', function( $post ) {
				if ( isset( $_POST['birs_service_enable_flexible_start'] ) ) {
					$enable_flexible = $_POST['birs_service_enable_flexible_start'];
				} else {
					$enable_flexible = false;
				}
				update_post_meta( $post['ID'], '_birs_service_enable_flexible_start',
					$enable_flexible );
				if ( isset( $_POST['birs_service_timeslot'] ) ) {
					$timeslot = $_POST['birs_service_timeslot'];
					update_post_meta( $post['ID'], '_birs_service_timeslot',
						$timeslot );
				}
			} );

		birch_defn( $ns, 'get_service_timeslot', function( $timeslot, $service_id ) use( $ns, $birchschedule ) {

				$service = $birchschedule->model->get( $service_id, array(
						'meta_keys' => array(
							'_birs_service_length', '_birs_service_length_type',
							'_birs_service_padding', '_birs_service_padding_type',
							'_birs_service_enable_flexible_start', '_birs_service_timeslot'
						),
						'base_keys' => array()
					) );
				if ( $service['_birs_service_enable_flexible_start'] ) {
					$timeslot = $service['_birs_service_timeslot'];
				} else {
					$timeslot = $birchschedule->model->get_service_length_with_paddings( $service_id );
				}
				if ( !$timeslot ) {
					$timeslot = 15;
				}
				return $timeslot;
			} );

		birch_defn( $ns, 'get_timeslot_html', function( $service ) use( $ns ) {
				global $birchpress;

				ob_start();
?>
        <select name="birs_service_timeslot" id="birs_service_timeslot">
        <?php
				$birchpress->util->render_html_options( $ns->get_timeslot_options(),
					$service['_birs_service_timeslot'] );
?>
        </select>
        <?php
				return ob_get_clean();
			} );

		birch_defn( $ns, 'render_advanced_settings', function( $post ) use( $ns, $birchschedule ) {

				$service_id = $post->ID;
				$service = $birchschedule->model->get( $service_id, array(
						'meta_keys' => array(
							'_birs_service_enable_flexible_start', '_birs_service_timeslot'
						),
						'base_keys' => array()
					) );
				$checked_html = " ";
				if ( $service['_birs_service_enable_flexible_start'] ) {
					$checked_html = "checked='checked'";
				}
?>
        <style type="text/css">
            .form-field input[type="checkbox"] {
                width: auto;
            }
            .form-field .birs_divider {
                border-bottom: 1px solid #DFDFDF;
                border-top: 1px solid white;
                padding: 0;
                margin: 0;
            }
        </style>
        <div class="panel-wrap birchschedule">
            <table class="form-table">
                <tr class="form-field">
                    <td colspan="2">
                        <input type="checkbox"
                            name="birs_service_enable_flexible_start"
                            value="on"
                            <?php echo $checked_html; ?>
                            id="birs_service_enable_flexible_start"/>
                        <label>
                            <?php printf( __( 'Enable flexible appointment start time with %s', 'birchschedule' ),
					$birchschedule->senhanced->get_timeslot_html( $service ) ); ?>
                        </label>
                    </td>
                </tr>
            </table>
        </div>
        <script type="text/javascript">
            jQuery(function($) {
                var flexible_checkbox = $('#birs_service_enable_flexible_start');
                $('#birs_service_timeslot').prop('disabled', !flexible_checkbox.is(':checked'));
                flexible_checkbox.change(function(){
                    $('#birs_service_timeslot').prop('disabled', !flexible_checkbox.is(':checked'));
                });
            });
        </script>
        <?php
			} );

	} );
