<?php get_header(); ?>

	<section id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php if ( have_posts() ) : ?>

			<header class="page-header">
        <?php
          echo "<h1 class=\"page-title\">Snowfall Records</h1>";
					the_archive_description( '<div class="taxonomy-description">', '</div>' );
				?>
			</header>

      <?php 
        $citiesByState = [];

        while ( have_posts() ) : the_post(); 
          $city = get_post_meta(get_the_ID());
          $citiesByState[$city['STATE'][0]][] = [
            'title' => get_the_title(),
            'city' => $city['City'][0],
            'permalink' => get_the_permalink(),
          ];
        endwhile; 

        $citiesByState = array_reverse($citiesByState);

        echo '<ul class="states-list">';
          foreach(array_keys($citiesByState) as $state) {
            echo '<li><a href="#'. $state .'">' . $state . '</a></li>';
          }
        echo '</ul>';

        echo '<div class="states-posts">';
        foreach($citiesByState as $state => $cities) {
          echo '<div id="' . $state . '">';
          echo "<h3>$state</h3>";
          echo '<ul class="post-list">';
          foreach($cities as $city) {
            echo '<li><a href="#"></a>' . $city['city'] . '</li>';
          }
          echo "</ul>";
          echo '</div>';
        }
        echo '</div>';
      
        the_posts_pagination(
          array(
            'prev_text'          => __( 'Previous page', 'snowfall-records' ),
            'next_text'          => __( 'Next page', 'snowfall-records' ),
            'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', 'snowfall-records' ) . ' </span>',
          )
        );
      else :
        echo 'No snowfall records found.';
      endif;
		?>

		</main><!-- .site-main -->
	</section><!-- .content-area -->

<?php get_footer(); ?>
