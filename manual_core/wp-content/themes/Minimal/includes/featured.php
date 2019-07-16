<!-- Start Featured -->
	<div id="featured" class="clearfix">
		<a href="#" id="left_arrow"><?php esc_html_e('Previous','Minimal') ?></a>
		<a href="#" id="right_arrow"><?php esc_html_e('Next','Minimal') ?></a>

	<!-- Featured Content -->
		<div id="featured_content">
			<!-- Featured Articles -->
			<?php
			$arr = array();
			$i=1;

			$width = 406;
			$height = 226;

			$featured_cat = get_option('minimal_feat_cat');
			$featured_num = (int) get_option('minimal_featured_num');

			if (get_option('minimal_use_pages') == 'false') query_posts("posts_per_page=$featured_num&cat=".get_catId($featured_cat));
			else {
				global $pages_number;

				if (get_option('minimal_feat_pages') <> '') $featured_num = count(get_option('minimal_feat_pages'));
				else $featured_num = $pages_number;

				$et_featured_pages_args = array(
					'post_type' => 'page',
					'orderby' => 'menu_order',
					'order' => 'ASC',
					'posts_per_page' => (int) $featured_num,
				);

				if ( is_array( et_get_option( 'minimal_feat_pages', '', 'page' ) ) )
					$et_featured_pages_args['post__in'] = (array) array_map( 'intval', et_get_option( 'minimal_feat_pages', '', 'page' ) );

				query_posts( $et_featured_pages_args );
			};

			while (have_posts()) : the_post(); ?>

				<div class="slide">
					<a href="<?php the_permalink();?>">
						<?php
						$post_title = get_the_title();

						$thumbnail = get_thumbnail($width,$height,'thumb',$post_title,$post_title);
						$thumb = $thumbnail["thumb"];

						print_thumbnail($thumb, $thumbnail["use_timthumb"], $post_title, $width, $height, 'thumb'); ?>
					</a>
					<div class="description">
						<h2><a href="<?php the_permalink();?>"><?php truncate_title(27); ?></a></h2>

						<?php $tagline = get_post_meta(get_the_ID(), 'Tagline', true);
						if ($tagline != '' ) { ?>
							<p class="tagline">&#147;<?php echo wp_kses( $tagline, array( 'span' => array() ) ); ?>&#148;</p>
						<?php } ?>

						<p><?php truncate_post(375);?></p>
						<a href="<?php the_permalink();?>" class="readmore"><span><?php esc_html_e('Read More', 'Minimal');?></span></a>
					</div> <!-- end .description -->
				</div> <!-- end .slide -->

		<?php endwhile; wp_reset_query(); ?>

		</div> <!-- end #featured_content -->
		<!-- End Featured Articles -->
		<!-- Featured Menu -->
		<div id="controllers" class="clearfix"></div>

		<!-- End Featured Menu -->
	</div> <!-- end #featured -->
	<!-- End Featured Content -->

<!-- End Featured -->