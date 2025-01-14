<?php
namespace DustPress;

class Title extends Helper {
    public function init( string $content, array $params ): string {
		ob_start();
		the_title();
		return ob_get_clean();
    }
}

Helper::register( dustpress()->twig, 'title', Title::class );