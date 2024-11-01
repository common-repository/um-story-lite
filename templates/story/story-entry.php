<?php while ( um_story_lite()->um_shortcodes()->loop->have_posts() ) {
	um_story_lite()->um_shortcodes()->loop->the_post(); 
	$post_id      = get_the_ID();
	?>
	<div class="um-story-entry">
		<?php
		$image_attributes = wp_get_attachment_image_src( get_post_thumbnail_id(), 'thumbnail' );
		if ( $image_attributes ) {
		?>
		<div class="um-story-thumbnail"><img src="<?php echo esc_attr( $image_attributes[0] ); ?>" /></div>
		<?php
		}
		?>
		<div class="um-story-content"><a href="<?php echo um_get_story_link( get_the_ID() ); ?>"><?php the_title(); ?></a>
			<div class="um-story-details">
				<?php um_story_delete_link( get_the_ID(), get_the_author_meta('ID') ); ?> <?php um_story_edit_link( get_the_ID(), get_the_author_meta('ID') ); ?>
			</div>
		</div>
		<?php um_story_date_block(); ?>
		<div class="um-story-clear"></div>
	</div>
	
<?php } ?>

<?php if ( isset( um_story_lite()->um_shortcodes()->modified_args) && um_story_lite()->um_shortcodes()->loop->have_posts() && um_story_lite()->um_shortcodes()->loop->found_posts >= 10 ) { ?>

	<div class="um-load-items">
		<a href="#" class="um-ajax-paginate um-button" data-hook="um_load_stories" data-args="<?php echo um_story_lite()->um_shortcodes()->modified_args; ?>"><?php _e( 'load more stories','um-story-lite' ); ?></a>
	</div>
	
<?php } ?>