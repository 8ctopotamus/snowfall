<?php get_header(); ?>

	<section id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php if ( have_posts() ) : ?>

			<header class="page-header">
				<?php
					the_archive_title( '<h1 class="page-title">', '</h1>' );
					the_archive_description( '<div class="taxonomy-description">', '</div>' );
				?>
			</header>

      <div class="snowfall-cities-grid">
			<?php
        while ( have_posts() ) :
          the_post(); ?>
            <a href="<?php the_permalink(); ?>" class="snowfall-city-link">
              <img src="<?php echo plugin_dir_url( __DIR__ ) . 'img/snowfall-record-placeholder.jpg'; ?>" alt="<?php the_title(); ?>" />
              <h3><?php the_title(); ?></h3>
            </a>
        <?php endwhile;
      echo '</div>'; // /.snowfall-cities-grid

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
