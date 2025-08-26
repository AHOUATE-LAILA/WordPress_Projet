<?php $component = \ColibriWP\Theme\View::getData( 'component' ); ?>
<div class="wp-block wp-block-kubio-hero  position-relative wp-block-kubio-hero__outer pixelize-front-header__k__J6FPNZyUrn-outer pixelize-local-162-outer d-flex h-section-global-spacing align-items-lg-center align-items-md-center align-items-center" data-kubio="kubio/hero" id="hero">
	<?php $component->printBackground(); ?><?php $component->printSeparator(); ?>
	<div class="position-relative wp-block-kubio-hero__inner pixelize-front-header__k__J6FPNZyUrn-inner pixelize-local-162-inner h-navigation-padding h-section-grid-container h-section-fluid-container">
		<script type='text/javascript'>
			(function () {
				// forEach polyfill
				if (!NodeList.prototype.forEach) {
					NodeList.prototype.forEach = function (callback) {
						for (var i = 0; i < this.length; i++) {
							callback.call(this, this.item(i));
						}
					}
				}
				var navigation = document.querySelector('[data-colibri-navigation-overlap="true"], .h-navigation_overlap');
				if (navigation) {
					var els = document
						.querySelectorAll('.h-navigation-padding');
					if (els.length) {
						els.forEach(function (item) {
							item.style.paddingTop = navigation.offsetHeight + "px";
						});
					}
				}
			})();
		</script>
		<div class="wp-block wp-block-kubio-row  position-relative wp-block-kubio-row__container pixelize-front-header__k__bgnhUSaQMl-container pixelize-local-163-container gutters-row-lg-0 gutters-row-v-lg-0 gutters-row-md-0 gutters-row-v-md-2 gutters-row-0 gutters-row-v-0" data-kubio="kubio/row">
			<div class="position-relative wp-block-kubio-row__inner pixelize-front-header__k__bgnhUSaQMl-inner pixelize-local-163-inner h-row align-items-lg-stretch align-items-md-stretch align-items-stretch justify-content-lg-center justify-content-md-center justify-content-center gutters-col-lg-0 gutters-col-v-lg-0 gutters-col-md-0 gutters-col-v-md-2 gutters-col-0 gutters-col-v-0">
				<div class="wp-block wp-block-kubio-column  position-relative wp-block-kubio-column__container pixelize-front-header__k__9IGHpldIpw-container pixelize-local-164-container d-flex h-col-lg-auto h-col-md-auto h-col-auto" data-kubio="kubio/column">
					<div class="position-relative wp-block-kubio-column__inner pixelize-front-header__k__9IGHpldIpw-inner pixelize-local-164-inner d-flex h-flex-basis h-px-lg-0 v-inner-lg-3 h-px-md-2 v-inner-md-2 h-px-0 v-inner-2">
						<div class="position-relative wp-block-kubio-column__align pixelize-front-header__k__9IGHpldIpw-align pixelize-local-164-align h-y-container h-column__content h-column__v-align flex-basis-100 align-self-lg-center align-self-md-center align-self-center">
							<?php pixelize_theme()->get('front-title')->render(); ?><?php pixelize_theme()->get('front-subtitle')->render(); ?><?php pixelize_theme()->get('buttons')->render(); ?>
						</div>
					</div>
				</div>
				<div class="wp-block wp-block-kubio-column  position-relative wp-block-kubio-column__container pixelize-front-header__k__ZHeXKNiPK-container pixelize-local-173-container d-flex h-col-lg-auto h-col-md-auto h-col-auto" data-kubio="kubio/column">
					<div class="position-relative wp-block-kubio-column__inner pixelize-front-header__k__ZHeXKNiPK-inner pixelize-local-173-inner d-flex h-flex-basis h-px-lg-0 v-inner-lg-0 h-px-md-0 v-inner-md-0 h-px-0 v-inner-0">
						<div class="position-relative wp-block-kubio-column__align pixelize-front-header__k__ZHeXKNiPK-align pixelize-local-173-align h-y-container h-column__content h-column__v-align flex-basis-100 align-self-lg-center align-self-md-center align-self-center">
							<?php pixelize_theme()->get('front-image')->render(); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
