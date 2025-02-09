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

declare(strict_types=1);

namespace Dmg\PostSearch;

use Dmg\PostSearch\CLICommand;


if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

/**
 * WP-CLI Custom Class
 */
require_once plugin_dir_path(__FILE__) . 'dmg-wp-cli.php';

/**
 * Create block category
 */
\add_filter('block_categories_all', function ($cats) {

	$cats[] = array(
		'slug'  => 'dmg-blocks',
		'title' => 'DMG Blocks'
	);

	return $cats;
});

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function dmg_post_search_block_init()
{
	\register_block_type(__DIR__ . '/build/post-search');
}
\add_action('init', __NAMESPACE__ . '\\dmg_post_search_block_init');

/**
 * Initialise WP-CLI command
 */
CLICommand::init();
