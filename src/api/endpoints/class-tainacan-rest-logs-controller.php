<?php

use Tainacan\Entities;
use Tainacan\Repositories;

class TAINACAN_REST_Logs_Controller extends TAINACAN_REST_Controller {
	private $logs_repository;
	private $log;

	/**
	 * TAINACAN_REST_Logs_Controller constructor.
	 */
	public function __construct() {
		$this->namespace = 'tainacan/v2';
		$this->rest_base = 'logs';

		add_action('rest_api_init', array($this, 'register_routes'));
		add_action('init', array($this, 'init_objects'));
	}

	public function init_objects(){
		$this->logs_repository = new Repositories\Logs();
		$this->log = new Entities\Log();
	}

	public function register_routes() {
		register_rest_route($this->namespace, '/' . $this->rest_base . '/',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array($this, 'get_items'),
					'permission_callback' => array($this, 'get_items_permissions_check'),
				)
			)
		);
		register_rest_route($this->namespace, '/' . $this->rest_base . '/(?P<log_id>[\d]+)',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array($this, 'get_item'),
					'permission_callback' => array($this, 'get_item_permissions_check'),
				)
			)
		);
	}

	/**
	 * @param mixed $item
	 * @param WP_REST_Request $request
	 *
	 * @return array|WP_Error|WP_REST_Response
	 */
	public function prepare_item_for_response( $item, $request ) {
		if(!empty($item)){
			return $item->__toArray();
		}

		return $item;
	}

	/**
	 * @param WP_REST_Request $request
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_items( $request ) {
		$args = $this->prepare_filters($request);

		$logs = $this->logs_repository->fetch($args);

		$map = $this->logs_repository->get_map();

		$response = [];
		if($logs->have_posts()){
			while ($logs->have_posts()){
				$logs->the_post();

				$collection = new Entities\Log($logs->post);

				array_push($response, $this->get_only_needed_attributes($collection, $map));
			}

			wp_reset_postdata();
		}

		$total_logs  = $logs->found_posts;
		$max_pages = ceil($total_logs / (int) $logs->query_vars['posts_per_page']);

		$rest_response = new WP_REST_Response($response, 200);

		$rest_response->header('X-WP-Total', (int) $total_logs);
		$rest_response->header('X-WP-TotalPages', (int) $max_pages);

		return $rest_response;
	}

	/**
	 * @param WP_REST_Request $request
	 *
	 * @return bool|WP_Error
	 */
	public function get_items_permissions_check( $request ) {
		return $this->logs_repository->can_read($this->log);
	}

	/**
	 * @param WP_REST_Request $request
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function get_item( $request ) {
		$log_id = $request['log_id'];

		$log = $this->logs_repository->fetch($log_id);

		$prepared_log = $this->prepare_item_for_response( $log, $request );

		return new WP_REST_Response($prepared_log, 200);
	}

	/**
	 * @param WP_REST_Request $request
	 *
	 * @return bool|WP_Error
	 */
	public function get_item_permissions_check( $request ) {
		$log = $this->logs_repository->fetch($request['log_id']);

		if($log instanceof Entities\Log){
			return $log->can_read();
		}

		return false;
	}
}

?>