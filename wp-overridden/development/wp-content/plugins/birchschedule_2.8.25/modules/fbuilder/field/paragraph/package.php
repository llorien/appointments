<?php

birch_ns( 'birchschedule.fbuilder.field.paragraph', function( $ns ) {

        global $birchschedule;

        birch_defn( $ns, 'init', function() use( $ns, $birchschedule ) {

                add_filter( 'birchschedule_fbuilder_field_get_field_lookup_config', function( $config ) {
                        $config['lookup_table']['paragraph_text'] = array( 'paragraph_text', '_root' );
                        return $config;
                    } );

                add_filter( 'birchschedule_fbuilder_field_get_default_field_config', function( $config ) {
                        $config['paragraph_text'] = array(
                            'category' => 'custom_fields',
                            'label' => __( 'Untitled', 'birchschedule' ),
                            'type' => 'paragraph_text',
                            'visibility' => 'both',
                            'required' => false
                        );
                        return $config;
                    } );

                birch_defmethod( $birchschedule->fbuilder->field, 'render_field_elements',
                    'paragraph_text', $ns->render_field_elements );

                birch_defmethod( $birchschedule->fbuilder->field, 'get_field_content_class',
                    'paragraph_text', $ns->get_field_content_class );

            } );

        birch_defn( $ns, 'render_field_elements', function( $field, $value=false ) use ( $birchschedule ) {

?>
                <textarea name="<?php echo $birchschedule->fbuilder->field->get_dom_name( $field ); ?>" id="<?php echo $birchschedule->fbuilder->field->get_dom_name( $field ); ?>"><?php echo $value; ?></textarea>
<?php
            } );

        birch_defn( $ns, 'get_field_content_class', function() {

                return 'birs_field_content birs_field_paragraph';
            } );

    } );
