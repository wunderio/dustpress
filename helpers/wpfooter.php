<?php
namespace DustPress;

class WPFooter extends Helper {
    public function init( string $content, array $params ): string {
		ob_start();
		wp_footer();
		return ob_get_clean();
    }
}

Helper::register( dustpress()->twig, 'wpfooter', WPFooter::class );
