<div class="um-form">
  <form method="post" id="umStoryForm" enctype="multipart/form-data">
	<?php
		/**
		 * UM Story Form
		 *
		 * Title - 10
		 * Date - 20
		 * Thumb - 30
		 * Content - 40
		 */
		do_action( 'um_story_form_content', $id, $post );
	?>
	<div class="um-col-alt">
		<div class="um-left um-half">
			<input type="submit" id="um_story_submit" value="<?php echo um_story_get_label( 'submit_button', __( 'Submit', 'um-story-lite' ) ); ?>" name="" class="um-button">
		</div>
		<div class="um-right um-half">
			<a href="#" class="um-button um-alt"><?php echo um_story_get_label( 'cancel_button', __( 'Cancel', 'um-story-lite' ) ); ?></a>
		</div>
		<div class="um-story-clear"></div>
	</div>
	<div class="um-story-clear"></div>
	<input type="hidden" name="post_id" id="um_story_post_id" value="<?php echo absint( $id ); ?>" />
	<input type="hidden" name="action" value="um_story_save" />
	<?php wp_nonce_field( 'um_story_save', 'um_story_nonce' ); ?>
  </form>
</div>
