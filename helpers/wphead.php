<?php
namespace DustPress;

class WPHead extends Helper {
    public function init( string $content, array $params ): string {
		ob_start();
		wp_head();
		return ob_get_clean();
    }
}

Helper::register( dustpress()->twig, 'wphead', WPHead::class );
