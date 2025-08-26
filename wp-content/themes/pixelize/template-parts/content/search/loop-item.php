<div class="<?php pixelize_print_archive_entry_class('wp-block wp-block-kubio-query-loop-item  position-relative wp-block-kubio-query-loop-item__container pixelize-search__k__QtetVXHJ9I-container pixelize-local-599-container d-flex   '); ?>"" data-kubio="kubio/query-loop-item">
	<div class="position-relative wp-block-kubio-query-loop-item__inner pixelize-search__k__QtetVXHJ9I-inner pixelize-local-599-inner d-flex h-flex-basis h-px-lg-3 v-inner-lg-3 h-px-md-3 v-inner-md-3 h-px-3 v-inner-3">
		<div class="position-relative wp-block-kubio-query-loop-item__align pixelize-search__k__QtetVXHJ9I-align pixelize-local-599-align h-y-container h-column__content h-column__v-align flex-basis-100 align-self-lg-start align-self-md-start align-self-start">
			<figure class="wp-block wp-block-kubio-post-featured-image  position-relative wp-block-kubio-post-featured-image__container pixelize-search__k__iE82N7AEu-container pixelize-local-600-container kubio-post-featured-image--has-image h-aspect-ratio--16-9 <?php pixelize_post_missing_featured_image_class(); ?>" data-kubio="kubio/post-featured-image" data-kubio-settings="{{kubio_settings_value}}">
				<?php if(has_post_thumbnail()): ?>
				<img class='position-relative wp-block-kubio-post-featured-image__image pixelize-search__k__iE82N7AEu-image pixelize-local--image' src='<?php echo esc_url(get_the_post_thumbnail_url());?>' />
				<?php endif; ?>
				<div class="position-relative wp-block-kubio-post-featured-image__inner pixelize-search__k__iE82N7AEu-inner pixelize-local-600-inner">
					<div class="position-relative wp-block-kubio-post-featured-image__align pixelize-search__k__iE82N7AEu-align pixelize-local-600-align h-y-container align-self-lg-center align-self-md-center align-self-center"></div>
				</div>
			</figure>
			<a class="position-relative wp-block-kubio-post-title__link pixelize-search__k__tstzQ_uACq-link pixelize-local-601-link d-block" href="<?php echo esc_url(get_the_permalink()); ?>">
				<h4 class="wp-block wp-block-kubio-post-title  position-relative wp-block-kubio-post-title__container pixelize-search__k__tstzQ_uACq-container pixelize-local-601-container" data-kubio="kubio/post-title">
					<?php the_title(); ?>
				</h4>
			</a>
			<p class="wp-block wp-block-kubio-post-excerpt  position-relative wp-block-kubio-post-excerpt__text pixelize-search__k__-hWWlFyCEF-text pixelize-local-602-text" data-kubio="kubio/post-excerpt">
				<?php pixelize_post_excerpt(array (
  'max_length' => 16,
)); ?>
			</p>
			<div class="wp-block wp-block-kubio-post-meta  position-relative wp-block-kubio-post-meta__metaDataContainer pixelize-search__k__in2mlwF4a-metaDataContainer pixelize-local-603-metaDataContainer h-blog-meta" data-kubio="kubio/post-meta">
				<span class="metadata-item">
					<span class="metadata-prefix">
						<?php esc_html_e('on', 'pixelize'); ?>
					</span>
					<a href="<?php echo esc_url(get_day_link(get_post_time( 'Y' ),get_post_time( 'm' ),get_post_time( 'j' ))); ?>">
						<?php echo esc_html(get_the_date('F j, Y')); ?>
					</a>
				</span>
			</div>
			<div class="position-relative wp-block-kubio-read-more-button__spacing pixelize-search__k__7TrnS1SQ70-spacing pixelize-local-604-spacing">
				<span class="wp-block wp-block-kubio-read-more-button  position-relative wp-block-kubio-read-more-button__outer pixelize-search__k__7TrnS1SQ70-outer pixelize-local-604-outer kubio-button-container" data-kubio="kubio/read-more-button">
					<a class="position-relative wp-block-kubio-read-more-button__link pixelize-search__k__7TrnS1SQ70-link pixelize-local-604-link h-w-100 h-global-transition" href="<?php echo esc_url(get_the_permalink()); ?>">
						<span class="position-relative wp-block-kubio-read-more-button__text pixelize-search__k__7TrnS1SQ70-text pixelize-local-604-text kubio-inherit-typography">
							<?php esc_html_e('read more', 'pixelize'); ?>
						</span>
						<span class="h-svg-icon wp-block-kubio-read-more-button__icon pixelize-search__k__7TrnS1SQ70-icon pixelize-local-604-icon" name="font-awesome/arrow-right">
							<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" id="arrow-right" viewBox="0 0 1536 1896.0833">
								<path d="M1472 960q0 54-37 91l-651 651q-39 37-91 37-51 0-90-37l-75-75q-38-38-38-91t38-91l293-293H117q-52 0-84.5-37.5T0 1024V896q0-53 32.5-90.5T117 768h704L528 474q-38-36-38-90t38-90l75-75q38-38 90-38 53 0 91 38l651 651q37 35 37 90z"/></svg>
							</span>
						</a>
					</span>
				</div>
			</div>
		</div>
	</div>
