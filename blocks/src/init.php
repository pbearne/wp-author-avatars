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

require_once plugin_dir_path( __FILE__ ) . 'show-avatar/class-render.php';
new author_avatar\Show_Avatar\Render();



/**
 * Enqueue Gutenberg block assets for both frontend + backend.
 *
 * @uses {wp-editor} for WP editor styles.
 * @since 1.0.0
 */
function author_avatar_block_assets() { // phpcs:ignore
	if ( has_block( 'author-avatars/show-avatar' ) ) {
		// Styles.
		wp_enqueue_style(
			'author-avatars-style-css', // Handle.
			plugins_url('build/show-avatar/style-block.css', __DIR__ ), // Block style CSS.
			array('wp-editor') // Dependency to include the CSS after it.
		// filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.style.build.css' ) // Version: File modification time.
		);
	}

}

// Hook: Frontend assets.
add_action( 'enqueue_block_assets', 'author_avatar_block_assets' );

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
	// Scripts.
	wp_enqueue_script(
		'author-avatars-block-js', // Handle.
		plugins_url( 'build/show-avatar/block.js', __DIR__ ), // Block.build.js: We register the block here. Built with Webpack.
		array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-components', 'wp-block-editor', 'wp-data', 'wp-server-side-render' ), // Dependencies, defined above.
		// filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.build.js' ), // Version: File modification time.
		true // Enqueue the script in the footer.
	);

	wp_localize_script( 'author-avatars-block-js', 'authorAvatars', array(
		    'query_preview' => plugins_url( 'icon128x128.png', __FILE__ ),
            'wppic_preview' => plugins_url( 'icon128x128.png', __FILE__ ),
	));

	// Styles.
	wp_enqueue_style(
		'author-avatars-block-editor-css', // Handle.
		plugins_url( 'build/show-avatar/block.css', __DIR__ ), // Block editor CSS.
		array( 'wp-edit-blocks' ) // Dependency to include the CSS after it.
		// filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.editor.build.css' ) // Version: File modification time.
	);
}


// Hook: Editor assets.
add_action( 'enqueue_block_editor_assets', 'author_avatar_editor_assets' );
