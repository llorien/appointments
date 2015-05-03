<?php

birch_ns( 'birchschedule.fbuilder.field.country', function( $ns ) {

        global $birchschedule;

        birch_defn( $ns, 'init', function() use( $ns, $birchschedule ) {

                add_filter( 'birchschedule_fbuilder_field_get_field_lookup_config', function( $config ) {
                        $config['lookup_table']['country'] = array( 'country', 'drop_down', 'selectable', '_root' );
                        return $config;
                    } );

                birch_defmethod( $birchschedule->fbuilder->field, 'render_field_elements',
                    'country', $ns->render_field_elements );

                birch_defmethod( $birchschedule->fbuilder->field, 'render_options_editing',
                    'country', $ns->render_options_editing );

                birch_defmethod( $birchschedule->fbuilder->field, 'render_field_editing',
                    'country', $ns->render_field_editing );

            } );

        birch_defn( $ns, 'render_field_elements', function( $field, $value=false ) use( $ns, $birchschedule ) {

                global $birchpress;

                $countries = $birchpress->util->get_countries();
?>
        <select name="<?php echo $birchschedule->fbuilder->field->get_dom_name( $field ); ?>" id="<?php echo $birchschedule->fbuilder->field->get_dom_name( $field ); ?>">
            <?php
                $birchpress->util->render_html_options( $countries,
                    $value, $birchschedule->fbuilder->field->get_field_default_value( $field ) );
?>
        </select>
        <?php
            } );

        birch_defn( $ns, 'render_options_editing', function( $field ) use( $ns, $birchschedule ) {

                global $birchpress;

                $birchschedule->fbuilder->field->render_options_editing->fns['_root']( $field );
                $countries = $birchpress->util->get_countries();
                $fields_options = $birchschedule->fbuilder->get_fields_options();
                $default_country = $fields_options['client_country']['default_value'];
?>
        <li>
            <label><?php _e( 'Default Country', 'birchschedule' ); ?></label>
            <div id="birchschedule_fields_options_client_country_container">
                <select id="birchschedule_fields_options_client_country_default_value" name="birchschedule_fields_options[client_country][default_value]">
                    <?php $birchpress->util->render_html_options( $countries, $default_country ); ?>
                </select>
                <input type="hidden" id="birchschedule_fields_options_client_state_default_value" name="birchschedule_fields_options[client_state][default_value]" value="" disabled />
            </div>
        </li>
        <script type="text/javascript">
            jQuery(function($){
                $('#birchschedule_fields_options_client_country_default_value').change(function(){
                    $('#birchschedule_fields_options_client_state_default_value').prop('disabled', false);
                });
            });
        </script>
        <?php
            } );

        birch_defn( $ns, 'render_field_editing', function( $field ) use( $birchschedule ) {

                $birchschedule->fbuilder->field->render_field_editing->fns['_root']( $field );
            } );

    } );
