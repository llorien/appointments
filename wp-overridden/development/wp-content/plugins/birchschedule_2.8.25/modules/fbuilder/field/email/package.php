<?php

birch_ns( 'birchschedule.fbuilder.field.email', function( $ns ) {

        global $birchschedule;

        birch_defn( $ns, 'init', function() use( $ns, $birchschedule ) {
                add_filter( 'birchschedule_fbuilder_field_get_field_lookup_config', function( $config ) {
                        $config['lookup_table']['email'] = array( 'email', 'single_line_text', '_root' );
                        return $config;
                    } );
                add_filter( 'birchschedule_fbuilder_field_get_default_field_config', function( $config ) {
                        $config['email'] = array(
                            'category' => 'custom_fields',
                            'label' => __( 'Untitled', 'birchschedule' ),
                            'type' => 'email',
                            'visibility' => 'both',
                            'required' => false
                        );
                        return $config;
                    } );

                birch_defmethod( $birchschedule->fbuilder->field, 'validate',
                    'email', $ns->validate );
            } );

        birch_defn( $ns, 'validate', function( $field ) use( $birchschedule ) {

                $error = $birchschedule->fbuilder->field->validate->fns['_root']( $field );
                if ( !$error ) {
                    $value = $_REQUEST[$birchschedule->fbuilder->field->get_dom_name( $field )];
                    if ( !is_email( $value ) ) {
                        $error[$birchschedule->fbuilder->field->get_dom_name( $field )] = __( 'Please input a valid email address', 'birchschedule' );
                    }
                }
                return $error;
            } );

    } );
