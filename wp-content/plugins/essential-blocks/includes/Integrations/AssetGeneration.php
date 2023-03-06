<?php
namespace EssentialBlocks\Integrations;

class AssetGeneration extends ThirdPartyIntegration {

    public function __construct() {
        $this->add_ajax( [
            'eb_regenerate_assets' => [
                'callback' => 'regenerate_assets',
                'public'   => false
            ]
        ] );
    }

    /**
     * regenerate_assets
     */
    public function regenerate_assets() {
        $response_data = ['messsage' => __( "Sorry, you are not allowed to do this operation.", "essential-blocks" )];

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( $response_data );
        }

        /**
         * Nonce verification
         */
        if ( isset( $_POST['admin_nonce'] ) && ! wp_verify_nonce( sanitize_key( $_POST['admin_nonce'] ), 'admin-nonce' ) ) {
            die( esc_html__( 'Nonce did not match', 'essential-blocks' ) );
        }

        if ( empty( $_POST ) ) {
            $response_data = ['messsage' => __( 'No post data found!', 'essential-blocks' )];
            wp_send_json_error( $response_data );
        }

        $upload_dir = wp_upload_dir();
        $base_path  = $upload_dir['basedir'];
        Self::remove_directory_files( $base_path . '/eb-style' );
        $response_data = [
            'messsage' => __( 'Successfully saved data!', 'essential-blocks' )
        ];
        wp_send_json_success( $response_data );
    }

    public static function remove_directory_files( $dir ) {
        foreach ( glob( $dir . '/*' ) as $file ) {
            if ( is_dir( $file ) ) {
                self::remove_directory_files( $file );
            } else {
                unlink( $file );
            }
        }
        rmdir( $dir );
    }
}
