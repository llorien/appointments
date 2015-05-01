<?php

birch_ns( 'birchschedule.fbuilder.field.section', function( $ns ) {

        global $birchschedule;

        birch_defn( $ns, 'init', function() use( $ns, $birchschedule ) {
                add_filter( 'birchschedule_fbuilder_field_get_field_lookup_config', function( $config ) {
                        $config['lookup_table']['section_break'] = array( 'section_break', '_root' );
                        return $config;
                    } );
                add_filter( 'birchschedule_fbuilder_field_get_default_field_config', function( $config ) {
                        $config['section_break'] =array(
                            'category' => 'custom_fields',
                            'label' => __( 'Untitled', 'birchschedule' ),
                            'type' => 'section_break',
                            'belong_to' => 'none',
                            'visibility' => 'frontend',
                            'required' => false
                        );
                        return $config;
                    } );

                birch_defmethod( $birchschedule->fbuilder->field, 'render_options_editing',
                    'section_break', $ns->render_options_editing );

                birch_defmethod( $birchschedule->fbuilder->field, 'render_field_view',
                    'section_break', $ns->render_field_view );

            } );


        birch_defn( $ns, 'render_options_editing',
            function( $field ) use( $birchschedule ) {

                $birchschedule->fbuilder->field->render_option_label( $field );
            } );

        birch_defn( $ns, 'render_field_view',
            function( $field, $value = false ) {

?>
                <h2 class="birs_section"><?php echo $field['label'] ?></h2>
<?php
            } );

    } );
