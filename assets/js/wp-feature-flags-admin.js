/**
 * Javascript for plugin admin.
 *
 * @package BoxUk\WpFeatureFlags
 */

const WP_FEATURE_FLAGS_BUTTON_SELECTOR = '.wp-feature-flags__button';
const WP_FEATURE_FLAG_TOGGLES = [ 'toggle_feature', 'toggle_preview' ];
const WP_FEATURE_FLAG_DIRECTIONS = [ 'on' , 'off' ];

/**
 * Handles submit buttons on the admin page.
 */
let WpFeatureFlagsAdmin = function() {
	'use strict';

	document.addEventListener(
		"DOMContentLoaded",
		function () {
			const flagButtons = document.querySelectorAll( WP_FEATURE_FLAGS_BUTTON_SELECTOR );

			if ( flagButtons.length === 0) {
				// Exit early if there are no feature flag buttons.
				return;
			}

			flagButtons.forEach(
				function ( flagButton ) {
					flagButton.addEventListener(
						'click',
						function ( e ) {
							e.preventDefault();

							const singleButton = e.target;
							const flagKey = singleButton.dataset.flag;
							const flagAction = singleButton.dataset.action;
							const flagDirection = singleButton.dataset.toggle;

							// Check all data attributes are set and valid.
							if (
								typeof( flagAction ) !== 'undefined' &&
								WP_FEATURE_FLAG_TOGGLES.includes( flagAction ) &&
								typeof( flagKey ) !== 'undefined' &&
								typeof( flagDirection ) !== 'undefined' &&
								WP_FEATURE_FLAG_DIRECTIONS.includes( flagDirection )
							) {
								this.toggleFlag( flagKey, flagAction, flagDirection, singleButton );
							}
						}.bind( this ),
						false
					);
				},
				this
			);
		}.bind( this ),
		false
	);
};

/**
 * AJAX handling for toggling flags on the admin page.
 * Applies to both previewing and publishing flags.
 *
 * @param {string} flagKey The key of the flag.
 * @param {string} flagAction The action to take - preview or publish.
 * @param {string} flagDirection The direction to toggle the flag - on or off.
 * @param {object} buttonObj The object of the clicked button.
 */
WpFeatureFlagsAdmin.prototype.toggleFlag = function ( flagKey, flagAction, flagDirection, buttonObj ) {
	const apiRequest = new XMLHttpRequest();
	apiRequest.open( 'POST', WPFFAdmin.ajaxUrl, true );
	apiRequest.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded;' );
	buttonObj.classList.add( 'disabled' );

	apiRequest.onload = function () {
		// Prevent more clicks.

		if (this.status >= 200 && this.status < 400) {
			buttonObj.classList.remove( 'disabled' );
			buttonObj.setAttribute( 'data-toggle', flagDirection === 'on' ? 'off' : 'on' );

			if ( 'toggle_feature' === flagAction ) {
				buttonObj.value = flagDirection === 'on' ? 'Unpublish' : 'Publish';

				let previewButton = document.querySelector( WP_FEATURE_FLAGS_BUTTON_SELECTOR + '[data-action="toggle_preview"][data-flag="' + flagKey + '"]' );

				// Published flags cannot be previewed.
				if ( 'on' === flagDirection ) {
					previewButton.classList.add( 'disabled' );
				} else {
					previewButton.classList.remove( 'disabled' );
				}

			} else if ( 'toggle_preview' === flagAction ) {
				buttonObj.value = flagDirection === 'on' ? 'Off' : 'On';
			}
		} else {
			// TODO: Proper error handling.
			buttonObj.value = 'Error';
		}
	};

	apiRequest.send( 'action=' + flagAction + '&secret=' + WPFFAdmin.secret + '&flag_key=' + flagKey + '&flag_direction=' + flagDirection );
}

new WpFeatureFlagsAdmin();
