<?php
/**
 * Blocks Initializer
 *
 * Enqueue CSS/JS of all the blocks.
 *
 * @since   1.0.0
 * @package CGB
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Register the block on the init hook.
 *
 * Note: We need to pass the attributes in order for the ServerSideRender to work
 */
function register_author_avatars_block() {
	register_block_type(
		__DIR__  . '/build/show-avatar', // Path to the block.json file to load the files we need.
		array(
			'render_callback' => 'callback',
			'attributes'      => array(
				'user_id'          => array(
					'type' => 'number',
				),
				'size'             => array(
					'type' => 'number',
				),
				'email'            => array(
					'type' => 'string',
				),
				'alignment'        => array(
					'type' => 'string',
				),
				'link'             => array(
					'type' => 'string',
				),
				'display'          => array(
					'type' => 'object',
				),
				'sort_avatars_by'  => array(
					'type' => 'string',
				),
				'sort_order'       => array(
					'type' => 'string',
				),
				'limit'            => array(
					'type' => 'number',
				),
				'bio_length'       => array(
					'type' => 'number',
				),
				'page_size'        => array(
					'type' => 'number',
				),
				'min_post_count'   => array(
					'type' => 'number',
				),
				'hidden_users'     => array(
					'type' => 'string',
				),
				'whitelist_users'  => array(
					'type' => 'string',
				),
				'background_color' => array(
					'type' => 'string',
				),
				'font_color'       => array(
					'type' => 'string',
				),
				'block_style'      => array(
					'type' => 'string',
				),
				'className'        => array(
					'type' => 'string',
				),
				'border_radius'    => array(
					'type' => 'number',
				),
				'sort'             => array(
					'type' => 'string',
				),
				'sort_by'          => array(
					'type' => 'string',
				),
				'role'             => array(
					'type' => 'object',
				),
				'blogs'            => array(
					'type' => 'object',
				),
				'border_size'            => array(
					'type' => 'number',
				),
				'border_color'            => array(
					'type' => 'string',
				),
			)
		)
	);

}
add_action( 'init', 'register_author_avatars_block' );

function callback( $attributes, $content ) {
	wp_register_style( 'author-avatars-shortcode', plugins_url( 'css/shortcode.css',dirname(__DIR__) ) );


	$html = '';
//		$html .= '<pre>';
//
//		$html .= print_r( $attributes, true );
//		$html .= print_r( $content, true );
//		$html .= '</pre>';
//
	if ( ! isset( $attributes['user_id'] ) ) {
		if ( isset( $attributes['role'] ) ) {

			$attributes['user_id'] = 0;
		} elseif ( isset( $attributes['email'] ) ) {

			$attributes['user_id'] = - 1;
		} else {

			return 'ERROR: user_id missing';
		}
	}


	$atts = array(
		'avatar_size'      => ( isset( $attributes['size'] ) ) ? esc_attr( $attributes['size'] ) : false,
		'max_bio_length'   => (int) ( isset( $attributes['bio_length'] ) ) ? esc_attr( $attributes['bio_length'] ) : - 1,
		'align'            => ( isset( $attributes['alignment'] ) ) ? esc_attr( $attributes['alignment'] ) : '',
		'user_link'        => ( isset( $attributes['link'] ) ) ? esc_attr( $attributes['link'] ) : 'none',
		'border_radius'    => ( isset( $attributes['border_radius'] ) ) ? esc_attr( $attributes['border_radius'] ) : '',
		'background_color' => ( isset( $attributes['background_color'] ) ) ? esc_attr( $attributes['background_color']) : '',
		'font_color'       => ( isset( $attributes['font_color'] ) ) ? esc_attr( $attributes['font_color'] ) : '',
		'border_color'     => ( isset( $attributes['border_color'] ) ) ? esc_attr( $attributes['border_color'] ) : '',
		'border_size'      => (int) ( isset( $attributes['border_size'] ) ) ? esc_attr( $attributes['border_size'] ) : 0,
	);

	if ( isset( $attributes['display'] ) ) {
		foreach ( $attributes['display'] as $key => $value ) {
			$atts['display'][] = $key;
		}
	}
	if ( 0 === (int) $attributes['user_id'] ) {
		require_once( dirname(__DIR__ )  . '/lib/AuthorAvatarsShortcode.class.php' );
		$render = new \AuthorAvatarsShortcode();

		$atts['roles'] = ( isset( $attributes['role'] ) )? array_keys( $attributes['role'] ) : array();

		if ( isset( $attributes['blogs'] ) ) {

			$atts['blogs'] = $attributes['blogs'];
		}
		if ( ! empty( $attributes['hidden_users'] ) ) {

			$atts['hiddenusers'] = $attributes['hidden_users'];
		}
		if ( ! empty( $attributes['whitelist_users'] ) ) {

			$atts['whitelistusers'] = $attributes['whitelist_users'];
		}
//			if( isset( $attributes['whitelist_users'] ) ){
//
//				$atts['whitelistusers'] = $attributes['whitelist_users'];
//			}
		if ( isset( $attributes['limit'] ) ) {

			$atts['limit'] = $attributes['limit'];
		} else if ( ! isset( $attributes['role'] ) ) {
			$atts['limit'] = 20;
			$atts['roles'] = array( 'administrator' );
		}
		$atts['min_post_count'] = 0;
		if ( ! empty( $attributes['min_post_count'] ) && 1 <= $attributes['min_post_count'] ) {

			$atts['min_post_count'] = $attributes['min_post_count'];
		}
		if ( isset( $attributes['page_size'] ) ) {

			$atts['page_size'] = $attributes['page_size'];
		}
		if ( isset( $attributes['sort_avatars_by'] ) ) {

			$atts['order'] = $attributes['sort_avatars_by'];
		}
		if ( isset( $attributes['sort_order'] ) ) {

			$atts['sort_direction'] = $attributes['sort_order'];
		}


		$html .= $render->shortcode_handler( $atts );
	} else {
		require_once( dirname( __DIR__ ) . '/lib/ShowAvatarShortcode.class.php' );
		$render = new \ShowAvatarShortcode();


		if ( - 1 === $attributes['user_id'] ) {
			$atts['email'] = $attributes['email'];
		} else {
			$atts['id'] = $attributes['user_id'];
		}

		$html .= $render->shortcode_handler( $atts );
	}

	return $html;
}


/**
 * Enqueue Gutenberg block assets for backend editor.
 *
 * @uses {wp-blocks} for block type registration & related functions.
 * @uses {wp-element} for WP Element abstraction — structure of blocks.
 * @uses {wp-i18n} to internationalize the block's text.
 * @uses {wp-editor} for WP editor styles.
 * @since 1.0.0
 */
function author_avatar_editor_assets() { // phpcs:ignore
	wp_localize_script( 'author-avatars-block-js', 'authorAvatars', array(
		    'query_preview' => plugins_url( 'icon128x128.png', __FILE__ ),
            'wppic_preview' => plugins_url( 'icon128x128.png', __FILE__ ),
	));

}


// Hook: Editor assets.
add_action( 'enqueue_block_editor_assets', 'author_avatar_editor_assets' );
