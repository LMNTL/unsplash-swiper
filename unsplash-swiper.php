<?php
/**
 * Plugin Name:       Unsplash Swiper
 * Description:       Image carousel block that pulls photos from Unsplash.com and The Cat API.
 * Requires at least: 5.8
 * Requires PHP:      7.0
 * Version:           1.0.0
 * Author:            Jessica Thomas
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       unsplash-swiper
 *
 * @package           create-block
 */

if ( ! defined( 'WPINC' ) ) {
	die('No direct script access allowed.');
}

/*
* The following to files contain API keys for Unsplash (unsplash.com)
* and The Cat API (thecatapi.com). They contain the constants
* CSGWP_UNSPLASH_API_KEY and CSGWP_CAT_API_KEY.
*/
require_once(__DIR__."/unsplash_api_key.php"); 
require_once(__DIR__."/cat_api_key.php");

class Unsplash_Swiper {
	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	private function build_request( $block_attributes ) {
		if( $block_attributes['catMode'] ) {
			$endpoint = "https://api.thecatapi.com/v1/images/search";
			$args = array(
				"headers" => array(
					"x-api-key" => CSGWP_CAT_API_KEY
				),
				"body" => array(
					"breed" => $block_attributes['breed'],
					"limit" => $block_attributes['imageCount']
				)
			);
			return array( $endpoint, $args );
		}

		$endpoint = "https://api.unsplash.com/";
		$args = array(
			"headers" => array(
				"Authorization" => CSGWP_UNSPLASH_API_KEY
			)
		);
		if( $block_attributes['query'] ) {
			$endpoint .= "search/photos";
			$args["body"] = array(
				"query" => esc_url( $block_attributes['query'] ),
				"per_page" => esc_url( $block_attributes['imageCount'] )
			);
		}
		else {
			$endpoint .= "photos/random";
			$args["body"] = array(
				"count" => $block_attributes['imageCount']
			);
		}
		return array( $endpoint, $args );
	}

	protected function get_api_result($endpoint, $args) {
		$res = wp_remote_get( $endpoint, $args );
		$res = wp_remote_retrieve_body( $res );
		return json_decode($res);
	}

	protected function render_html($block_attributes, $response) {
		$render_body = '<script type="module">
			const swiper = new Swiper(".swiper", {
				direction: "horizontal",
				loop: true,
				observer: true,
				observeParents: true,
				centeredSlides: true,
				navigation: {
					nextEl: ".swiper-button-next",
					prevEl: ".swiper-button-prev",
				},
			});
		</script>
		<div class="swiper unsplash-csgwp__swiper">
		<div class="swiper-wrapper">';

		foreach($response as $key=>$val){
			$url = ($block_attributes['catMode']) ? $response[$key]->url : $response[$key]->urls->full;
			$render_body .= sprintf(
				'<div class="swiper-slide unsplash-csgwp__slider">
					<img class="unsplash-csgwp__image" src="%1s"/>
				</div>',
				esc_html($url)
			);
		}
		$render_body .= "</div>
			<div class='swiper-button-next'></div>
			<div class='swiper-button-prev'></div>
			</div>";
		return $render_body;
	}

	public function render_callback( $block_attributes, $content ) {
		list( $endpoint, $args ) = $this->build_request( $block_attributes );
		$res = $this->get_api_result( $endpoint, $args );
		if( !$res or array_key_exists( 'error', $res ) ) {
			return null;
		}
		return $this->render_html($block_attributes, $res);
	}

	public function unsplash_swiper_block_init() {
		wp_register_script("swiper-js", "https://unpkg.com/swiper/swiper-bundle.min.js");
		wp_register_style("swiper-css", "https://unpkg.com/swiper@7/swiper-bundle.min.css");
		wp_register_style("csgwp-css", plugins_url( "/unsplash_swiper.css", __FILE__ ));
		register_block_type( __DIR__, array(
			"render_callback" => [ $this, 'render_callback' ],
			"script" => "swiper-js",
			"style" => [ "swiper-css", "csgwp-css" ]
		));
	}
}

$plugin = new Unsplash_Swiper("unsplash-swiper", "1.0.0");

add_action( 'init', [ $plugin, 'unsplash_swiper_block_init' ] );