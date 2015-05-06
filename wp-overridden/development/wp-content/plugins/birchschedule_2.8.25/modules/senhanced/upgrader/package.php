<?php

birch_ns( 'birchschedule.senhanced.upgrader', function( $ns ) {

        global $birchschedule;

        birch_defn( $ns, 'init', function() use ( $ns, $birchschedule ) {

                birch_defmethod( $birchschedule, 'upgrade_module', 'senhanced', $ns->upgrade_module );

            } );

        birch_defn( $ns, 'upgrade_from_1_0_to_1_1', function() use( $ns, $birchschedule ) {

                $version = $ns->get_service_advanced_settings_version();
                if ( $version != '1.0' ) {
                    return;
                }
                $config = array(
                    'meta_keys' => array(
                        '_birs_service_enable_flexible_start',
                        '_birs_service_timeslot'
                    ),
                    'base_keys' => array()
                );

                $services = $birchschedule->model->query(
                    array(
                        'post_type' => 'birs_service'
                    ), $config
                );
                foreach ( $services as $service ) {
                    $service['_birs_service_enable_flexible_start'] = 'on';
                    $service['_birs_service_timeslot'] = 15;
                    $birchschedule->model->save( $service, $config );
                }
                update_option( 'birs_service_advanced_settings_version', '1.1' );
            } );

        birch_defn( $ns, 'get_service_advanced_settings_version', function() {
                return get_option( 'birs_service_advanced_settings_version', '1.0' );
            } );

        birch_defn( $ns, 'upgrade_module', function() use( $ns ) {
                $ns->upgrade_from_1_0_to_1_1();
            } );

    } );
