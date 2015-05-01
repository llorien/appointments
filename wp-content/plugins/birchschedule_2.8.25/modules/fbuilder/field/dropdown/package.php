<?php

birch_ns( 'birchschedule.fbuilder.field.dropdown', function( $ns ) {

        global $birchschedule;

        birch_defn( $ns, 'init', function() use( $ns, $birchschedule ) {

                add_filter( 'birchschedule_fbuilder_field_get_field_lookup_config', function( $config ) {
                        $config['lookup_table']['drop_down'] = array( 'drop_down', 'selectable', '_root' );
                        return $config;
                    } );

                add_filter( 'birchschedule_fbuilder_field_get_default_field_config', function( $config ) {
                        $config['drop_down'] = array(
                            'category' => 'custom_fields',
                            'label' => __( 'Untitled', 'birchschedule' ),
                            'type' => 'drop_down',
                            'visibility' => 'both',
                            'required' => false,
                            'choices' => array(
                                'First Choice' => __( 'First Choice', 'birchschedule' ),
                                'Second Choice' => __( 'Second Choice', 'birchschedule' ),
                                'Third Choice' => __( 'Third Choice', 'birchschedule' )
                            )
                        );
                        return $config;
                    } );

                birch_defmethod( $birchschedule->fbuilder->field, 'render_field_elements',
                    'drop_down', $ns->render_field_elements );

            } );

        birch_defn( $ns, 'render_field_elements', function( $field, $value=false ) use( $ns, $birchschedule ) {

                global $birchpress;

?>
                <select name="<?php echo $birchschedule->fbuilder->field->get_dom_name( $field ); ?>" id="<?php echo $birchschedule->fbuilder->field->get_dom_name( $field ); ?>">
<?php
                $birchpress->util->render_html_options( $field['choices'],
                    $value, $birchschedule->fbuilder->field->get_field_default_value( $field ) );
?>
                </select>
<?php
            } );

    } );
