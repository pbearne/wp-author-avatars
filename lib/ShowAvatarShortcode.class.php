<?php

/**
 * Show Avatar Shortcode: provides a shortcode for displaying avatars for any email address/userid
 */
class ShowAvatarShortcode {
	var $userlist = null;

	/**
	 * Constructor
	 */
	function __construct() {
		$this->register();
	}

	/**
	 * register shortcode
	 */
	function register() {
		add_shortcode( 'show_avatar', array( $this, 'shortcode_handler' ) );
	}

	/**
	 * The shortcode handler for the [show_avatar] shortcode.
	 *
	 * Example: [show_avatar id=pbearne@tycoelectronics.com avatar_size=30 align=right]
	 */
	function shortcode_handler( $atts, $content = null ) {
		require_once( 'UserList.class.php' );
		$this->userlist = new UserList();
		$extraClass     = '';
		$hrefStart      = '';
		$name           = '';
		$bio            = '';
		$last_post      = '';
		$style          = '';
		$email          = '';
		$link           = '';
		$id             = ''; // get id or email

		$atts = array_map( 'aa_clean_commas', $atts );


		if ( ! empty( $atts['id'] ) ) {
			$id = esc_html( preg_replace( '[^\w\.@\-]', '', $atts['id'] ) );
		}
		if ( empty( $id ) && ! empty( $atts['email'] ) ) {
			$id = esc_html( preg_replace( '[^\w\.@\-]', '', $atts['email'] ) );
		}
		// get avatar size
		$bio_length = - 1;
		if ( ! empty( $atts['max_bio_length'] ) ) {
			$bio_length = (int) esc_attr( $atts['max_bio_length'] );
		}


		// get avatar size
		$avatar_size = false;
		if ( ! empty( $atts['avatar_size'] ) ) {
			$avatar_size = (int) esc_attr( $atts['avatar_size'] );
		}

		// get alignment
		$aligin_class = 'alignleft';
		if ( ! empty( $atts['align'] ) ) {
			switch ( $atts['align'] ) {
				case 'left':
//					$style = "float: left; margin-right: 10px; width: auto; text-align: center;";
					$aligin_class = 'alignleft';
					break;
				case 'right':
//					$style = "float: right; margin-left: 10px; width: auto; text-align: center;";
					$aligin_class = 'alignright';
					break;
				case 'center':
					$style = "text-align: center; width: 100%;";
					$aligin_class = 'aligncenter';
					break;
			}
		}
		$extraClass .= ' ' .  $aligin_class;

		if ( ! empty( $id ) ) {
			$avatar = get_avatar( $id, $avatar_size );
		} else {
			$avatar = __( "[show_author shortcode: please set id/email attribute]" );
		}
		// is there an user link request

		if ( ! stripos( $avatar, 'style=' ) ) {
			$avatar_style = '';
			if ( ! empty( $atts['border_radius'] ) ) {
				$avatar_style .= ' border-radius:' . absint( $atts['border_radius'] ) . '%;';
			}
			/**
			 * filter the avatar alt
			 *
			 * @param string $alt users nicename.
			 * @param object $user The user object
			 */
			$avatar_style = apply_filters( 'aa_user_avatar_style', $avatar_style, $atts );

			$avatar = preg_replace( '@ ?\/>@', ' style="' . $avatar_style . '"  />', $avatar );
		}

		if ( apply_filters( 'aa_user_avatar_lazy_load', true, $avatar, $id ) ) {
			// add the lazy loading tag
			$avatar = preg_replace( '@ ?/>@', ' loading="lazy" />', $avatar );
		}

		if ( ! empty( $atts['user_link'] )
		     || ! empty( $atts['show_biography'] )
		     || ! empty( $atts['show_postcount'] )
		     || ! empty( $atts['show_name'] )
		     || ! empty( $atts['show_email'] )
		     || ! empty( $atts['show_nickname'] )
		) {

			// try to fetch user profile
			$isUser = true;

			if ( ! is_numeric( $id ) ) {
				if ( email_exists( $id ) ) {
					$id = email_exists( $id );

				} else {
					$isUser = false;
				}
			}


			if ( $isUser ) {
				$all_meta_for_user = get_user_meta( $id );
				if ( count( $all_meta_for_user ) == 0 ) {
					$isUser = false;
				}
			}


			if ( $isUser ) {
				if ( ! empty( $atts['user_link'] ) ) {
					switch ( $atts['user_link'] ) {
						case 'authorpage':
							$link = get_author_posts_url( $id );
							break;
						case 'website':
							$link = get_the_author_meta( 'user_url', $id );
							if ( empty( $link ) || 'http://' === $link ) {
								$link = false;
							}
							break;
						case 'blog':
							if ( AA_is_wpmu() ) {
								$blog = get_active_blog_for_user( $id );
								if ( ! empty( $blog->siteurl ) ) {
									$link = $blog->siteurl;
								}
							}
							break;
						case 'bp_memberpage':
							if ( function_exists( 'bp_core_get_user_domain' ) ) {
								$link = bp_core_get_user_domain( $id );
							} elseif ( function_exists( 'bp_core_get_userurl' ) ) { // BP versions < 1.1
								$link = bp_core_get_userurl( $id );
							}
							break;
						case 'um_profile':
							if ( function_exists( 'um_user_profile_url' ) ) {
								um_fetch_user( $id );
								$link = um_user_profile_url();
								um_reset_user();
							}
							if ( empty( $link ) || 'http://' === $link ) {
								$link = false;
							}
							break;
						case 'bbpress_memberpage':
							if ( function_exists( 'bbp_get_user_profile_url' ) ) {
								$link = bbp_get_user_profile_url( $id );
							}
							if ( empty( $link ) || 'http://' === $link ) {
								$link = false;
							}
							break;
						case 'last_post':
							$recent = get_posts( array(
								'author'      => $id,
								'orderby'     => 'date',
								'order'       => 'desc',
								'numberposts' => 1,
							) );
							$link   = get_permalink( $recent[0]->ID );
							break;

						case 'last_post_filtered':
							$recent = get_posts( array(
								'author'      => $id,
								'orderby'     => 'date',
								'order'       => 'desc',
								'numberposts' => 1,
							) );
							$link   = get_permalink( $recent[0]->ID );
							break;

						case 'last_post_all':
							$last_post = get_most_recent_post_of_user( $id );
							$link      = get_permalink( $last_post['post_id'] );
							break;
					}
					if ( $link ) {
						$hrefStart = '<a href="' . $link . '">';
					}

					$extraClass .= ' user-' . $id;
				}


				$display= AAFormHelper::get_display_list( $atts );

				// support for all style shortcode
				$default_display_options = array(
					'show_name',
					'show_postcount',
					'show_email',
					'show_nickname',
					'show_biography',
					'show_last_post',
					'show_bbpress_post_count'
				);
				// loop the old name=true settings and add them to the new array format
				foreach ( $default_display_options as $default_display_option ) {
					if ( isset( $atts[ $default_display_option ] ) && ( strlen( $atts[ $default_display_option ] ) > 0 ) ) {
						if ( true == $atts[ $default_display_option ] && ! in_array( $default_display_option, $display ) ) {
							$display[] = $default_display_option;
						}
					}

				}

				foreach ( $display as $show ) {
					switch ( $show ) {
						case 'show_name';
							$name       .= '<br />' . get_the_author_meta( 'display_name', $id );
							$extraClass .= ' with-name';
							break;

						case 'show_nickname':
							$name       = '<br />' . get_the_author_meta( 'nickname', $id );
							$extraClass .= ' with-nickname';
							break;

						case 'show_email':
							$userEmail = get_the_author_meta( 'user_email', $id );
							$email     = "<div class='email'><a href='mailto:" . $userEmail . "''>" . $userEmail . "</a></div>";
							if ( empty( $email ) ) {
								$extraClass .= 'email-missing';
							} else {
								$extraClass .= ' with-email';
							}
							break;

						case 'show_postcount':
							$name .= ' (' . $postcount = $this->userlist->get_user_postcount( $id ) . ')';

							break;

						case 'show_bbpress_post_count':
							if ( function_exists( 'bbp_get_user_topic_count_raw' ) ) {
								$BBPRESS_postcount = bbp_get_user_topic_count_raw( $id ) + bbp_get_user_reply_count_raw( $id );
								$name              .= ' (' . $postcount = $BBPRESS_postcount . ')';
							}
							break;

						case 'show_biography':

							$biography = get_the_author_meta( 'description', $id );

							if ( 0 < $bio_length ) {
								$biography = $this->userlist->truncate_html( wpautop( $biography, true ), apply_filters( 'aa_user_bio_length', $bio_length ) );
							} else {
								$biography = wpautop( $biography, true );
							}

							$max_bio_length = (int) ( isset( $atts['max_bio_length'] ) ) ? esc_attr( $atts['max_bio_length'] ) : 0;
							$bio            = '<div class="bio bio-length-' . $max_bio_length . '">' . $biography . '</div>';

							break;

						case    'show_last_post':

							$last_post = '<div class="last_post">' . $this->userlist->aa_get_last_post( $id ) . '</div>';
							if ( empty( $last_post ) ) {
								$extraClass .= ' last-post-missing';
							} else {
								$extraClass .= ' with-last-post';
							}
							break;
					}
				}
			}

			if ( empty( $bio ) ) {
				$extraClass .= ' biography-missing';
			} else {
				$extraClass .= ' with-biography bio-length-' . $bio_length;
			}

		}
		$hrefend = '';
		if ( ! empty( $hrefStart ) ) {
			$hrefend = '</a>';
		}

		if ( ! empty( $style ) ) {
			$style .= $style;
		}
		if ( ! empty( $atts['background_color'] ) ) {
			$style .= ' background-color:' . sanitize_hex_color( $atts['background_color'] ) . ';';
		}

		if ( ! empty( $atts['font_color'] ) ) {
			$style .= ' color:' . sanitize_hex_color( $atts['font_color'] ) . ';';
			$hrefStart =  preg_replace( '@<a @', '<a style="color:' . sanitize_hex_color( $atts['font_color'] )  . ';"', $hrefStart );
			$last_post =  preg_replace( '@<a @', '<a style="color:' . sanitize_hex_color( $atts['font_color'] )  . ';"', $last_post );
			$email =  preg_replace( '@<a @', '<a style="color:' . sanitize_hex_color( $atts['font_color'] )  . ';"', $email );
		}


		return '<div class="shortcode-show-avatar ' . $extraClass . '"style="' . $style . '" >' . $hrefStart . $avatar . $name . $last_post . $hrefend . $bio . $email . '</div>' . $content;
	}



}