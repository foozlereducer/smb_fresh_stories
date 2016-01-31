<?php
// Creating the widget
class smb_Widget_Fresh_Stories extends WP_Widget {

	function __construct() {
		
		parent::__construct( 
				'smb-widget-fresh-stories' // id 
				, $name = __('SMB Fresh Stories', 'SMB Fresh Stories' ) 
				, array( 'description' => 'Adds fresh posts to a dynamic widget area') // Arguments
		);
		
	}

	// Creating widget front-end
	// This is where the action happens
	public function widget( $args, $instance ) {


	/**********************
	display_fresh_posts
	***********************/
		if ( isset( $args['before_widget'] ) ) {
			echo $args['before_widget'];	
		}
		
		global $post;
		
		// Start of the mini-loop, specifies which category to look in and how many posts to pull//
		$fresh_post_args = array (
			'showposts' => $instance['number_of_posts']
		);

		$query = $this->query_posts( $fresh_post_args, true, array( $this, 'days_where' ) );
        ?>
        <div class="row row--padding-wide fresh-stories">
            <div class="row container-wide">
        <?php
		if ( $query->found_posts > $instance['number_of_posts'] ) {
			
			while ( $query->have_posts() ) : $query->the_post();
			 	$do_not_duplicate = $post->ID;
			 	$content = get_the_content();
				?>
				<!-- freshest stories -->
			
						<div class="col-wide <?php echo esc_attr( $instance['outer_container_classes'] ); ?>">'
								<h2 class="fresh-stories__heading">
									<a href="<?php the_permalink(); ?>"><?php esc_attr( get_the_title() ); ?></a>
								</h2>
								<p>
									<a href="<?php the_permalink(); ?>"><img src="<?php bloginfo('template_url') ?>/img/x.jpg" /></a>
									<span>
										<?php 
											
											$excerpt = $this->generate_auto_excerpt( get_the_post(), $instance['excerpt_num_of_words'] );
											
											if ( empty( $excerpt ) ) {
												'no exerpt available';
											} else {
												echo( esc_attr( $excerpt ) );
											}
										?>
										<a href="<?php get_permalink(); ?>"> more </a>
									</span>
									<span class="fresh-stories_meta">
										Posted on <?php esc_attr( the_time( 'F jS, Y' ) ) ?> at 
										<?php esc_attr( the_time( 'g:i a' ) ) ?> by 
										<?php esc_attr( the_author_posts_link() ) ?>
									</span>
								</p>
							</div>
						<?php
					endwhile;

					wp_reset_postdata();

					} else {
						$fresh_post_args = array (
							'showposts' => $instance['number_of_posts'],
							'orderby' => 'rand'
						);

						$query = $this->query_posts( $fresh_post_args );

						while ( $query->have_posts() ) : $query->the_post();
						 	$do_not_duplicate = $post->ID;
						 	$excerpt = $this->generate_auto_excerpt( get_the_content(), $instance['excerpt_num_of_words'] );
							
                            if ( ! empty( $instance['outer_container_classes'] ) ) {
                                echo  '<div class="col-wide ' . esc_attr( $instance['outer_container_classes'] ) . '" >'; 
                            } else if ( 1 == $instance['number_of_posts'] ) {
                            ?>
                                <div class="col-wide fresh-stories__story_one">
                            <?php 
                            } else if( 2 == $instance['number_of_posts'] ) {
                            ?>
                                <div class="col-wide fresh-stories__story_two">
                            <?php
                            } else if( 1 == $instance['number_of_posts'] ) {
                            ?>
                                 <div class="col-wide fresh-stories__story">
                            <?php
                            } 
                            ?>
									<h2 class="fresh-stories__heading">
										<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
									</h2>
									<p>
										<a href="<?php the_permalink(); ?>"><img src="<?php the_post_thumbnail( 'thumbnail' ) ?>" /></a>
										<span>
										<?php 	
											if ( empty( $excerpt ) ) {
												'no exerpt available';
											} else {
												echo( $excerpt );
											}
										?>
										<a href="<?php the_permalink(); ?>">more ...</a>
										</span>
									</p>
									<p>
										<span class="fresh-stories_meta">
											Posted on <?php esc_attr( the_time( 'F jS, Y' ) ) ?> at 
											<?php esc_attr( the_time( 'g:i a' ) ) ?> by 
											<?php esc_attr( the_author_posts_link() ) ?>
										</span>
									</p>
								</div>
							<?php
						endwhile;

						wp_reset_postdata();
			?>
				</div>
            </div>
			<?php

			if ( isset( $args['after_widget'] ) ) {
				echo $args['after_widget'];
			}
		}
	}

	// Widget Backend
	public function form( $instance ) {
	?>
		<h3>Number of Posts</h3>
		<input 
			type='text' 
			class='widefat smb-number-of-posts' 
			id='<?php echo esc_attr( $this->get_field_id('smb-number-of-posts') )?>' 
			name='<?php  echo esc_attr( $this->get_field_name('number_of_posts') ); ?>'  
			value='<?php echo esc_attr( $instance['number_of_posts'] )?>' 
			placeholder='3'
		/>
		<h3>Number of Excerpt Words</h3>
		<input 
			type='text' 
			class='widefat smb-excerpt-num-of-words' 
			id='<?php echo esc_attr( $this->get_field_id('smb-excerpt_num_of_words') )?>' 
			name='<?php  echo esc_attr( $this->get_field_name('excerpt_num_of_words') ); ?>'  
			value='<?php echo esc_attr( $instance['excerpt_num_of_words'] )?>' 
			placeholder='200'
		/>
        <h3>Outer Container Classes </h3>
        <input 
            type='text' 
            class='widefat smb-outer-container-classes' 
            id='<?php echo esc_attr( $this->get_field_id('smb-outer-container-classes') )?>' 
            name='<?php  echo esc_attr( $this->get_field_name('outer_container_classes') ); ?>'  
            value='<?php echo esc_attr( $instance['outer_container_classes'] )?>' 
            placeholder='( optional ) my-class another-class'
        />
	<?php
	}

	// update
	function update( $new_instance, $a_existing ) {

		$instance =  $a_existing;

		( isset( $new_instance['number_of_posts'] ) 
            ? $instance['number_of_posts'] = (int) $new_instance['number_of_posts'] 
            : $instance['number_of_posts'] = 3
        );

        ( isset( $new_instance['excerpt_num_of_words'] ) 
            ? $instance['excerpt_num_of_words'] = (int) $new_instance['excerpt_num_of_words']
            : $instance['excerpt_num_of_words'] = 9 
        );

        ( ! empty( $new_instance['outer_container_classes'] )
            ? $instance['outer_container_classes' ] = sanitize_text_field( $new_instance['outer_container_classes'] )
            : $instance['outer_container_classes'] = 'fresh-stories__story' 
        );
        

     return $instance;
	}

/**********************
	post query
	***********************/
	function query_posts( $args, $is_where = false, $where_filter_name = '' ) {

		if ( empty( $is_where ) ) {
			return new WP_Query( $args );
		} else {
			add_filter( 'posts_where', $where_filter_name, 10, 3 );
			$query = new WP_Query( $args );
			remove_filter( 'posts_where',  $where_filter_name );
			return $query;
		}
	}

	/*
	* where filters for post query
	*/
	function days_where( $where = '' ) {
	    // posts in the last 10 days
	    $where .= " AND post_date > '" . date( 'Y-m-d', strtotime( '-7 days' ) ) . "'";
	    return $where;
	}

	/**********************
	generate auto excerpt
	***********************/
	function generate_auto_excerpt( $content, $num_of_words ) {
		
		$words = explode( ' ', $content );
		$excerpt = '';

		for ( $i = 0; $i <= $num_of_words; $i++ ) {
			$excerpt .= $words[ $i ] . " ";
		}

		return $excerpt;
	}
}

