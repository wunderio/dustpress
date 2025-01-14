<?php
/**
 * Password protection.
 */

namespace DustPress;

/**
 * Password protection helper
 *
 * @example {@password}Anything worth password protection will be replaced with password form.{/password}
 * @example {@password no_form=true}Should be used with an instance where form isn't hidden.{/password}
 *
 * @package DustPress
 */
class Password extends Helper {
    public function init( string $content, array $params ): string {
        $id      = $params['id'] ?? get_the_ID();
        $no_form = isset( $params['no_form'] );

        $post = get_post( $id );

        // If a password is not needed, render the content block as is
        if ( empty( $post->post_password ) || ! post_password_required( $id ) ) {
            return $content;
        }

        // If used like {@password no_form=anything} the contents are hidden,
        // but no password_form partial will be rendered.
        if ( $no_form ) {
            return '';
        }

        // Populate data object to be passed to the password form template
        $data        = new \stdClass();
        $data->url   = esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) );
        $data->label = 'pwbox-' . $id;

        return dustpress()->render( [
            'partial' => 'password_form',
            'data'    => $data,
            'echo'    => false,
        ] );
    }
}

Helper::register( dustpress()->twig, 'password', Password::class );
