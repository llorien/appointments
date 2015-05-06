<?php

birch_ns( 'birchschedule.fbuilder.field.selectable', function( $ns ) {

        global $birchschedule;

        birch_defn( $ns, 'init', function() use( $ns, $birchschedule ) {
                add_filter( 'birchschedule_fbuilder_field_get_field_lookup_config', function( $config ) {
                        $config['lookup_table']['selectable'] = array( 'selectable', '_root' );
                        return $config;
                    } );

                birch_defmethod( $birchschedule->fbuilder->field, 'render_options_editing',
                    'selectable', $ns->render_options_editing );

                birch_defmethod( $birchschedule->fbuilder->field, 'get_field_default_value',
                    'selectable', $ns->get_field_default_value );

                birch_defmethod( $birchschedule->fbuilder->field, 'render_field_editing',
                    'selectable', $ns->render_field_editing );

            } );

        birch_defn( $ns, 'render_options_editing',
            function( $field ) use( $ns, $birchschedule ) {

                $birchschedule->fbuilder->field->render_options_editing->fns['_root']( $field );
                $ns->render_option_choices( $field );
            } );

        birch_defn( $ns, 'get_field_default_value',
            function( $field ) {

                $default_value = "";
                if ( isset( $field['default_value'] ) ) {
                    $default_value = $field['default_value'];
                }
                return $default_value;
            } );

        birch_defmulti( $ns, 'render_option_choices', $birchschedule->fbuilder->field->get_field_lookup_config,
            function( $field ) use ( $ns ) {
?>
                <li>
                    <label><?php _e( 'Choices', 'birchschedule' ); ?></label>
                    <div class="birchschedule-choices-group">
                        <ul>
                            <?php $ns->render_choice_edit_items( $field ); ?>
                        </ul>
                        <a class="birchschedule-add-choice" href="javascript:void(0);"><?php _e( '+ Add Choice', 'birchschedule' ); ?></a>
                    </div>
                </li>
<?php
            } );

        birch_defmulti( $ns, 'render_choice_edit_items', $birchschedule->fbuilder->field->get_field_lookup_config,
            function( $field ) use ( $ns, $birchschedule ) {

                foreach ( $field['choices'] as $choice_value => $choice_text ) {
                    if ( $birchschedule->fbuilder->field->get_field_default_value( $field ) == $choice_value ) {
                        $checked = " checked='checked' ";
                    } else {
                        $checked = '';
                    }
                    $ns->render_choice_edit_item( $field, $choice_value, $choice_text, $checked );
                }
            } );

        birch_defmulti( $ns, 'render_choice_edit_box', $birchschedule->fbuilder->field->get_field_lookup_config,
            function( $field, $choice_value, $checked ) use ( $ns, $birchschedule ) {

?>
                <input type="radio" name="birchschedule_fields_options[<?php echo $field['field_id']; ?>][default_value]" value="<?php echo $choice_value; ?>" <?php echo $checked; ?>/>
<?php
            } );

        birch_defmulti( $ns, 'render_choice_edit_item', $birchschedule->fbuilder->field->get_field_lookup_config,
            function( $field, $choice_value, $choice_text, $checked = '' ) use ( $ns, $birchschedule ) {

                $choice_value = esc_attr( $choice_value );
                $choice_text = esc_attr( $choice_text );
?>
                <li>
                    <?php $ns->render_choice_edit_box( $field, $choice_value, $checked ); ?>
                    <input type="text" name="birchschedule_fields_options[<?php echo $field['field_id']; ?>][choices][<?php echo $choice_value ?>]" value="<?php echo $choice_text; ?>"/>
                    <a class="birchschedule-delete-choice" href="javascript:void(0);"><?php _e( 'Delete', 'birchschedule' ); ?></a>
                </li>
<?php
            } );

        birch_defmulti( $ns, 'get_new_choice_edit_li', $birchschedule->fbuilder->field->get_field_lookup_config,
            function( $field ) use ( $ns, $birchschedule ) {

                ob_start();
                $ns->render_choice_edit_item( $field, '', '' );
                return esc_js( ob_get_clean() );
            } );

        birch_defn( $ns, 'render_field_editing',
            function( $field ) use ( $ns, $birchschedule ) {

                $birchschedule->fbuilder->field->render_field_editing->fns['_root']( $field );
?>
                <script type="text/javascript">
                    //<![CDATA[
                    jQuery(document).ready( function($) {
                        var choiceEditLi = '<?php echo $ns->get_new_choice_edit_li( $field ); ?>';
                        choiceEditLi = $("<div />").html(choiceEditLi).text();

                        var initChoiceEdit = function(){
                            $('#birchschedule_form_builder .birchschedule-choices-group input[type=text]').change(function(){
                                $(this).attr('name', 'birchschedule_fields_options[<?php echo $field['field_id']; ?>][choices][' + $(this).val() + ']');
                                $(this).prev().val($(this).val());
                            });
                            $('#birchschedule_form_builder .birchschedule-choices-group .birchschedule-delete-choice').click(function(){
                                $(this).parent().remove();
                            });
                        };
                        initChoiceEdit();
                        $('#birchschedule_form_builder .birchschedule-choices-group .birchschedule-add-choice').click(function(){
                            var newHtml = $(this).prev().html() + choiceEditLi;
                            $(this).prev().append($(choiceEditLi));
                            initChoiceEdit();
                        });
                    });
                    //]]>
                </script>
<?php
            } );

    } );
