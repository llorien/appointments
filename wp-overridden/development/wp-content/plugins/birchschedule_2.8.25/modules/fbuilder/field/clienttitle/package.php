<?php

birch_ns( 'birchschedule.fbuilder.field.clienttitle', function( $ns ) {

		global $birchschedule;

		birch_defn( $ns, 'init', function() {
				add_filter( 'birchschedule_fbuilder_field_get_field_lookup_config', function( $config ) {
						$config['lookup_table']['client_title'] = array( 'client_title', 'drop_down', 'selectable', '_root' );
						return $config;
					} );
			} );

	} );
