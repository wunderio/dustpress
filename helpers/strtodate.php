<?php
namespace DustPress;

class Strtodate extends Helper {
    public function init( string $content, array $params ): string {
		$value 	= $params['value'];
	    	if ( isset( $params['format'] ) ) {
			$format	= $params['format'];
		} else {
			$format = get_option( 'date_format' );
		}
		$now	= $params['now'];

		return date_i18n( $format, strtotime( $value, $now ) );
    }
}

Helper::register( dustpress()->twig, 'strtodate', Strtodate::class );
