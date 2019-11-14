<?php

function btc_add_dashboard_widgets() {
    // Add function here
    $widget_id = "btc-config";
    $widget_name = esc_html__("Configuration du site", "btc");
    $callback_configure = null;
    $callback_args = null;

    wp_add_dashboard_widget( $widget_id, $widget_name, 'btc_config_widget_render', $callback_configure, $callback_args );


    // == Force widget to the top ==
    // Globalize the metaboxes array, this holds all the widgets for wp-admin.
    global $wp_meta_boxes;
    // Get the regular dashboard widgets array (which already has our new widget but appended at the end).
    $default_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];
    // Backup and delete our new dashboard widget from the end of the array.
    $example_widget_backup = array( 'btc-config' => $default_dashboard['btc-config'] );
    unset( $default_dashboard['btc-config'] );
    // Merge the two arrays together so our widget is at the beginning.
    $sorted_dashboard = array_merge( $example_widget_backup, $default_dashboard );
    // Save the sorted array back into the original metaboxes. 
    $wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;

}
add_action( 'wp_dashboard_setup', 'btc_add_dashboard_widgets' );

function btc_config_widget_render() {
    //$default_options = ['date_festival_from' => '', 'date_festival_to' => '', 'etat_inscriptions' => 'closed'];
    $options = get_option('btc-config');
    //print_r($options);

    echo "<h3><b>Tu peux gérer ici les principaux paramètres du site Be The Church.</b>
    <br>Pour ajouter ces éléments dans du texte sur le site, tu peux ajouter le code correspondant directement dans le texte. Il sera automatiquement remplacé par la bonne valeur.
    </h3>";
    echo '<p>
        Le Festival aura lieu 
            <br>du <input id="date_festival_from" type="date" value="' . $options['date_festival_from'] . '"> (code : <span class="config_code">{date_festival_from}</span> )
            <br>au <input id="date_festival_to" type="date" value="' . $options['date_festival_to'] . '"> (code : <span class="config_code">{date_festival_to}</span> )
    </p>
    <p>
    État des inscriptions : <select id="etat_inscriptions">';
        foreach (['Fermées', 'PRÉ-INSCRIPTION' ,'PRÉINSCRIS OU INSCRIS-TOI', 'RÉSERVE TA PLACE'] as $v) {
            echo '<option value="'.$v.'" '.($options['etat_inscriptions'] == $v ? 'selected': '').'>'.$v.'</option>';
        }
    echo '</select> (code : {etat_inscriptions})
    </p>';

    echo '<button id="submit_btc_config">Mettre à jour</button>';

    echo '<script>
    jQuery( document ).ready( function( $ ){

        $(document).on("click", "#submit_btc_config", function() {
            let submit_text = $("#submit_btc_config").html();
            $("#submit_btc_config").html("<i class=\"fa fa-spinner fa-spin\"></i>");
            wp.ajax.post( "btc_config_endpoint", {
                "btc-config" : {
                    date_festival_from: $("#date_festival_from").val(),
                    date_festival_to: $("#date_festival_to").val(),
                    etat_inscriptions: $("#etat_inscriptions").val(),
                },
            } )
            .done( function( response ) {
                console.log( response );
                $("#submit_btc_config").html(submit_text);
            } )
            .fail( function(err) {
                console.error( "err", err );
                $("#submit_btc_config").html(submit_text);
            } );
        });
    
    });
    </script>';
}


add_action( 'admin_enqueue_scripts', function() {
    wp_enqueue_style( 'ccnbtc-fa', 'https://use.fontawesome.com/releases/v5.5.0/css/all.css');
    wp_register_script( 'btc_config_script', get_template_directory_uri() . '/js/admin/main.js', array( 'jquery', 'wp-util' ), '0.1.0', true );
});

function btc_config_server_save(){

    if (!isset($_POST['btc-config'])) {
        wp_send_json_error( ['success' => false, 'data' => $_POST], Requests_Exception_HTTP_400 );
        wp_die();
    }

    update_option('btc-config', $_POST['btc-config']);

    //Create the array we send back to javascript here
    $array_we_send_back = array( 'success' => true, 'data' => $_POST['btc-config'] );

    //Make sure to json encode the output because that's what it is expecting
    wp_send_json_success( $array_we_send_back );
    wp_die();

}
add_action( 'wp_ajax_' . 'btc_config_endpoint', 'btc_config_server_save' );
add_action( 'wp_ajax_nopriv_' . 'btc_config_endpoint', 'btc_config_server_save' );

?>