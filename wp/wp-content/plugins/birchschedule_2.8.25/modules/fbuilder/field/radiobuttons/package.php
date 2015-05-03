<?php

birch_ns( 'birchschedule.fbuilder.field.radiobuttons', function( $ns ) {

        global $birchschedule;

        birch_defn( $ns, 'init', function() use( $ns, $birchschedule ) {
                add_filter( 'birchschedule_fbuilder_field_get_field_lookup_config', function( $config ) {
                        $config['lookup_table']['radio_buttons'] = array( 'radio_buttons', 'selectable', '_root' );
                        return $config;
                    } );
                add_filter( 'birchschedule_fbuilder_field_get_default_field_config', function( $config ) {
                        $config['radio_buttons'] =array(
                            'category' => 'custom_fields',
                            'label' => __( 'Untitled', 'birchschedule' ),
                            'type' => 'radio_buttons',
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

                birch_defmethod( $birchschedule->fbuilder->field, 'render_field_label',
                    'radio_buttons', $ns->render_field_label );

                birch_defmethod( $birchschedule->fbuilder->field, 'get_field_default_value',
                    'radio_buttons', $ns->get_field_default_value );

                birch_defmethod( $birchschedule->fbuilder->field, 'render_field_elements',
                    'radio_buttons', $ns->render_field_elements );

            } );

        birch_defn( $ns, 'render_field_label',
            function( $field ) {

?>
                <label><?php echo $field['label'] ?></label>
<?php
            } );

        birch_defn( $ns, 'get_field_default_value',
            function( $field ) {

                $default_value = "";
                if ( isset( $field['default_value'] ) ) {
                    $default_value = $field['default_value'];
                }
                return $default_value;
            } );

        birch_defn( $ns, 'render_field_elements',
            function( $field, $value = false ) use ( $birchschedule ) {
?>
        <ul class="birchschedule-radio-buttons">
            <?php
                $index = 0;
                foreach ( $field['choices'] as $choice_value => $choice_text ):
                if ( $value === false ) {
                    $value = $birchschedule->fbuilder->field->get_field_default_value( $field );
                }
                if ( $value == $choice_value ) {
                    $checked = " checked='checked' ";
                } else {
                    $checked = '';
                }
                $id = $birchschedule->fbuilder->field->get_dom_name( $field ) . '_' . $index++;
                $choice_value = esc_attr( $choice_value );
?>
                <li>
                    <input type="radio" name="<?php echo $birchschedule->fbuilder->field->get_dom_name( $field ) ?>" id="<?php echo $id; ?>" value="<?php echo $choice_value; ?>" <?php echo $checked; ?> />
                    <label for="<?php echo $id; ?>"><?php echo $choice_text; ?></label>
                </li>
            <?php endforeach; ?>
        </ul>
<?php
            } );

    } );
