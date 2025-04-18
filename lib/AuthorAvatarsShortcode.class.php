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
	 *
	 * @return string
	 */
	public function shortcode_handler( $atts, $content = null ) {
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
				$blogs = array( -1 );
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
				$hiddenusers = array_unique ( explode( ',', esc_attr( $atts['hiddenusers'] ) ) );
			} else{
				$hiddenusers = array_map( 'esc_attr', $atts['hiddenusers'] );
			}
		}
		$this->userlist->hiddenusers = array_map( 'trim', $hiddenusers );

		// whitelist users
		$whitelistusers = array(); // default value: no restriction -> all users
		if ( ! empty( $atts['whitelistusers'] ) ) {
			if ( ! is_array( $atts['whitelistusers'] ) ) {
				$whitelistusers = array_unique ( explode( ',', esc_attr( $atts['whitelistusers'] ) ) );
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

		$display = AAFormHelper::get_display_list( $atts );

		// support for all style shortcode
		$default_display_options = array('show_name','show_postcount','show_email', 'show_nickname','show_biography','show_last_post','show_bbpress_post_count');
		// loop the old name=true settings and add them to the new array format
		foreach( $default_display_options as $default_display_option ){
			if ( isset( $atts[$default_display_option] ) && ($atts[$default_display_option] !== '') ) {
				if( $atts[$default_display_option] && ! in_array( $default_display_option, $display, true ) ){
					$display[] = $default_display_option;
				}
			}

		}
		// the defaults array and set the globals if found
		foreach( $default_display_options as $default_display_option ){
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
			$size = (int) $atts['avatar_size'];
			if ( $size > 0 ) {
				$this->userlist->avatar_size = $size;
			}
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
		$this->userlist->bio_length = -1;
		if ( ! empty( $atts['max_bio_length'] ) ) {
			$bio_length = (int) esc_attr( $atts['max_bio_length'] );
			if (  0 < $bio_length ) {
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
		} elseif ( isSet( $_REQUEST['aa_page'] ) && is_numeric( $_REQUEST['aa_page'] ) ) {
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
		$style = [];
		if ( ! empty( $atts['border_radius'] ) ) {
			$style[] = sprintf( "border-radius:%dpx;",  $atts['border_radius'] );
		}
		if ( ! empty( $atts['background_color'] ) ) {
			$style[] = sprintf( "background-color:%s;", sanitize_hex_color( $atts['background_color'] ) );
		}
		if ( ! empty( $atts['font_color'] ) ) {
			$style[] = sprintf( "color:%s;", sanitize_hex_color( $atts['font_color'] ) );
		}
		if ( ! empty( $atts['border_size'] ) ) {
			$style[] = sprintf( "border:solid transparent %dpx;", $atts['border_size'] );
		}
		if ( ! empty( $atts['border_color'] ) ) {
			$style[] = sprintf( "border-color:%s;", sanitize_hex_color( $atts['border_color'] ) );
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

		$return = '<div class="shortcode-author-avatars" style="'. implode(' ', $style ).'">' . $this->userlist->get_output() . $content . $this->userlist->pagingHTML . '</div>';
		if ( $switch_back_to_blog_id ) {
			switch_to_blog($switch_back_to_blog_id);
		}

		return $return;
	}
}
