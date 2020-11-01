<?php
/**
 * wp-author-avatars.
 * User: Paul
 * Date: 2018-12-19
 *
 */

namespace author_avatar\show_avatar;


if ( ! defined( 'WPINC' ) ) {
	die;
}

class Render {

	function __construct() {

		add_action( 'init', array( $this, 'init' ) );
	}


	public function init() {

		register_block_type( 'author-avatars/show-avatar', array(
			'render_callback' => array( $this, 'callback' ),
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
			)
		) );
	}


	public function callback( $attributes, $content ) {
		wp_register_style( 'author-avatars-shortcode', plugins_url( 'css/shortcode.css',dirname(  dirname( __FILE__ ) ) ) );


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
			'avatar_size'      => ( isset( $attributes['size'] ) ) ? $attributes['size'] : false,
			'max_bio_length'   => ( isset( $attributes['bio_length'] ) ) ? $attributes['bio_length'] : - 1,
			'align'            => ( isset( $attributes['alignment'] ) ) ? $attributes['alignment'] : '',
			'user_link'        => ( isset( $attributes['link'] ) ) ? $attributes['link'] : 'none',
			'border_radius'    => ( isset( $attributes['border_radius'] ) ) ? $attributes['border_radius'] : '',
			'background_color' => ( isset( $attributes['background_color'] ) ) ? $attributes['background_color'] : '',
			'font_color'       => ( isset( $attributes['font_color'] ) ) ? $attributes['font_color'] : '',
		);
		if ( isset( $attributes['display'] ) ) {
			foreach ( $attributes['display'] as $key => $value ) {
				$atts[ $key ] = $value;
			}
		}
		if ( 0 === (int) $attributes['user_id'] ) {
			require_once( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/lib/AuthorAvatarsShortcode.class.php' );
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
			require_once( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/lib/ShowAvatarShortcode.class.php' );
			$render = new \ShowAvatarShortcode();


			if ( - 1 == $attributes['user_id'] ) {
				$atts['email'] = $attributes['email'];
			} else {
				$atts['id'] = $attributes['user_id'];
			}

			$html .= $render->shortcode_handler( $atts );
		}


//		$html .= ob_get_clean() ;


//		require_once( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/lib/AuthorAvatarsEditorButton.class.php' );
//		$editor_button = new \AuthorAvatarsEditorButton();
//
//		ob_start();
//
//		$editor_button->render_tinymce_popup_body();
//
//		$html .= str_replace( 'body>', 'div/>', ob_get_clean() );

		return $html;
	}
}

