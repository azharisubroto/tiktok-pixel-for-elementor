(
	function ( $, window, document ) {
		'use strict';

		$(
			function ( $ ) {

				$( document ).on(
					// Buttons, headings, images.
					'click',
					'[data-wpl_tracker] a:not(.tiktok-pixel-for-elementor-exclude)',
					function ( event ) {
						var $link    = $( this ),
							href     = $link.attr( 'href' ),
							target   = $link.attr( 'target' ),
							lightbox = $link.data( 'elementor-open-lightbox' ),
							options  = $link.parents( '.tiktok-pixel-for-elementor' ).data( 'wpl_tracker' );

						// Отменим переход по ссылке.
						event.preventDefault();

						/**
						 * Если есть ссылка для перехода, добавим задержку, чтобы трекинг успел отработать.
						 *
						 * Магия правил:
						 *
						 * 1. `'%23' !== href.substr( 0, 3 )` - когда по кнопке открывается попап
						 * 2. `href && '#' !== href` - когда ставят пустые ссылки
						 * 3. `( ! lightbox || 'no' === lightbox )` - когда по кнопке открывается лайтбокс.
						 */
						if ( href && '#' !== href && '%23' !== href.substr( 0, 3 ) && ( ! lightbox || 'no' === lightbox ) ) {
							track_element( options );
							console.log( 'Click with link' );

							/**
							 * Open in new window.
							 *
							 * @link https://learn.javascript.ru/popup-windows
							 */
							if ( target && '_blank' === target ) {
								window.open( href );
							} else {
								setTimeout(
									function () {
										document.location.href = href;
									},
									2000
								);
							}
						} else {
							track_element( options );
							console.log( 'Click without link' );
						}
					}
				);

				$( document ).on(
					// Forms.
					'submit_success',
					'[data-wpl_tracker] form:not(.tiktok-pixel-for-elementor-exclude)',
					function ( event ) {
						var $form   = $( this ),
							options = $form.parents( '.tiktok-pixel-for-elementor' ).data( 'wpl_tracker' );

						track_element( options );
						// console.log( 'Submit success' );
					}
				);

				function track_element( options ) {
					if ( options.tiktok ) {
						console.log(options.tiktok_event_params);
						if( options.tiktok_event_use_params ) {
							track_tiktok(
                options.tiktok_event_name,
                options.tiktok_event_params
              );
						} else {
							track_tiktok(options.tiktok_event_name);
						}
					}
				}

				function track_tiktok( event_name, params = {} ) {
					if (window.ttq && typeof ttq === "object") {
            ttq.track(event_name, params);
          } else {
            window.console.log("TikTok Pixel not loaded");
          }
				}
			}
		);
	}
)( window.jQuery, window, document );

// eof.
