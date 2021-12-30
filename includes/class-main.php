<?php
/**
 * @package tiktok-pixel-for-elementor
 */
namespace WPL\Tiktok_Pixel_Tracker_For_Elementor;

use Elementor\Controls_Manager;
use Elementor\Element_Base;
use Elementor\Widget_Base;
use Elementor\Plugin;
//use ElementorPro\Plugin;

class Main {
	/**
	 * @var Options $options
	 */
	private $options;

	/**
	 * @var array $allowed_widget Array of allowed widgets to tracking.
	 */
	private $allowed_widget = array(
		'button',
		'form',
		'heading',
		'image',
		'icon-list',
		'call-to-action',
		'price-table',
	);

	/**
	 * Main constructor.
	 *
	 * @param Options $options
	 */
	public function __construct( $options = null ) {
		$this->options = $options;

		if ( ! $this->options ) {
			$this->options = new Options();
		}

		$this->hooks();
	}

	/**
	 * Register hooks
	 */
	public function hooks() {
		add_action( 'elementor/element/button/section_button/after_section_end', array( $this, 'add_tracking_controls' ), 10, 2 );
		add_action( 'elementor/element/form/section_form_options/after_section_end', array( $this, 'add_tracking_controls' ), 10, 2 );
		add_action( 'elementor/element/heading/section_title/after_section_end', array( $this, 'add_tracking_controls' ), 10, 2 );
		add_action( 'elementor/element/image/section_image/after_section_end', array( $this, 'add_tracking_controls' ), 10, 2 );
		add_action( 'elementor/element/icon-list/section_icon_list/after_section_end', array( $this, 'add_tracking_controls' ), 10, 2 );
		add_action( 'elementor/element/call-to-action/section_ribbon/after_section_end', array( $this, 'add_tracking_controls' ), 10, 2 );
		add_action( 'elementor/element/price-table/section_ribbon/after_section_end', array( $this, 'add_tracking_controls' ), 10, 2 );

		add_action( 'elementor/widget/before_render_content', array( $this, 'before_render' ) );
		add_action( 'elementor/frontend/before_enqueue_scripts', array( $this, 'enqueue_scripts' ), 9 );
		add_action( 'wp_footer', [ $this, 'add_tracker_code_to_footer' ] );
		add_action( 'wp_head', [ $this, 'add_tracker_code_to_header' ] );
		add_action( 'wp_body_open', [ $this, 'add_tracker_code_to_body' ] );

		$a = [];

		add_action( 'elementor/element/icon-list/section_icon_list/before_section_end__', function ( Widget_Base $widget ) {
			$elementor   = Plugin::instance();
			$widget_name = $widget->get_name();

			$control_data = $elementor->controls_manager->get_control_from_stack( $widget_name, 'icon_list' );

			if ( is_wp_error( $control_data ) ) {
				return;
			}

			$controls = [
				'masked'         =>
					[
						'name'  => 'gh_tiktok_pixel_for_elementor_vkontakte',
						'label' => __( 'VK', 'masked-input-for-elementor' ),
						'type'  => Controls_Manager::SWITCHER,
						'tab'   => 'advanced',
					],
				'masked_type'    =>
					[
						'name'       => 'gh_tiktok_pixel_for_elementor_vkontakte_event_name',
						'label'      => __( 'Event Name', 'masked-input-for-elementor' ),
						'type'       => Controls_Manager::TEXT,
						'default'    => '',
						'conditions' => [
							'terms' => [
								[
									'name'     => 'gh_tiktok_pixel_for_elementor_vkontakte',
									'operator' => '==',
									'value'    => 'yes',
								],
							],
						],
					],
			];

			$control_data['fields'] = array_merge( $control_data['fields'], $controls );

			$elementor->controls_manager->update_control_in_stack( $widget, 'icon_list', $control_data );
		} );
	}

	/**
	 * Get option value for plugin.
	 *
	 * @param string $key
	 * @param bool   $default
	 *
	 * @return mixed|void
	 */
	public function get_option( $key, $default = false ) {
		return get_option( 'elementor_' . WPL_ELEMENTOR_EVENTS_TRACKER_SLUG . '_' . $key, $default );
	}

	/**
	 * Add tracker codes to site header.
	 */
	public function add_tracker_code_to_header() {
		$tiktok_pixel_id        = $this->get_option( 'tiktok_pixel_id' );

		if ( $tiktok_pixel_id ) {
			?>
			<!-- TikTok Pixel Code -->
			<script>
				!function (w, d, t) {
					w.TiktokAnalyticsObject=t;var ttq=w[t]=w[t]||[];ttq.methods=["page","track","identify","instances","debug","on","off","once","ready","alias","group","enableCookie","disableCookie"],ttq.setAndDefer=function(t,e){t[e]=function(){t.push([e].concat(Array.prototype.slice.call(arguments,0)))}};for(var i=0;i<ttq.methods.length;i++)ttq.setAndDefer(ttq,ttq.methods[i]);ttq.instance=function(t){for(var e=ttq._i[t]||[],n=0;n<ttq.methods.length;n++)ttq.setAndDefer(e,ttq.methods[n]);return e},ttq.load=function(e,n){var i="https://analytics.tiktok.com/i18n/pixel/events.js";ttq._i=ttq._i||{},ttq._i[e]=[],ttq._i[e]._u=i,ttq._t=ttq._t||{},ttq._t[e]=+new Date,ttq._o=ttq._o||{},ttq._o[e]=n||{};var o=document.createElement("script");o.type="text/javascript",o.async=!0,o.src=i+"?sdkid="+e+"&lib="+t;var a=document.getElementsByTagName("script")[0];a.parentNode.insertBefore(o,a)};
					ttq.load('<?php echo $tiktok_pixel_id; ?>');
					ttq.page();
				}(window, document, 'ttq');
			</script>
			<!-- End TikTok Pixel Code -->
			<?php
		}
	}

	/**
	 * Add tracker codes to site body.
	 */
	public function add_tracker_code_to_body() {
		$gtm_id = $this->get_option( 'gtm_id' );

		if ( $gtm_id ) {
			?>
			<!-- Google Tag Manager (noscript) -->
			<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo esc_js( $gtm_id ); ?>" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
			<!-- End Google Tag Manager (noscript) -->
			<?php
		}
	}

	/**
	 * Add tracker codes to site footer.
	 */
	public function add_tracker_code_to_footer() {
		// damn
	}

	/**
	 * Add required scripts.
	 */
	public function enqueue_scripts() {
		wp_enqueue_script(
			WPL_ELEMENTOR_EVENTS_TRACKER_SLUG . '_app',
			WPL_ELEMENTOR_EVENTS_TRACKER_URL . 'frontend/js/app.js',
			array( 'jquery', 'elementor-frontend' ),
			filemtime( WPL_ELEMENTOR_EVENTS_TRACKER_DIR . 'frontend/js/app.js' ),
			true
		);
	}

	/**
	 * Add new TikTok Pixel Tracking section to buttons/forms
	 *
	 * @param Element_Base $element
	 * @param array $args
	 */
	public function add_tracking_controls( $element, $args ) {

		// Element name.
		$name = $element->get_name();

		$element->start_controls_section(
			'gh_tiktok_pixel_for_elementor',
			array(
				'label' => esc_html__( 'TikTok Pixel Tracking', 'tiktok-pixel-for-elementor' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			)
		);


		// Hidden control from Button & Form widgets.
		if ( ! in_array( $name, [ 'form', 'button' ] ) ) {
			$element->add_control(
				'gh_tiktok_pixel_for_elementor_gtm_css_id',
				array(
					'label'       => esc_html__( 'CSS ID', 'tiktok-pixel-for-elementor' ),
					'type'        => Controls_Manager::TEXT,
					'show_label'  => true,
					'placeholder' => esc_html__( 'Without #', 'tiktok-pixel-for-elementor' ),
					'condition'   => array(
						'gh_tiktok_pixel_for_elementor_gtm' => 'yes',
					),
					'render_type' => 'none',
				)
			);
		}

		$element->add_control(
			'gh_tiktok_pixel_for_elementor_tiktok',
			array(
				'label'       => esc_html__( 'Track with TikTok', 'tiktok-pixel-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'render_type' => 'none',
			)
		);

		$element->add_control(
			'gh_tiktok_pixel_for_elementor_tiktok_event_name',
			array(
				'label'       => esc_html__( 'TikTok Event', 'tiktok-pixel-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => [
					'ViewContent'						=> 'View Content',
					'AddPaymentInfo' 				=> 'Add Payment Info',
					'AddToCart' 						=> 'Add to Cart',
					'AddToWishlist' 				=> 'Add to Wishlist',
					'ClickButton' 					=> 'Click Button',
					'CompletePayment' 			=> 'Complete Payment',
					'CompleteRegistration' 	=> 'Complete Registration',
					'Contact' 							=> 'Contact',
					'Download' 							=> 'Download',
					'InitiateCheckout' 			=> 'Initiate Checkout',
					'PlaceAnOrder'					=> 'Place an Order',
					'Search' 								=> 'Search',
					'SubmitForm' 						=> 'Submit Form',
					'Subscribe' 						=> 'Subscribe',
				],
				'default'     => 'ViewContent',
				'condition'   => array(
					'gh_tiktok_pixel_for_elementor_tiktok' => 'yes',
				),
				'render_type' => 'none',
			)
		);

		$element->add_control(
			'gh_tiktok_pixel_for_elementor_tiktok_event_name_custom',
			array(
				'label'       => esc_html__( 'Custom Event', 'tiktok-pixel-for-elementor' ),
				'type'        => Controls_Manager::TEXT,
				'show_label'  => true,
				'placeholder' => esc_html__( 'i.e Discard', 'tiktok-pixel-for-elementor' ),
				'condition'   => array(
					'gh_tiktok_pixel_for_elementor_tiktok'            => 'yes',
					'gh_tiktok_pixel_for_elementor_tiktok_event_name' => 'Custom',
				),
				'render_type' => 'none',
			)
		);

		$element->add_control(
			'gh_tiktok_pixel_for_elementor_tiktok_params',
			array(
				'label'       => esc_html__( 'TikTok Pixel Custom Params', 'tiktok-pixel-for-elementor' ),
				'type'        => Controls_Manager::SWITCHER,
				'condition'   => array(
					'gh_tiktok_pixel_for_elementor_tiktok'            => 'yes',
				),
				'render_type' => 'none',
			)
		);
		
		$element->add_control(
			'gh_tiktok_pixel_for_elementor_tiktok_params_content_type',
			array(
				'label'       => 'content_type',
				'type'        => Controls_Manager::SELECT,
				'options'     => ["product", "product_group"],
				'show_label'  => true,
				'condition'   => array(
					'gh_tiktok_pixel_for_elementor_tiktok'            => 'yes',
					'gh_tiktok_pixel_for_elementor_tiktok_params' => 'yes',
				),
				'render_type' => 'none',
			)
		);

		$element->add_control(
			'gh_tiktok_pixel_for_elementor_tiktok_params_content_id',
			array(
				'label'       => 'content_id',
				'type'        => Controls_Manager::TEXT,
				'show_label'  => true,
				'condition'   => array(
					'gh_tiktok_pixel_for_elementor_tiktok'            => 'yes',
					'gh_tiktok_pixel_for_elementor_tiktok_params' => 'yes',
				),
				'render_type' => 'none',
			)
		);

		$element->add_control(
			'gh_tiktok_pixel_for_elementor_tiktok_params_content_category',
			array(
				'label'       => 'content_category',
				'type'        => Controls_Manager::TEXT,
				'show_label'  => true,
				'condition'   => array(
					'gh_tiktok_pixel_for_elementor_tiktok'            => 'yes',
					'gh_tiktok_pixel_for_elementor_tiktok_params' => 'yes',
				),
				'render_type' => 'none',
			)
		);
		
		$element->add_control(
			'gh_tiktok_pixel_for_elementor_tiktok_params_content_name',
			array(
				'label'       => 'content_name',
				'type'        => Controls_Manager::TEXT,
				'show_label'  => true,
				'condition'   => array(
					'gh_tiktok_pixel_for_elementor_tiktok'            => 'yes',
					'gh_tiktok_pixel_for_elementor_tiktok_params' => 'yes',
				),
				'render_type' => 'none',
			)
		);

		$element->add_control(
			'gh_tiktok_pixel_for_elementor_tiktok_params_currency',
			array(
				'label'       => 'currency',
				'type'        => Controls_Manager::SELECT,
				'options'     => ["AED", "ARS", "AUD", "BDT", "BIF", "BOB", "BRL", "CAD", "CHF", "CLP", "CNY", "COP", "CRC", "CZK", "DKK", "DZD", "EGP", "EUR", "GBP", "GTQ", "HKD", "HNL", "HUF", "IDR", "ILS", "INR", "ISK", "JPY", "KES", "KRW", "KWD", "KZT", "MAD", "MOP", "MXN", "MYR", "NGN", "NIO", "NOK", "NZD", "PEN", "PHP", "PKR", "PLN", "PYG", "QAR", "RON", "RUB", "SAR", "SEK", "SGD", "THB", "TRY", "TWD", "USD", "VES", "VND", "ZAR"],
				'show_label'  => true,
				'condition'   => array(
					'gh_tiktok_pixel_for_elementor_tiktok'            => 'yes',
					'gh_tiktok_pixel_for_elementor_tiktok_params' => 'yes',
				),
				'render_type' => 'none',
			)
		);
		

		$element->end_controls_section();
	}

	/**
	 * @param Widget_Base $element
	 */
	public function before_render( $element ) {

		$name = $element->get_name();

		if ( in_array( $name, $this->allowed_widget ) ) {

			$data = $element->get_data();

			$settings     = $data['settings'];
			$attr         = array();
			$has_tracking = false;

			// TikTok.
			if ( isset( $settings['gh_tiktok_pixel_for_elementor_tiktok'] ) ) {
				$has_tracking                = true;
				$attr['tiktok']            = true;
				$attr['tiktok_event_name'] = $settings['gh_tiktok_pixel_for_elementor_tiktok_event_name'];
				$attr['tiktok_event_use_params'] = $settings['gh_tiktok_pixel_for_elementor_tiktok_params'];

				if ( isset( $settings['gh_tiktok_pixel_for_elementor_tiktok_event_name_custom'] ) ) {
					$attr['tiktok_event_name_custom'] = $settings['gh_tiktok_pixel_for_elementor_tiktok_event_name_custom'];
				}

				$params_type = ['content_type','content_id','content_category','content_name','currency','value','quantity*','price'];
				$paramsobj = [];
				
				foreach($params_type as $param) {
					$paramsobj[$param] = $settings['gh_tiktok_pixel_for_elementor_tiktok_params_'.$param];
				}

				if ( isset( $settings['gh_tiktok_pixel_for_elementor_tiktok_params'] ) ) {
					$attr['tiktok_event_params'] = $paramsobj;
				}
			}

			if ( $has_tracking ) {
				$element->add_render_attribute(
					'_wrapper',
					array(
						'data-wpl_tracker' => json_encode( $attr ),
						'class'            => 'tiktok-pixel-for-elementor',
					)
				);
			}

			if ( isset( $settings['gh_tiktok_pixel_for_elementor_gtm_css_id'] ) ) {
				$control = 'url';

				if ( 'image' === $name ) {
					$control = 'link';
				}

				$element->add_render_attribute(
					$control,
					'data-wpl_id',
					esc_attr( $settings['gh_tiktok_pixel_for_elementor_gtm_css_id'] )
				);
			}
		}
	}
}

// eol.
