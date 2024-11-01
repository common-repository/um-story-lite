<?php global $ultimatemember; ?>
<?php $query_posts = um_story_lite()->um_query()->make('post_type=' . um_story_post_type() . '&posts_per_page=9&offset=0&author=' . um_user('ID') ); ?>

<?php um_story_lite()->um_shortcodes()->loop = apply_filters('um_profile_query_make_posts', $query_posts ); ?>
<?php do_action( 'um_story_before_list' ); ?>
<div class="um-story-list" id="umDocsList">
<?php if ( um_story_lite()->um_shortcodes()->loop->have_posts()) { ?>

	<?php get_um_story_template( 'story-entry.php' ); ?>

	<div class="um-ajax-items">
		<div class="um-story-clear"></div>
		<!--Ajax output-->

		<?php if ( um_story_lite()->um_shortcodes()->loop->found_posts >= 9 ) { ?>

		<div class="um-load-items">
			<a href="#" class="um-ajax-paginate um-button" data-hook="um_load_stories" data-args="<?php echo um_story_post_type(); ?>,9,9,<?php echo um_user('ID'); ?>"><?php _e('load more stories','um-story-lite'); ?></a>
		</div>

		<?php } ?>

	</div>

<?php } else { ?>

	<div class="um-profile-note"><span><?php echo ( um_profile_id() == get_current_user_id() ) ? __('You have not created any stories.','um-story-lite') : __('This user has not added any stories.','um-story-lite' ); ?></span></div>

<?php } wp_reset_postdata(); ?>
</div>
