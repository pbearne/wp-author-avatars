<?php
/**
 * wp-author-avatars.
 * User: Paul
 * Date: 2018-12-19
 *
 */

namespace author_avatar;


if ( ! defined( 'WPINC' ) ) {
	die;
}

class Show_Avatar_Block {

	function __construct() {

		add_action( 'init', array( $this, 'init' ) );
	}


	public function init(){

		register_block_type( 'author-avatars/show-avatar', array(
			'render_callback' =>  array( $this, 'callback' ),
		) );
	}


	public function callback( $attributes, $content ){

		require_once( dirname( dirname(  dirname( dirname( __FILE__ ) ) ) ). '/lib/AuthorAvatarsEditorButton.class.php' );
		$editor_button = new \AuthorAvatarsEditorButton();

		ob_start();

		$editor_button->render_tinymce_popup_body();

		$html = str_replace( 'body>' , 'div/>', ob_get_clean() );

		return $html;
	}
}

