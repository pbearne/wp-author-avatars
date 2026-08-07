<?php

/**
 * Author Avatars Shortcode: provides a shortcode for displaying avatars of blog users
 *
 */
class AuthorAvatarsShortcode {

	var $userlist;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->register();
	}

	/**
	 * register shortcode
	 */
	public function register() {
		add_shortcode( 'authoravatars', array( &$this, 'shortcode_handler' ) );
//		add_action( 'wp_enqueue_scripts', array( &$this, 'enqueue_resources' ), 20 );
	}

	/**
	 * The shortcode handler for the [authoravatars] shortcode.
	 *
	 * @param $atts
	 * @param null $content
	 * @param bool $skip_wrapper
	 *
	 * @return string
	 */
	public function shortcode_handler( $atts, $content = null, $skip_wrapper = false ) {
		require_once( 'UserList.class.php' );
		wp_enqueue_style( 'author-avatars-shortcode' );
		$this->userlist = new UserList();
		$settings       = AA_settings();

		$atts = array_map( 'aa_clean_commas', $atts );

		// roles
		$roles = array(); // default value: no restriction -> all users
		if ( ! empty( $atts['roles'] ) ) {
			if ( ! is_array( $atts['roles'] ) ) {
				$roles = explode( ',', $atts['roles'] );
			} else {
				$roles = $atts['roles'];
			}
			$roles = array_map( 'trim', $roles );
		}
		$this->userlist->roles = $roles;

		// blogs
		$blogs = array(); // default value: empty -> only current blog
		if ( ! empty( $atts['blogs'] ) && $settings->blog_selection_allowed() ) {
			if ( strtolower( $atts['blogs'] ) === 'all' ) {
				$blogs = array( - 1 );
			} else {
				if ( ! is_array( $atts['blogs'] ) ) {
					$blogs = explode( ',', esc_attr( $atts['blogs'] ) );
				} else {
					$blogs = array_map( 'trim', $blogs );
				}
				$blogs = array_map( 'intval', $blogs );
			}
		}
		$this->userlist->blogs = $blogs;

		// perform a switch to another MU blog_id (to set avatar/path relations)
		$switch_back_to_blog_id = false;
		if ( $settings->blog_selection_allowed() && ! empty( $atts['switchblog'] ) ) {
			if ( $GLOBALS['blog_id'] !== (int) $atts['switchblog'] ) {
				$switch_back_to_blog_id = $GLOBALS['blog_id'];
				switch_to_blog( (int) $atts['switchblog'] );
			}
		}

		// grouping
		$group_by = '';
		if ( isset( $atts['group_by'] ) ) {
			if ( AA_is_wpmu() && 'blog' === esc_attr( $atts['group_by'] ) ) {
				$group_by = 'blog';
			}
		}
		$this->userlist->group_by = $group_by;

		// hidden users
		$hiddenusers = array(); // default value: no restriction -> all users
		if ( ! empty( $atts['hiddenusers'] ) ) {
			if ( ! is_array( $atts['hiddenusers'] ) ) {
				$hiddenusers = array_unique( explode( ',', esc_attr( $atts['hiddenusers'] ) ) );
			} else {
				$hiddenusers = array_map( 'esc_attr', $atts['hiddenusers'] );
			}
		}
		$this->userlist->hiddenusers = array_map( 'trim', $hiddenusers );

		// whitelist users
		$whitelistusers = array(); // default value: no restriction -> all users
		if ( ! empty( $atts['whitelistusers'] ) ) {
			if ( ! is_array( $atts['whitelistusers'] ) ) {
				$whitelistusers = array_unique( explode( ',', esc_attr( $atts['whitelistusers'] ) ) );
			}
		}
		$this->userlist->whitelistusers = array_map( 'trim', $whitelistusers );

		// just these users
		$onlyusers = array(); // default value: no restriction -> all users
		if ( ! empty( $atts['onlyusers'] ) ) {
			if ( ! is_array( $atts['onlyusers'] ) ) {
				$onlyusers = explode( ',', esc_attr( $atts['onlyusers'] ) );
			}
		}
		$this->userlist->onlyusers = array_map( 'trim', $onlyusers );

		// link to author page? (deprecated)
		if ( isset( $atts['link_to_authorpage'] ) && ( $atts['link_to_authorpage'] !== '' ) ) {
			// by default always true, has to be set explicitly to not link the users
			$set_to_false = ( $atts['link_to_authorpage'] === 'false' || (bool) $atts['link_to_authorpage'] === false );
			if ( $set_to_false ) {
				$this->userlist->user_link = 'none';
			}
		}

		if ( ! empty( $atts['user_link'] ) ) {
			$this->userlist->user_link = esc_attr( $atts['user_link'] );
		}

		if ( ! empty( $atts['contact_links'] ) ) {
			$this->userlist->contact_links = esc_attr( $atts['contact_links'] );
		}

		require_once( 'AuthorAvatarsEditorButton.class.php' );
		$display = AAFormHelper::get_display_list( $atts );

		// support for all style shortcode
		$default_display_options = array(
			'show_name',
			'show_postcount',
			'show_email',
			'show_nickname',
			'show_biography',
			'show_last_post',
			'show_bbpress_post_count',
		);
		// loop the old name=true settings and add them to the new array format
		foreach ( $default_display_options as $default_display_option ) {
			if ( isset( $atts[ $default_display_option ] ) && ( $atts[ $default_display_option ] !== '' ) ) {
				if ( $atts[ $default_display_option ] && ! in_array( $default_display_option, $display, true ) ) {
					$display[] = $default_display_option;
				}
			}

		}
		// the defaults array and set the globals if found
		foreach ( $default_display_options as $default_display_option ) {
			if ( in_array( $default_display_option, $display ) ) {
				$this->userlist->$default_display_option = true;
			} else {
				$this->userlist->$default_display_option = false;
			}
		}

		$this->userlist->display_extra = array_diff( $display, $default_display_options );

		//var_dump($this->userlist->display_extra);

		// avatar size
		if ( ! empty( $atts['avatar_size'] ) ) {
			$this->userlist->avatar_size = $atts['avatar_size'];
		}
		if ( ! empty( $atts['avatar_radius'] ) ) {
			$avatar_radius = (int) $atts['avatar_radius'];
			if ( $avatar_radius > 0 ) {
				$this->userlist->avatar_radius = $avatar_radius;
			}
		}
		if ( ! empty( $atts['border_radius'] ) ) {
			$border_radius = (int) $atts['border_radius'];
			if ( $border_radius > 0 ) {
				$this->userlist->border_radius = $border_radius;
			}
		}
		if ( ! empty( $atts['align'] ) ) {
			$this->userlist->align = esc_attr( $atts['align'] );
		}

		// max. number of avatars
		if ( ! empty( $atts['limit'] ) ) {
			$limit = (int) $atts['limit'];
			if ( $limit > 0 ) {
				$this->userlist->limit = $limit;
			}
		}

		// max. number of avatars
		$this->userlist->bio_length = - 1;
		if ( ! empty( $atts['max_bio_length'] ) ) {
			$bio_length = (int) esc_attr( $atts['max_bio_length'] );
			if ( 0 < $bio_length ) {
				$this->userlist->bio_length = $bio_length;
			}
		} elseif ( ! empty( $atts['bio_length'] ) ) { // Handle both keys
			$bio_length = (int) esc_attr( $atts['bio_length'] );
			if ( 0 < $bio_length ) {
				$this->userlist->bio_length = $bio_length;
			}
		}

		// min. number of posts
		if ( ! empty( $atts['min_post_count'] ) ) {
			$min_post_count = (int) esc_attr( $atts['min_post_count'] );
			if ( 0 < $min_post_count ) {
				$this->userlist->min_post_count = $min_post_count;
			}
		}
		// get page size
		if ( ! empty( $atts['page_size'] ) ) {
			$page_size = (int) $atts['page_size'];
			if ( 0 < $page_size ) {
				$this->userlist->page_size = $page_size;
			}
		}

		// get paging page
		if ( ! empty( $atts['aa_page'] ) ) {

			$page_size = (int) $atts['aa_page'];
			if ( 0 < $page_size ) {
				$this->userlist->aa_page = $page_size;
			}
		} elseif ( isset( $_REQUEST['aa_page'] ) && is_numeric( $_REQUEST['aa_page'] ) ) {
			$page_size = (int) $_REQUEST['aa_page'];
			if ( 0 < $page_size ) {
				$this->userlist->aa_page = $page_size;
			}
		}

		// display order
		$sort_direction = 'asc';
		if ( ! empty( $atts['order'] ) ) {
			$order = esc_attr( $atts['order'] );
			if ( str_contains( $order, ',' ) ) {
				list( $order, $sort_direction ) = explode( ',', $order, 2 );
			}
			$this->userlist->order = $order;
		}
		if ( ! empty( $atts['sort_direction'] ) ) {
			$sort_direction = esc_attr( $atts['sort_direction'] );
		}
		$valid_directions = array( 'asc', 'ascending', 'desc', 'descending' );
		if ( in_array( $sort_direction, $valid_directions, true ) ) {
			$this->userlist->sort_direction = $sort_direction;
		}

		// render as a list?
		if ( isset( $atts['render_as_list'] ) ) {
			$set_to_false = ( $atts['render_as_list'] === 'false' );
			if ( ! $set_to_false ) {
				$this->userlist->use_list_template();
			}
		}

		// Pass block attributes to UserList for localized JS
		foreach (
			array(
				'background_color',
				'font_color',
				'border_size',
				'border_color',
				'link_color',
				'link_hover_color',
				'card_border',
				'card_border_radius',
				'card_min_width',
				'card_max_width',
				'card_min_height',
				'card_max_height',
				'avatar_padding',
				'avatar_margin',
				'avatar_border',
				'avatar_border_radius',
			) as $key
		) {
			if ( isset( $atts[ $key ] ) ) {
				$this->userlist->$key = $atts[ $key ];
			}
		}

		$inner_content = $this->userlist->get_output() . $content . $this->userlist->pagingHTML;

		$style = array();
		if ( ! empty( $atts['border_radius'] ) ) {
			$style[] = sprintf( 'border-radius:%dpx;', $atts['border_radius'] );
		}
		if ( ! empty( $atts['background_color'] ) ) {
			$style[] = sprintf( 'background-color:%s;', sanitize_hex_color( $atts['background_color'] ) );
		}
		if ( ! empty( $atts['font_color'] ) ) {
			$style[] = sprintf( 'color:%s;', sanitize_hex_color( $atts['font_color'] ) );
		}
		if ( ! empty( $atts['border_size'] ) ) {
			$style[] = sprintf( 'border:solid transparent %dpx;', $atts['border_size'] );
		}
		if ( ! empty( $atts['border_color'] ) ) {
			$style[] = sprintf( 'border-color:%s;', sanitize_hex_color( $atts['border_color'] ) );
		}
		if ( ! empty( $atts['align'] ) ) {
			switch ( $atts['align'] ) {
				case 'right':
					$style[] = 'padding-right: 0.4em;';
					break;
				case 'center':
					break;
			}
		} else {
			$style[] = 'padding-left: 0.2em;';
		}

		$html = '<div class="shortcode-author-avatars" style="' . implode( ' ', $style ) . '">' . $inner_content . '</div>';

		// Apply post-processing (moving styles to cards)
		$html = $this->apply_card_and_wrapper_styles( $html, $atts );

		if ( $skip_wrapper ) {
			// Extract inner content from the (potentially modified) wrapper
			if ( preg_match( '/^<div\s+class="[^"]*shortcode-author-avatars[^"]*"[^>]*>(.*)<\/div>$/is', $html, $matches ) ) {
				$html = $matches[1];
			}
		}

		if ( $switch_back_to_blog_id ) {
			switch_to_blog( $switch_back_to_blog_id );
		}

		return $html;
	}

	/**
	 * Moves styling from the outer wrapper to individual user cards.
	 * Replicated logic from Gutenberg block's Render class for consistency.
	 *
	 * @param string $html
	 * @param array $attributes
	 *
	 * @return string
	 */
	public function apply_card_and_wrapper_styles( $html, $attributes ) {
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
		if ( ! empty( $card_border_data ) && function_exists( 'wp_style_engine_get_styles' ) ) {
			// Ensure units for numeric values.
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

		$avatar_styles = function_exists( 'wp_style_engine_get_styles' ) ? wp_style_engine_get_styles( array(
			'spacing' => array(
				'padding' => $this->add_missing_px_units( $attributes['avatar_padding'] ?? array() ),
				'margin'  => $this->add_missing_px_units( $attributes['avatar_margin'] ?? array() ),
			),
		) ) : array();
		if ( ! empty( $avatar_styles['css'] ) ) {
			$card_style[] = rtrim( $avatar_styles['css'], ';' ) . ';';
		}

		// Avatar Image Border and Radius
		$avatar_image_style = array();
		$avatar_border_data = $attributes['avatar_border'] ?? array();
		if ( ! empty( $attributes['avatar_border_radius'] ) ) {
			$avatar_border_data['radius'] = $attributes['avatar_border_radius'];
		}
		if ( ! empty( $avatar_border_data ) && function_exists( 'wp_style_engine_get_styles' ) ) {
			// Ensure units for numeric values.
			if ( isset( $avatar_border_data['radius'] ) ) {
				if ( is_numeric( $avatar_border_data['radius'] ) ) {
					$avatar_border_data['radius'] .= 'px';
				} elseif ( is_array( $avatar_border_data['radius'] ) ) {
					$avatar_border_data['radius'] = $this->add_missing_px_units( $avatar_border_data['radius'] );
				}
			}
			foreach ( array( 'top', 'right', 'bottom', 'left' ) as $side ) {
				if ( isset( $avatar_border_data[ $side ]['width'] ) && is_numeric( $avatar_border_data[ $side ]['width'] ) ) {
					$avatar_border_data[ $side ]['width'] .= 'px';
				}
			}
			if ( isset( $avatar_border_data['width'] ) && is_numeric( $avatar_border_data['width'] ) ) {
				$avatar_border_data['width'] .= 'px';
			}

			$avatar_border_styles = wp_style_engine_get_styles( array( 'border' => $avatar_border_data ) );
			if ( ! empty( $avatar_border_styles['css'] ) ) {
				$avatar_image_style[] = rtrim( $avatar_border_styles['css'], ';' ) . ';';
			}
		}

		$is_editor_preview = defined( 'REST_REQUEST' ) && REST_REQUEST;

		// Process the outer wrapper div
		$html = preg_replace_callback(
			'/(<div\s+class="shortcode-author-avatars"([^>]*)>)/i',
			function ( $m ) use ( $attributes, $is_editor_preview ) {
				$attrs_string = $m[2];

				// Extract style and class from the legacy tag
				$legacy_style = '';
				if ( preg_match( '/style="([^"]*)"/i', $attrs_string, $s_match ) ) {
					$legacy_style = $s_match[1];
				}
				$legacy_class = 'shortcode-author-avatars';
				if ( preg_match( '/class="([^"]*)"/i', $attrs_string, $c_match ) ) {
					$legacy_class = $c_match[1];
				}

				// Strip legacy card styles from legacy_style (they are moved to .user cards below)
				$kept_styles = array();
				foreach ( array_filter( array_map( 'trim', explode( ';', $legacy_style ) ) ) as $decl ) {
					if ( ! preg_match( '/^(background-color|border-color|border-radius|border|color)\s*:/i', $decl ) ) {
						$kept_styles[] = $decl;
					}
				}
				$kept_style_string = implode( '; ', $kept_styles ) . ( $kept_styles ? ';' : '' );

				// Remove existing class and style from attrs_string so we can replace them
				$other_attrs = preg_replace( '/\b(class|style)\s*=\s*"[^"]*"\s*/i', '', $attrs_string );

				// Get native wrapper attributes (classes and styles) for the front end.
				// In the editor preview, ServerSideRender's own wrapper already handles these.
				if ( ! $is_editor_preview && function_exists( 'get_block_wrapper_attributes' ) ) {
					$wrapper_attributes = get_block_wrapper_attributes( array(
						'class' => $legacy_class,
						'style' => $kept_style_string,
					) );

					return '<div ' . $wrapper_attributes . ' ' . trim( $other_attrs ) . '>';
				}

				// Fallback or editor preview: reconstruct the tag manually
				$new_attrs = ' class="' . esc_attr( $legacy_class ) . '"';
				if ( $kept_style_string ) {
					$new_attrs .= ' style="' . esc_attr( $kept_style_string ) . '"';
				}

				return '<div' . $new_attrs . ' ' . trim( $other_attrs ) . '>';
			},
			$html
		);

		if ( empty( $card_style ) && empty( $avatar_image_style ) ) {
			return $html;
		}
		$card_style_string = implode( ' ', $card_style );

		// Add the card style to each user's card div.
		$html = preg_replace_callback(
			'/(<div[^>]+class=["\']([^"\']*\buser\b[^"\']*)["\']([^>]*>))/i',
			function ( $m ) use ( $card_style_string ) {
				$tag = $m[1];
				// Match style attribute with either single or double quotes
				if ( preg_match( '/style=(["\'])(.*?)\1/i', $tag, $s_match ) ) {
					$quote    = $s_match[1];
					$existing = rtrim( trim( $s_match[2] ), ';' ) . '; ';
					$new_tag  = str_replace( $s_match[0], 'style=' . $quote . $existing . $card_style_string . $quote, $tag );
				} else {
					$new_tag = preg_replace( '/<div/i', '<div style="' . $card_style_string . '"', $tag, 1 );
				}

				return $new_tag;
			},
			$html
		);

		// Add the avatar image style to each img tag inside the user div.
		if ( ! empty( $avatar_image_style ) ) {
			$avatar_image_style_string = implode( ' ', $avatar_image_style );
			$html                      = preg_replace_callback(
				'/(<img[^>]+class=["\']([^"\']*\b(?:avatar|photo|gravatar|bp-user-avatar|bp-avatar)\b[^"\']*)["\']([^>]*>))/i',
				function ( $m ) use ( $avatar_image_style_string ) {
					$tag = $m[1];
					// Match style attribute with either single or double quotes
					if ( preg_match( '/style=(["\'])(.*?)\1/i', $tag, $s_match ) ) {
						$quote    = $s_match[1];
						$existing = rtrim( trim( $s_match[2] ), ';' ) . '; ';
						$new_tag  = str_replace( $s_match[0], 'style=' . $quote . $existing . $avatar_image_style_string . $quote, $tag );
					} else {
						$new_tag = preg_replace( '/<img/i', '<img style="' . $avatar_image_style_string . '"', $tag, 1 );
					}

					return $new_tag;
				},
				$html
			);
		}

		return $html;
	}

	private function add_px_if_numeric( $value ) {
		return is_numeric( $value ) ? $value . 'px' : $value;
	}

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
