<?php
namespace DustPress;

class Permalink extends Helper {
    public function init ( string $content, array $params ): string {
    	if ( isset( $params['id'] ) ) {
    		return get_permalink( $params['id'] );
    	}
    	else {
    		return get_permalink();
    	}
	}
}

Helper::register( dustpress()->twig, 'permalink', Permalink::class );
