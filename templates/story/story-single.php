<style type="text/css">
	.um-story-image{
		display: block;
		background-repeat:no-repeat;
		background-size:cover;
		height: 300px;
		width:100%;
		position:relative;
		<?php if ( $image ) : ?>
	  	background: 
			linear-gradient(
			  to bottom,
			  rgba(0, 0, 0, 0),
			  rgba(0, 0, 0, 0.4)
			),url('<?php echo $image; ?>');
		<?php endif; ?>
    	-webkit-background-size: cover;
		background-repeat: no-repeat;
		background-size: cover;
		background-position: center center;
		z-index: 10;
	}
	.um-story-image .um-story-title {
		position: absolute;
		z-index: 30;
		bottom: 12px;
		left: 30px;
		color: #fff;
	}
	.um-story-image .um-story-title h2 {
		text-shadow: 0px 0px 35px gray;
		color: #fff;
		font-size: 18px;
	}
</style>
<div class="um-story-single-container">
	<div class="um-story-header">
    	<?php if ( $image ) : ?>
        <div class="um-story-image">
        	<div class="um-story-title">
				<h2><?php echo wp_kses_post( $post->post_title ); ?></h2>
			</div>
        </div>
        <?php else: ?>
        <div class="um-story-title">
				<h2><?php echo wp_kses_post( $post->post_title ); ?></h2>
		</div>
        <?php endif; ?>
    </div>
    <div class="um-story-content">
    	<?php echo apply_filters( 'the_content', wp_kses_post( $post->post_content ) ); ?>
    </div>
</div>