<?php
namespace DustPress;

class Content extends Helper {
    public function init( string $content, array $params ): string {
        global $post;

        if ( isset( $params['data'] ) ) {
            return apply_filters( 'the_content', $params['data'] );
        } else {
            if ( isset( $params['id'] ) ) {
                $post = get_post( $params['id'] );
            }

            ob_start();
            setup_postdata( $post );
            the_content();
            wp_reset_postdata();
            return ob_get_clean();
        }
    }
}

Helper::register( dustpress()->twig, 'content', Content::class );