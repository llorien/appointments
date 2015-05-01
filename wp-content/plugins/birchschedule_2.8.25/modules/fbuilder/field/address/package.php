<?php

birch_ns( 'birchschedule.fbuilder.field.address', function( $ns ) {

        global $birchschedule;

        birch_defn( $ns, 'init', function() use ( $ns, $birchschedule ) {
                add_filter( 'birchschedule_fbuilder_field_get_field_lookup_config', function( $config ) {
                        $config['lookup_table']['address'] = array( 'address', '_root' );

                        return $config;
                    } );

                birch_defmethod( $birchschedule->fbuilder->field, 'render_field_label',
                    'address', $ns->render_field_label );

                birch_defmethod( $birchschedule->fbuilder->field, 'render_field_elements',
                    'address', $ns->render_field_elements );

                birch_defmethod( $birchschedule->fbuilder->field, 'render_field_hidden',
                    'address', $ns->render_field_hidden );

                birch_defmethod( $birchschedule->fbuilder->field, 'validate',
                    'address', $ns->validate );

                birch_defmethod( $birchschedule->fbuilder->field, 'get_meta_field_name',
                    'address', $ns->get_meta_field_name );

                birch_defmethod( $birchschedule->fbuilder->field, 'get_field_merge_tag',
                    'address', $ns->get_field_merge_tag );

            } );

        birch_defn( $ns, 'render_field_label', function( $field ) use( $ns, $birchschedule ) {
?>
                <label for="<?php echo $birchschedule->fbuilder->field->get_dom_name( $field ) . '1'; ?>"><?php echo $field['label'] ?></label>
<?php
            } );

        birch_defn( $ns, 'render_field_elements', function( $field, $value = false ) use ( $ns, $birchschedule ) {

                if ( $value === false ) {
                    $value = array(
                        '_birs_client_address1' => '',
                        '_birs_client_address2' => ''
                    );
                }
                $address1_value = esc_attr( $value['_birs_client_address1'] );
                $address2_value = esc_attr( $value['_birs_client_address2'] );
?>
                <input type='text' name="<?php echo $birchschedule->fbuilder->field->get_dom_name( $field ) . '1'; ?>" id="<?php echo $birchschedule->fbuilder->field->get_dom_name( $field ) . '1' ?>" style="display: block;" value="<?php echo $address1_value; ?>"/>
                <input type="text" name="<?php echo $birchschedule->fbuilder->field->get_dom_name( $field ) . '2'; ?>" id="<?php echo $birchschedule->fbuilder->field->get_dom_name( $field ) . '2'; ?>" value="<?php echo $address2_value; ?>" />
<?php
            } );

        birch_defn( $ns, 'render_field_hidden', function( $field ) {
?>
                <input type="hidden" name="birs_client_fields[]" value="_birs_client_address1" />
                <input type="hidden" name="birs_client_fields[]" value="_birs_client_address2" />
<?php
            } );

        birch_defn( $ns, 'validate', function( $field ) use( $ns, $birchschedule ) {
                $error = array();
                if ( $field['required'] ) {
                    $address1 = $birchschedule->fbuilder->field->get_dom_name( $field ) . '1';
                    $address2 = $birchschedule->fbuilder->field->get_dom_name( $field ) . '2';
                    if ( ( !isset( $_REQUEST[$address1] ) || !$_REQUEST[$address1] ) && ( !isset( $_REQUEST[$address2] ) || !$_REQUEST[$address2] ) ) {
                        $error[$birchschedule->fbuilder->field->get_dom_name( $field )] = __( 'This field is required', 'birchschedule' );
                    }
                }
                return $error;
            } );

        birch_defn( $ns, 'get_meta_field_name', function( $field ) {
                return array( '_birs_client_address1', '_birs_client_address2' );
            } );

        birch_defn( $ns, 'get_field_merge_tag', function( $field ) {
                return '{client_address1}, {client_address2}';
            } );

    } );
