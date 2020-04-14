<?php

namespace author_avatar\blocks_for_gutenberg;

// Abort if this file is called directly.
use AuthorAvatarsForm;

if ( ! defined( 'WPINC' ) ) {
	die;
}

class api {

	private $version;
	private $namespace;
	protected $form;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		include_once 'AuthorAvatarsForm.class.php';
		$this->form = new AuthorAvatarsForm();

		$this->version   = '1';
		$this->namespace = 'author_avatar/blocks/v' . $this->version;

		$this->run();
	}

	/**
	 * Run all of the plugin functions.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		add_action( 'rest_api_init', array( $this, 'user_roles' ) );
	}

	/**
	 * Register REST API
	 */
	public function user_roles() {

		register_rest_route(
			$this->namespace,
			'/data',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_data' ),
				'permission_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);

	}

	/**
	 * Get the user roles
	 *
	 * @return array $roles JSON feed of returned objects
	 */
	public function get_user_roles() {
		global $wp_roles;

		$roles      = array();
		$user_roles = $wp_roles->roles;

		foreach ( $user_roles as $key => $role ) {
			$roles[] = array(
				'value' => $key,
				'label' => $role['name'],
			);
		}

		return $roles;
	}

	/**
	 * Get the user roles
	 *
	 * @return array $roles JSON feed of returned objects
	 */
	public function get_users() {

		$users = get_users();

		$return = [
			[
				'label' => 'Custom id or Email',
				'value' => - 1
			],
			[
				'label' => 'Select by role(s)',
				'value' => 0
			],
		];
		foreach ( $users as $key => $user ) {

			$return[] = array(
				'value' => $user->ID,
				'label' => $user->user_nicename,
			);
		}

		return $return;
	}

	/**
	 *
	 *
	 * @return array of display options
	 */
	public function get_display_options() {
		$return = array();

		foreach ( $this->form->display_options() as $name => $label ) {

			$return[] = array(
				'label' => $label,
				'value' => $name
			);
		}

		return $return;

	}

	/**
	 *
	 *
	 * @return array of user links  options
	 */
	public function get_user_links() {
		$return = array();

		foreach ( $this->form->get_user_links() as $name => $label ) {

			$return[] = array(
				'label' => $label,
				'value' => $name
			);
		}

		return $return;

	}

	/**
	 *
	 *
	 * @return array of sort by options
	 */
	public function get_sort_by() {
		$return = array();

		foreach ( $this->form->get_sort_by() as $name => $label ) {

			$return[] = array(
				'label' => $label,
				'value' => $name
			);
		}

		return $return;

	}


	/**
	 *
	 *
	 * @return array list of blogs options
	 */
	private function get_blogs() {
		$return = array();

		foreach ( $this->form->_getAllBlogs() as $name => $label ) {

			$return[] = array(
				'label' => $label,
				'value' => $name
			);
		}

		return $return;
	}

	/**
	 * creates a singke return object used by the Block to display option in sidebar
	 *
	 * @return array $data
	 */
	public function get_data() {
		$tran_key = 'AA_gutenberg_data';
		$data = get_transient( $tran_key );
		if( false === $data ){
			$data = array(
				'users'           => $this->get_users(),
				'display_options' => $this->get_display_options(),
				'roles'           => $this->get_user_roles(),
				'links'           => $this->get_user_links(),
				'sort_avatars_by' => $this->get_sort_by(),
				'donate'          => AA_donateButton('link'),

			);

			$data['blogs'] = array();
			if ( AA_is_wpmu() ) {
				$data['blogs'] = $this->get_blogs();
			}
			set_transient( $tran_key, $data, HOUR_IN_SECONDS );
		}

		return $data;
	}


}

new api();
