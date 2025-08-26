<?php $component = \ColibriWP\Theme\View::getData( 'component' ); ?>
<div class="wp-block wp-block-kubio-navigation  position-relative wp-block-kubio-navigation__outer <?php echo $component->printNavigationClasses(); ?> pixelize-header__k__-X5WmROADcU-outer pixelize-local-618-outer h-navigation_overlap" data-kubio="kubio/navigation" data-kubio-component="overlap" data-kubio-settings="true" id="navigation">
	<?php pixelize_theme()->get('inner-top-bar')->render(); ?>
	<div class="wp-block wp-block-kubio-navigation-section  position-relative wp-block-kubio-navigation-section__nav pixelize-header__k__hLXjFkQI_ff-nav pixelize-local-635-nav h-section h-navigation <?php echo $component->printNavigationTypeClasses(); ?>" data-kubio="kubio/navigation-section" data-kubio-component="navigation" data-kubio-settings="{&quot;sticky&quot;:{&quot;startAfterNode&quot;:{&quot;enabled&quot;:false},&quot;animations&quot;:{&quot;enabled&quot;:false,&quot;duration&quot;:0.5,&quot;name&quot;:&quot;slideDown&quot;}},&quot;overlap&quot;:true}">
		<div class="position-relative wp-block-kubio-navigation-section__nav-section pixelize-header__k__hLXjFkQI_ff-nav-section pixelize-local-635-nav-section    <?php echo $component->printContainerClasses(); ?>">
			<div class="wp-block wp-block-kubio-navigation-items  position-relative wp-block-kubio-navigation-items__outer pixelize-header__k__F-EsTKi8Mdz-outer pixelize-local-636-outer" data-kubio="kubio/navigation-items" data-nav-normal="true">
				<div class="wp-block wp-block-kubio-row  position-relative wp-block-kubio-row__container pixelize-header__k__Xs4HIiwiA61-container pixelize-local-637-container gutters-row-lg-0 gutters-row-v-lg-2 gutters-row-md-2 gutters-row-v-md-2 gutters-row-2 gutters-row-v-2" data-kubio="kubio/row">
					<div class="position-relative wp-block-kubio-row__inner pixelize-header__k__Xs4HIiwiA61-inner pixelize-local-637-inner h-row align-items-lg-stretch align-items-md-stretch align-items-stretch justify-content-lg-center justify-content-md-center justify-content-center gutters-col-lg-0 gutters-col-v-lg-2 gutters-col-md-2 gutters-col-v-md-2 gutters-col-2 gutters-col-v-2">
						<div class="wp-block wp-block-kubio-column  position-relative wp-block-kubio-column__container pixelize-header__k__NegNbfnXgQR-container pixelize-local-638-container d-flex h-col-lg-auto h-col-md-auto h-col" data-kubio="kubio/column">
							<div class="position-relative wp-block-kubio-column__inner pixelize-header__k__NegNbfnXgQR-inner pixelize-local-638-inner d-flex h-flex-basis h-px-lg-0 v-inner-lg-0 h-px-md-0 v-inner-md-0 h-px-2 v-inner-2">
								<div class="position-relative wp-block-kubio-column__align pixelize-header__k__NegNbfnXgQR-align pixelize-local-638-align h-y-container h-column__content h-column__v-align flex-basis-auto align-self-lg-center align-self-md-center align-self-center">
									<?php pixelize_theme()->get('logo')->render(array (
  'wrapper_class' => 'wp-block wp-block-kubio-logo position-relative wp-block-kubio-logo__container  kubio-logo-direction-row pixelize-header__k__3p-j4vSFZU_-container pixelize-local--container',
  'logo_image_class' => 'position-relative wp-block-kubio-logo__image  kubio-logo-image  pixelize-header__k__3p-j4vSFZU_-image pixelize-local--image',
  'alt_logo_image_class' => 'position-relative wp-block-kubio-logo__alternateImage kubio-logo-image kubio-alternate-logo-image   pixelize-header__k__3p-j4vSFZU_-alternateImage pixelize-local--alternateImage',
  'logo_text_class' => 'position-relative wp-block-kubio-logo__text  pixelize-header__k__3p-j4vSFZU_-text pixelize-local--text',
)); ?>
								</div>
							</div>
						</div>
						<div class="wp-block wp-block-kubio-column  kubio-hide-on-mobile position-relative wp-block-kubio-column__container pixelize-header__k__-D0AUwT705t-container pixelize-local-640-container d-flex h-col-lg h-col-md h-col" data-kubio="kubio/column">
							<div class="position-relative wp-block-kubio-column__inner pixelize-header__k__-D0AUwT705t-inner pixelize-local-640-inner d-flex h-flex-basis h-px-lg-3 v-inner-lg-0 h-px-md-3 v-inner-md-0 h-px-3 v-inner-0">
								<div class="position-relative wp-block-kubio-column__align pixelize-header__k__-D0AUwT705t-align pixelize-local-640-align h-y-container h-column__content h-column__v-align flex-basis-100 align-self-lg-center align-self-md-center align-self-center">
									<div class="wp-block wp-block-kubio-spacer  position-relative wp-block-kubio-spacer__container pixelize-header__k__5pwlFy15lHM-container pixelize-local-641-container" data-kubio="kubio/spacer"></div>
								</div>
							</div>
						</div>
						<div class="wp-block wp-block-kubio-column  position-relative wp-block-kubio-column__container pixelize-header__k__CHIANIrbbs--container pixelize-local-642-container d-flex h-col-lg-auto h-col-md-auto h-col-auto" data-kubio="kubio/column">
							<div class="position-relative wp-block-kubio-column__inner pixelize-header__k__CHIANIrbbs--inner pixelize-local-642-inner d-flex h-flex-basis h-px-lg-0 v-inner-lg-0 h-px-md-0 v-inner-md-0 h-px-2 v-inner-2">
								<div class="position-relative wp-block-kubio-column__align pixelize-header__k__CHIANIrbbs--align pixelize-local-642-align h-y-container h-column__content h-column__v-align flex-basis-auto align-self-lg-center align-self-md-center align-self-center">
									<?php pixelize_theme()->get('header-menu')->render(); ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
