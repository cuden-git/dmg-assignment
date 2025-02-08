<?php
function dmg_validate_date_format($date)
{
  // Split the date into parts
  $parts = explode('/', $date);

  // Ensure there are exactly three parts (day, month, year)
  if (count($parts) !== 3) {
    return false;
  }

  list($day, $month, $year) = $parts;

  // Convert day and month to integers to remove leading zeros
  $day = (int) $day;
  $month = (int) $month;
  $year = (int) $year;

  // Create a DateTime object from the normalized values
  $d = DateTime::createFromFormat('j/n/Y', "$day/$month/$year");

  // Ensure the date is valid and matches the input after normalization
  return $d && $d->format('j/n/Y') === "$day/$month/$year";
}

function dmg_block_posts_query($current_page = 1, $before_date = null, $after_date = null)
{
  $arr = [];

  $args = [
    'post_type' => 'post',
    'post_status' => 'publish',
    'paged' => $current_page,
    'posts_per_page' => 100,
    'fields' => 'ids',
    's' => '<!-- wp:dmg/post-search',
    // 'date_query' => [
    //    [
    //     'after' => DateTime::createFromFormat('d/m/Y', $after_date)->format('Y-m-d'),//'February, 22nd 2010',//strtotime('23/02/2024'), //'February 22nd, 2010',//date("Y/n/j",strtotime('22/02/2010')),//'February 22nd, 2010'
    //     'before' => DateTime::createFromFormat('d/m/Y', $before_date)->format('Y-m-d'),//'February, 24th 2010',//strtotime('29/10/2024'), //'February 23rd, 2010',// date("Y/n/j",strtotime('23/10/2010')),//$after_date//strtotime('29/10/2024')
    //     'inclusive' => false
    //    ],
    //  ]
  ];
  if ($before_date) {
    $args['date_query']['before'] = DateTime::createFromFormat('d/m/Y', $before_date)->format('Y-m-d');
  }

  if ($after_date) {
    $args['date_query']['after'] = DateTime::createFromFormat('d/m/Y', $after_date)->format('Y-m-d');
  }

  if(empty($before_date) && empty($after_date)) {
    $args['date_query']['after'] = date('Y/m/d', strtotime('-30 days'));
    $args['date_query']['inclusive'] = false;
  }

//print_r($args); die();
  $results = new WP_Query($args);
  foreach ($results->posts as $id) {
    //   echo '<h4>' . $res->post_title . ' - ' . $res->post_date . '</h4>';
    //  echo '<h4>' . $id . '</h4>';
    array_push($arr, $id);
  }
  //print_r($results);
  // die();
  return $arr;
  //'<!-- wp:dmg/post-search'
}

add_action('cli_init', function ($args) {
  WP_CLI::add_command('dmg-find-posts', function ($args, $flags) {
    $flags = wp_parse_args(
      $flags,
      array(
        'date-before' => null,
        'date-after' => null,
      )
    );
    // WP_CLI::log( print_r($flags, true) );
    $date_before = $flags['date-before'];
    $date_after = $flags['date-after'];

    $fnc_params = [];
    $date_format_error = 'Incorrect date format. Date format should be entered as dd/mm/yyyy';

    if ($date_before && !dmg_validate_date_format($date_before)) {
      WP_CLI::error($date_format_error);

      return;  
    }
   
    if($date_after && !dmg_validate_date_format($date_after)) {
        WP_CLI::error($date_format_error);

      return;
    }

    $paged  = 1;

    do {
      $posts = dmg_block_posts_query($paged, $date_before, $date_after);

      if (empty($posts)) {
        WP_CLI::success('No posts found.');
        break;
      }
  
      WP_CLI::line(implode(' - ', $posts));

      if (count($posts) < 100) {//TODO: set per_page as global
        WP_CLI::success('No more posts to display');
        break;
      }

      // Prompt user to continue
      fwrite(STDOUT, "Press Enter to load more, or type 'q' to quit:");
      $input = trim(fgets(STDIN));

      // Stop if user types 'q'
      if (strtolower($input) === 'q') {
        WP_CLI::success('Exited.');
        break;
      }

      $paged++;
    } while (true);
  });
});

/** TODO:Remove for production */
add_filter('manage_post_posts_columns', function ($columns) {
  return array_merge($columns, ['id' => __('ID', 'dmg')]);
});
add_action('manage_post_posts_custom_column', function ($column_key, $post_id) {
  if ($column_key == 'id') {

    if ($post_id) {
      echo '<span>' .  __($post_id, 'dmg') . '</span>';
    }
  }
}, 10, 2);
