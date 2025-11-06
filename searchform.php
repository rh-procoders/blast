<form role="search" method="get" id="searchform"
	class="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<div class="searchform__item">
		<label class="screen-reader-text" for="s"><?php _x( 'Search for:', 'label' ); ?></label>
		<input class="input-item" type="text" value="<?php echo get_search_query(); ?>" placeholder="Search" name="s" id="s" />
		<button type="submit" id="searchsubmit" class="btn btn--icon">
			<span class="btn__icon">
				<?php sprite_svg('icon-search', 14, 14) ?>
			</span>
		</button>
	</div>
</form>