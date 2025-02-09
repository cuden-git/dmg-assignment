<?php

declare(strict_types=1);

namespace Dmg\PostSearch;

/**
 * Registers the WP-CLI command for post search by dates
 */
class CLICommand
{
  private $perPage = 100;

  public static function init()
  {
    $self = new self();
    add_action('cli_init', [$self, 'cliCommand']);
  }

  /**
   * @param string $date
   * @return boolean
   */
  public function validateFormat($date): bool
  {
    $parts = explode('/', $date);

    if (count($parts) !== 3) {
      return false;
    }

    list($day, $month, $year) = $parts;

    $day = (int) $day;
    $month = (int) $month;
    $year = (int) $year;

    $d = \DateTime::createFromFormat('j/n/Y', "$day/$month/$year");

    return $d && $d->format('j/n/Y') === "$day/$month/$year";
  }

  /**
   * @param int $currentPage
   * @param string $beforeDate
   * @param string $afterDate
   * @return array
   */
  public function postsQuery($currentPage = 1, $beforeDate = null, $afterDate = null): array
  {
    $arr = [];

    $args = [
      'post_type' => 'post',
      'post_status' => 'publish',
      'paged' => $currentPage,
      'posts_per_page' => $this->perPage,
      'fields' => 'ids',
      's' => '<!-- wp:dmg/post-search',
    ];

    if ($beforeDate) {
      $args['date_query']['before'] = \DateTime::createFromFormat('d/m/Y', $beforeDate)->format('Y-m-d');
    }

    if ($afterDate) {
      $args['date_query']['after'] = \DateTime::createFromFormat('d/m/Y', $afterDate)->format('Y-m-d');
    }

    if (empty($beforeDate) && empty($afterDate)) {
      $args['date_query']['after'] = date('Y/m/d', strtotime('-30 days'));
      $args['date_query']['inclusive'] = false;
    }

    $results = new \WP_Query($args);

    foreach ($results->posts as $id) {
      array_push($arr, $id);
    }

    return $arr;
  }

  /**
   * @param array $args
   * @param array $flags
   * @return mixed
   */
  public function cliCommand($args): void
  {
    \WP_CLI::add_command('dmg-find-posts', function ($args, $flags) {
      $flags = wp_parse_args(
        $flags,
        array(
          'date-before' => null,
          'date-after' => null,
        )
      );

      $dateBefore = $flags['date-before'];
      $dateAfter = $flags['date-after'];

      $fncParams = [];
      $dateFormatError = 'Incorrect date format. Date format should be entered as dd/mm/yyyy';

      if ($dateBefore && !$this->validateFormat($dateBefore)) {
        \WP_CLI::error($dateFormatError);

        return;
      }

      if ($dateAfter && !$this->validateFormat($dateAfter)) {
        \WP_CLI::error($dateFormatError);

        return;
      }

      $paged  = 1;

      do {
        $posts = $this->postsQuery($paged, $dateBefore, $dateAfter);

        if (empty($posts)) {
          \WP_CLI::success(__('No posts found.'));
          break;
        }

        \WP_CLI::line(implode(' - ', $posts));

        if (count($posts) < $this->perPage) {
          \WP_CLI::success(__('No more posts to display'));
          break;
        }

        fwrite(STDOUT, __('Press Enter to load more, or type \'q\' to quit:'));
        $input = trim(fgets(STDIN));

        // Stop if user types 'q'
        if (strtolower($input) === 'q') {
          \WP_CLI::success(__('Exited.'));
          break;
        }

        $paged++;
      } while (true);
    });
  }
}
