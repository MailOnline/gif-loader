<?php

class Gif_Loader {

    public static $image;
    public static $image_location;

    public function __construct() {
        self::check_input();
    }

    private static function check_input() {

        $check_location = filter_var( 'http://' . $_GET['img_src'], FILTER_VALIDATE_URL );

        $headers = get_headers( $check_location );

        if ( false !== strpos( $headers[0], '200' ) ) {
            self::$image_location = $check_location;
            self::send_headers( false );
        } else {
            self::send_headers();
        }
    }

    private static function build_image() {
        /* Atddtempt to open */
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
