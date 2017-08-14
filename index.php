<?php

class Gif_Loader {

    public static $image;
    public static $image_location;

    public function __construct() {
        self::check_input();
    }

    private static function is_valid_image( $url ) {
        
        $headers = get_headers( $url );

        if ( false !== strpos( $headers[0], '200' ) ) {
            self::$image_location = $url;
            return true;
        } else {
            return false;
        }
    }

    private static function check_input() {

        $check_location = filter_var( 'http://' . $_GET['img_src'], FILTER_VALIDATE_URL );
        $check_secure_location = filter_var( 'https://' . $_GET['img_src'], FILTER_VALIDATE_URL );
        
        if ( ! self::is_valid_image( $check_location ) ) {
            $secure_found = self::is_valid_image( $check_secure_location );
            if ( ! $secure_found ) {
                self::send_headers();
            }
        }
        self::send_headers( false );

        $headers = get_headers( $check_location );
    }

    private static function build_image() {
        /* Attempt to open */
        $im = @imagecreatefromgif( self::$image_location );

        if ( ! $im ){
            $im = imagecreatetruecolor( 150, 30 );
            $bgc = imagecolorallocate( $im, 255, 255, 255 );
            $tc = imagecolorallocate( $im, 0, 0, 0 );
            imagefilledrectangle( $im, 0, 0, 150, 30, $bgc );
            imagestring( $im, 1, 5, 5, 'Error loading ' . self::$image_location, $tc );
        }
        self::$image = $im;
        self::output();
        self::cleanup();
    }

    private static function output() {
        imagegif( self::$image );
    }

    private static function send_headers( $error = true ) {

	header('Cache-Control: max-age=900');

        if ( $error ) {
            header('HTTP/1.0 400 Bad Request', true, 400);
            exit;
        } else {
            header('Content-Type: image/gif');
            self::build_image();
        }
    }

    public static function cleanup() {
        imagedestroy( self::$image );
    }

}

new Gif_Loader;

?>
