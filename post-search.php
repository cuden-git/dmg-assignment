<?php
/**
 * Plugin Name:       Post Search
 * Description:       Post search block and WP-CLI command to search posts by date.
 * Version:           0.1.0
 * Requires at least: 6.7
 * Requires PHP:      7.4
 * Author:            Denis Cummins
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       post-search
 *
 * @package CreateBlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
require_once __DIR__ . '/dmg-wp-cli.php';

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function create_block_post_search_block_init() {
	register_block_type( __DIR__ . '/build/post-search' );
}
add_action( 'init', 'create_block_post_search_block_init' );

add_action( 'rest_api_init', 'add_custom_fields' );
function add_custom_fields() {
	register_rest_field(
	'post', 
	'postURL', //New Field Name in JSON RESPONSEs
	array(
			'get_callback'    => 'get_custom_fields', // custom function name 
			'update_callback' => null,
			'schema'          => null,
			)
	);
}

function get_custom_fields( $object, $field_name, $request ) {
	if(isset($object['id'])) {
		return get_permalink($object['id']);
	}
}


