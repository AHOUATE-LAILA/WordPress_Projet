<?php $component = \ColibriWP\Theme\View::getData( 'component' ); ?>
<div class="wp-block wp-block-kubio-navigation  position-relative wp-block-kubio-navigation__outer <?php echo $component->printNavigationClasses(); ?> pixelize-front-header__k__Gp3qTlxXlu-outer pixelize-local-89-outer h-navigation_overlap" data-kubio="kubio/navigation" data-kubio-component="overlap" data-kubio-settings="true" id="navigation">
	<?php pixelize_theme()->get('front-top-bar')->render(); ?>
	<div class="wp-block wp-block-kubio-navigation-section  position-relative wp-block-kubio-navigation-section__nav pixelize-front-header__k__xLwdIMLPC_l-nav pixelize-local-106-nav h-section h-navigation <?php echo $component->printNavigationTypeClasses(); ?>" data-kubio="kubio/navigation-section" data-kubio-component="navigation" data-kubio-settings="{&quot;sticky&quot;:{&quot;startAfterNode&quot;:{&quot;enabled&quot;:false},&quot;animations&quot;:{&quot;enabled&quot;:false,&quot;duration&quot;:0.5,&quot;name&quot;:&quot;slideDown&quot;}},&quot;overlap&quot;:true}">
		<div class="position-relative wp-block-kubio-navigation-section__nav-section pixelize-front-header__k__xLwdIMLPC_l-nav-section pixelize-local-106-nav-section    <?php echo $component->printContainerClasses(); ?>">
			<div class="wp-block wp-block-kubio-navigation-items  position-relative wp-block-kubio-navigation-items__outer pixelize-front-header__k__DqcL_YF9LKJ-outer pixelize-local-107-outer" data-kubio="kubio/navigation-items" data-nav-normal="true">
				<div class="wp-block wp-block-kubio-row  position-relative wp-block-kubio-row__container pixelize-front-header__k__MqErEXZ17Jm-container pixelize-local-108-container gutters-row-lg-0 gutters-row-v-lg-2 gutters-row-md-2 gutters-row-v-md-2 gutters-row-2 gutters-row-v-2" data-kubio="kubio/row">
					<div class="position-relative wp-block-kubio-row__inner pixelize-front-header__k__MqErEXZ17Jm-inner pixelize-local-108-inner h-row align-items-lg-stretch align-items-md-stretch align-items-stretch justify-content-lg-center justify-content-md-center justify-content-center gutters-col-lg-0 gutters-col-v-lg-2 gutters-col-md-2 gutters-col-v-md-2 gutters-col-2 gutters-col-v-2">
						<div class="wp-block wp-block-kubio-column  position-relative wp-block-kubio-column__container pixelize-front-header__k__SFXC9Ze09eu-container pixelize-local-109-container d-flex h-col-lg-auto h-col-md-auto h-col" data-kubio="kubio/column">
							<div class="position-relative wp-block-kubio-column__inner pixelize-front-header__k__SFXC9Ze09eu-inner pixelize-local-109-inner d-flex h-flex-basis h-px-lg-0 v-inner-lg-0 h-px-md-0 v-inner-md-0 h-px-2 v-inner-2">
								<div class="position-relative wp-block-kubio-column__align pixelize-front-header__k__SFXC9Ze09eu-align pixelize-local-109-align h-y-container h-column__content h-column__v-align flex-basis-auto align-self-lg-center align-self-md-center align-self-center">
									<?php pixelize_theme()->get('logo')->render(array (
  'wrapper_class' => 'wp-block wp-block-kubio-logo position-relative wp-block-kubio-logo__container  kubio-logo-direction-row pixelize-front-header__k__YpuDHFCdd-container pixelize-local--container',
  'logo_image_class' => 'position-relative wp-block-kubio-logo__image  kubio-logo-image  pixelize-front-header__k__YpuDHFCdd-image pixelize-local--image',
  'alt_logo_image_class' => 'position-relative wp-block-kubio-logo__alternateImage kubio-logo-image kubio-alternate-logo-image   pixelize-front-header__k__YpuDHFCdd-alternateImage pixelize-local--alternateImage',
  'logo_text_class' => 'position-relative wp-block-kubio-logo__text  pixelize-front-header__k__YpuDHFCdd-text pixelize-local--text',
)); ?>
								</div>
							</div>
						</div>
						<div class="wp-block wp-block-kubio-column  kubio-hide-on-mobile position-relative wp-block-kubio-column__container pixelize-front-header__k__Cu9zji0X5O-container pixelize-local-111-container d-flex h-col-lg h-col-md h-col" data-kubio="kubio/column">
							<div class="position-relative wp-block-kubio-column__inner pixelize-front-header__k__Cu9zji0X5O-inner pixelize-local-111-inner d-flex h-flex-basis h-px-lg-3 v-inner-lg-0 h-px-md-3 v-inner-md-0 h-px-3 v-inner-0">
								<div class="position-relative wp-block-kubio-column__align pixelize-front-header__k__Cu9zji0X5O-align pixelize-local-111-align h-y-container h-column__content h-column__v-align flex-basis-100 align-self-lg-center align-self-md-center align-self-center">
									<div class="wp-block wp-block-kubio-spacer  position-relative wp-block-kubio-spacer__container pixelize-front-header__k__uj2bfOyqkL-container pixelize-local-112-container" data-kubio="kubio/spacer"></div>
								</div>
							</div>
						</div>
						<div class="wp-block wp-block-kubio-column  position-relative wp-block-kubio-column__container pixelize-front-header__k__ZEkYpBrx7RA-container pixelize-local-113-container d-flex h-col-lg-auto h-col-md-auto h-col-auto" data-kubio="kubio/column">
							<div class="position-relative wp-block-kubio-column__inner pixelize-front-header__k__ZEkYpBrx7RA-inner pixelize-local-113-inner d-flex h-flex-basis h-px-lg-0 v-inner-lg-0 h-px-md-0 v-inner-md-0 h-px-2 v-inner-2">
								<div class="position-relative wp-block-kubio-column__align pixelize-front-header__k__ZEkYpBrx7RA-align pixelize-local-113-align h-y-container h-column__content h-column__v-align flex-basis-auto align-self-lg-center align-self-md-center align-self-center">
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
