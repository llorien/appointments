<?php

birch_ns( 'birchschedule.fbuilder.field.clientemail', function( $ns ) {

        global $birchschedule;

        birch_defn( $ns, 'init', function() use( $ns, $birchschedule ) {
                add_filter( 'birchschedule_fbuilder_field_get_field_lookup_config', function( $config ) {
                        $config['lookup_table']['client_email'] = array( 'client_email', 'email', 'single_line_text', '_root' );
                        return $config;
                    } );

                birch_defmethod( $birchschedule->fbuilder->field, 'get_attr_data_shown_client_type',
                    'client_email', $ns->get_attr_data_shown_client_type );

                birch_defmethod( $birchschedule->fbuilder->field, 'render_options_editing',
                    'client_email', $ns->render_options_editing );

                birch_defmethod( $birchschedule->fbuilder->field, 'validate',
                    'client_email', $ns->validate );

            } );

        birch_defn( $ns, 'get_attr_data_shown_client_type', function( $field ) {

                return "data-shown-client-type='new returning'";
            } );

        birch_defn( $ns, 'render_options_editing', function( $field ) use( $birchschedule ) {

                $birchschedule->fbuilder->field->render_option_label( $field );
            } );

        birch_defn( $ns, 'validate', function( $field ) use( $ns, $birchschedule ) {

                $request_name = $birchschedule->fbuilder->field->get_dom_name( $field );
                $client_type = $birchschedule->fbuilder->get_client_type();
                $error = array();
                $login_disabled = $birchschedule->fbuilder->is_login_disabled();
                if ( !isset( $_POST[$request_name] ) || !$_POST[$request_name] ) {
                    $error[$request_name] = __( 'Email is required', 'birchschedule' );
                } else {
                    $value = $_POST[$birchschedule->fbuilder->field->get_dom_name( $field )];
                    if ( !is_email( $value ) ) {
                        $error[$birchschedule->fbuilder->field->get_dom_name( $field )] = __( 'Please input a valid email address', 'birchschedule' );
                    }
                }
                if ( $client_type === 'new' && !$login_disabled ) {
                    if ( !$error ) {
                        $user = get_user_by( 'email', $_POST['birs_client_email'] );
                        $client_exists = $birchschedule->model->get_client_by_email( $_POST['birs_client_email'],
                            array(
                                'base_keys' => array(),
                                'meta_keys' => array()
                            )
                        );
                        if ( $user || $client_exists ) {
                            $error[$request_name] = __( 'Email already exists. You may book using this email by selecting "I am a returning user."', 'birchschedule' );
                            if ( $user && !$client_exists ) {
                                $birchschedule->uintegration->sync_user_to_client( $user );
                            }
                            if ( !$user && $client_exists ) {
                                $birchschedule->uintegration->sync_client_to_user( $client );
                            }
                        }
                    }
                }
                if ( $client_type === 'returning' && !$login_disabled ) {
                    if ( !$error ) {
                        $client_exists = $birchschedule->model->get_client_by_email( $_POST['birs_client_email'],
                            array(
                                'base_keys' => array(),
                                'meta_keys' => array()
                            )
                        );
                        $user = get_user_by( 'email', $_POST['birs_client_email'] );
                        if ( !$user && !$client_exists ) {
                            $register_disabed = $birchschedule->fbuilder->is_register_disabled();
                            if ( $register_disabed ) {
                                $error['birs_client_password'] =
                                __( 'Email does not exist. Please contact the administrator to create a user account.', 'birchschedule' );
                            } else {
                                $error['birs_client_password'] =
                                __( 'Email does not exist. You may book using this email by selecting "I am a new user."', 'birchschedule' );
                            }
                        } else {
                            if ( !$birchschedule->model->check_password( $_POST['birs_client_email'],
                                    $_POST['birs_client_password'] ) ) {
                                $error['birs_client_password'] = __( 'Email or password is incorrect.', 'birchschedule' );
                            }
                            if ( $user && !$client_exists ) {
                                $birchschedule->uintegration->sync_user_to_client( $user );
                            }
                            if ( !$user && $client_exists ) {
                                $birchschedule->uintegration->sync_client_to_user( $client );
                            }
                        }
                    }
                }

                return $error;
            } );

    } );
