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
		// Register the block using the metadata (block.json) from the build directory.
		// This ensures correct enqueuing of scripts and styles for the iframed editor (API v3).
		$block_type = register_block_type( dirname( dirname( __DIR__ ) ) . '/build/show-avatar', array(
			'render_callback' => array( $this, 'callback' ),
		) );

		// Enqueue the localized script data if the block was registered successfully.
		if ( $block_type && ! empty( $block_type->editor_script_handles ) ) {
			foreach ( $block_type->editor_script_handles as $handle ) {
				wp_localize_script( $handle, 'authorAvatars', array(
					'query_preview' => plugins_url( '../icon128x128.png', __FILE__ ),
					'wppic_preview' => plugins_url( '../icon128x128.png', __FILE__ ),
				) );
			}
		}
	}


	public function callback( $attributes, $content ) {
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

//var_dump($attributes);
		$atts = array(
			'avatar_size'      => ( isset( $attributes['size'] ) ) ? esc_attr( $attributes['size'] ) : false,
			'max_bio_length'   => (int) ( isset( $attributes['bio_length'] ) ) ? esc_attr( $attributes['bio_length'] ) : - 1,
			'align'            => ( isset( $attributes['alignment'] ) ) ? esc_attr( $attributes['alignment'] ) : '',
			'user_link'        => ( isset( $attributes['link'] ) ) ? esc_attr( $attributes['link'] ) : 'none',
			'avatar_radius'    => ( isset( $attributes['avatar_radius'] ) ) ? esc_attr( $attributes['avatar_radius'] ) : 0,

		);
		if( !  ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ){
			$atts['border_radius']    = ( isset( $attributes['border_radius'] ) ) ? esc_attr( $attributes['border_radius'] ) : '';
			$atts['background_color'] = ( isset( $attributes['background_color'] ) ) ? esc_attr( $attributes['background_color']) : '';
			$atts['font_color']       = ( isset( $attributes['font_color'] ) ) ? esc_attr( $attributes['font_color'] ) : '';
			$atts['border_color']     = ( isset( $attributes['border_color'] ) ) ? esc_attr( $attributes['border_color'] ) : '';
			$atts['border_size']      = (int) ( isset( $attributes['border_size'] ) ) ? esc_attr( $attributes['border_size'] ) : 0;
		}

		if ( isset( $attributes['display'] ) ) {
			foreach ( $attributes['display'] as $key => $value ) {
				$atts['display'][] = $key;
			}
		}
		if ( 0 === (int) $attributes['user_id'] ) {
			require_once( dirname( dirname( dirname(__DIR__ ) ) ) . '/lib/AuthorAvatarsShortcode.class.php' );
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
			require_once( dirname( dirname( dirname(__DIR__ ) ) ) . '/lib/ShowAvatarShortcode.class.php' );
			$render = new \ShowAvatarShortcode();


			if ( - 1 === $attributes['user_id'] ) {
				$atts['email'] = $attributes['email'];
			} else {
				$atts['id'] = $attributes['user_id'];
			}

			$html .= $render->shortcode_handler( $atts );
		}

		return $this->apply_card_and_wrapper_styles( $html, $attributes );
	}

	/**
	 * The shortcode renderers above apply background color / border
	 * size / border color / border radius as an inline style on the
	 * outer `.shortcode-author-avatars` wrapper (which spans the full
	 * block width) instead of on each individual user's card, and
	 * hardcode the border color to `transparent` on the wrapper
	 * regardless of the Border color setting.
	 *
	 * This moves those same live block attribute values onto each
	 * `.user` card instead, so the block's "Avatar Card" settings panel
	 * (background color, border size/color/corner, avatar padding/
	 * margin/border) always controls the per-user card and nothing else.
	 * The native "Dimensions"/"Border" Style tab (`style.spacing` /
	 * `style.border`) applies to the outer wrapper instead, matching
	 * standard WordPress block behavior.
	 *
	 * @param string $html       Rendered shortcode HTML.
	 * @param array  $attributes Block attributes.
	 * @return string
	 */
	private function apply_card_and_wrapper_styles( $html, $attributes ) {
		$card_style = array();
		if ( ! empty( $attributes['background_color'] ) ) {
			$card_style[] = sprintf( 'background-color:%s;', sanitize_hex_color( $attributes['background_color'] ) );
		}
		if ( ! empty( $attributes['font_color'] ) ) {
			$card_style[] = sprintf( 'color:%s;', sanitize_hex_color( $attributes['font_color'] ) );
		}
		if ( ! empty( $attributes['link_color'] ) ) {
			$card_style[] = sprintf( '--aa-card-link-color:%s;', sanitize_hex_color( $attributes['link_color'] ) );
		}
		if ( ! empty( $attributes['link_hover_color'] ) ) {
			$card_style[] = sprintf( '--aa-card-link-hover-color:%s;', sanitize_hex_color( $attributes['link_hover_color'] ) );
		}
		if ( ! empty( $attributes['border_size'] ) ) {
			$border_color = ! empty( $attributes['border_color'] ) ? sanitize_hex_color( $attributes['border_color'] ) : '#000';
			$card_style[] = sprintf( 'border:%dpx solid %s;', (int) $attributes['border_size'], $border_color );
		}
		if ( ! empty( $attributes['border_radius'] ) ) {
			$card_style[] = sprintf( 'border-radius:%dpx;', (int) $attributes['border_radius'] );
		}

		// Avatar-card Border
		$card_border_data = $attributes['card_border'] ?? array();
		if ( ! empty( $attributes['card_border_radius'] ) ) {
			$card_border_data['radius'] = $attributes['card_border_radius'];
		}
		if ( ! empty( $card_border_data ) ) {
			// Ensure units for numeric values to prevent wp_style_engine from emitting unitless lengths.
			if ( isset( $card_border_data['radius'] ) ) {
				if ( is_numeric( $card_border_data['radius'] ) ) {
					$card_border_data['radius'] .= 'px';
				} elseif ( is_array( $card_border_data['radius'] ) ) {
					$card_border_data['radius'] = $this->add_missing_px_units( $card_border_data['radius'] );
				}
			}
			foreach ( array( 'top', 'right', 'bottom', 'left' ) as $side ) {
				if ( isset( $card_border_data[ $side ]['width'] ) && is_numeric( $card_border_data[ $side ]['width'] ) ) {
					$card_border_data[ $side ]['width'] .= 'px';
				}
			}
			if ( isset( $card_border_data['width'] ) && is_numeric( $card_border_data['width'] ) ) {
				$card_border_data['width'] .= 'px';
			}

			$card_border_styles = wp_style_engine_get_styles( array( 'border' => $card_border_data ) );
			if ( ! empty( $card_border_styles['css'] ) ) {
				$card_style[] = rtrim( $card_border_styles['css'], ';' ) . ';';
			}
		}

		if ( ! empty( $attributes['card_min_width'] ) ) {
			$card_style[] = sprintf( 'min-width:%s;', $this->add_px_if_numeric( $attributes['card_min_width'] ) );
		}
		if ( ! empty( $attributes['card_max_width'] ) ) {
			$card_style[] = sprintf( 'max-width:%s;', $this->add_px_if_numeric( $attributes['card_max_width'] ) );
		}
		if ( ! empty( $attributes['card_min_height'] ) ) {
			$card_style[] = sprintf( 'min-height:%s;', $this->add_px_if_numeric( $attributes['card_min_height'] ) );
		}
		if ( ! empty( $attributes['card_max_height'] ) ) {
			$card_style[] = sprintf( 'max-height:%s;', $this->add_px_if_numeric( $attributes['card_max_height'] ) );
		}

		$avatar_styles = wp_style_engine_get_styles( array(
			'spacing' => array(
				'padding' => $this->add_missing_px_units( $attributes['avatar_padding'] ?? array() ),
				'margin'  => $this->add_missing_px_units( $attributes['avatar_margin'] ?? array() ),
			),
		) );
		if ( ! empty( $avatar_styles['css'] ) ) {
			$card_style[] = rtrim( $avatar_styles['css'], ';' ) . ';';
		}

		// Native "Dimensions"/"Border" Style tab values apply to the OUTER
		// block wrapper only. Skip this during the editor's ServerSideRender
		// REST preview: Gutenberg's own block-list wrapper already shows the
		// native border/spacing there via its built-in support HOCs, so
		// applying it again here would double it up. On the real front end
		// there is no such wrapper, so this is the only place it can render.
		$is_editor_preview    = defined( 'REST_REQUEST' ) && REST_REQUEST;
		$wrapper_styles       = $is_editor_preview ? array() : wp_style_engine_get_styles( array(
			'spacing'    => $attributes['style']['spacing'] ?? array(),
			'border'     => $attributes['style']['border'] ?? array(),
			'color'      => $attributes['style']['color'] ?? array(),
			'background' => $attributes['style']['background'] ?? array(),
		) );
		$wrapper_extra_style  = ! empty( $wrapper_styles['css'] ) ? rtrim( $wrapper_styles['css'], ';' ) . ';' : '';

		if ( empty( $card_style ) && '' === $wrapper_extra_style ) {
			return $html;
		}

		// Strip the plugin's own background/border/radius from the outer
		// wrapper (moved to the card below), keeping layout declarations
		// (e.g. padding-left / align), then add the native wrapper-level style.
		$html = preg_replace_callback(
			'/(<div class="shortcode-author-avatars" style=")([^"]*)(")/',
			function ( $m ) use ( $wrapper_extra_style ) {
				$kept = array();
				foreach ( array_filter( array_map( 'trim', explode( ';', $m[2] ) ) ) as $decl ) {
					if ( ! preg_match( '/^(background-color|border-color|border-radius|border|color)\s*:/i', $decl ) ) {
						$kept[] = $decl;
					}
				}
				$style = $kept ? implode( '; ', $kept ) . ';' : '';
				if ( $wrapper_extra_style ) {
					$style .= ( $style ? ' ' : '' ) . $wrapper_extra_style;
				}
				return $m[1] . $style . $m[3];
			},
			$html
		);

		if ( empty( $card_style ) ) {
			return $html;
		}
		$card_style = implode( ' ', $card_style );

		// Add the card style to each user's card div.
		$html = preg_replace_callback(
			'/(<div class="[^"]*\buser\b[^"]*" style=")([^"]*)(")/',
			function ( $m ) use ( $card_style ) {
				$existing = trim( $m[2] );
				$style    = $existing ? rtrim( $existing, ';' ) . '; ' . $card_style : $card_style;
				return $m[1] . $style . $m[3];
			},
			$html
		);

		return $html;
	}

	/**
	 * Default any purely-numeric value to px.
	 *
	 * @param string $value CSS value.
	 * @return string
	 */
	private function add_px_if_numeric( $value ) {
		return is_numeric( $value ) ? $value . 'px' : $value;
	}

	/**
	 * The Padding/Margin BoxControl in the block's "Avatar Card" panel starts
	 * with no stored unit, so a value typed via its numeric slider (rather
	 * than its unit dropdown) is saved as a bare number (e.g. "156") instead
	 * of a CSS length (e.g. "156px"). wp_style_engine_get_styles() then
	 * emits an invalid declaration like "padding-top:156;", which browsers
	 * silently drop. Default any purely-numeric side value to px so the
	 * setting always takes visible effect.
	 *
	 * @param array $sides Map of side => value (e.g. top/right/bottom/left).
	 * @return array
	 */
	private function add_missing_px_units( $sides ) {
		if ( ! is_array( $sides ) ) {
			return $sides;
		}
		foreach ( $sides as $side => $value ) {
			if ( is_numeric( $value ) ) {
				$sides[ $side ] = $value . 'px';
			}
		}
		return $sides;
	}
}

