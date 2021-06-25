<?php
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once  ABSPATH . 'wp-admin/includes/class-wp-list-table.php' ;
}

class Customers_List extends WP_List_Table { 

	/** Class constructor */
	public function __construct() {
		parent::__construct( [
			'singular' => __( 'Customer', 'sp' ), //singular name of the listed records
			'plural'   => __( 'Customers', 'sp' ), //plural name of the listed records
			'ajax'     => false //should this table support ajax?

		] );
	}

	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	*/
	public function get_bulk_actions() {
		$actions = [
			'bulk-import' => 'Import Bulk'
		];
		return $actions;
	}

	/**
	 *  Associative array of columns
	 *
	 * @return array
	*/
	function get_columns() {
		$columns = [
		  'cb'      => '<input type="checkbox" />',
		  'Image'      => __( 'Image'),
		  'Title'      => __( 'Title'),
		  'Sku'    => __( 'Sku'),
		  'Item_id'    => __( 'Item_id'),
		  'Price' => __( 'Price'),
		  'Type'    => __( 'Type'),
		  'Action'    => __( 'Action')
		];
		return $columns;
	}


	/**
	 * Render a column when no column specific method exists.
	 *
	 * @param array $item
	 * @param string $column_name
	 *
	 * @return mixed
	*/
	public function column_default( $item, $column_name ) {
		// To disable the button when product is already imported once //
			$sku =  $item['item']['item_sku'];
			global $wpdb;
			$check_existing_sku = $wpdb->get_col( $wpdb->prepare( "SELECT meta_value FROM $wpdb->postmeta WHERE  meta_value='%s'", $sku) );
		if ( $check_existing_sku ) {
			$render_this ='<input type="button" value="Import Product" class="import" disabled>';
		} else {
			$render_this ='<input type="button" value="Import Product" data-item_id="' . $item['item']['item_id'] . '" data-item_sku="' . $item['item']['item_sku'] . '" class="import">';
		}
		// ---------------------------------------------------------- //    

		// return print_r($item);
		switch ( $column_name ) {
			case 'Image':
				return '<img src=' . $item['item']['images'][0] . ' height="100" width="100">'; 
			case 'Title':
				return $item['item']['name'];
			case 'Sku':
				return $item['item']['item_sku'];
			case 'Item_id':
				return $item['item']['item_id'];
			case 'Price':
				return $item['item']['original_price'];
			case 'Type':
				if ( 1 == $item['item']['has_variation']) {
					return 'Variable';
				} else {
					return 'Simple';
				}
			case 'Action':
				return $render_this;
			default:
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}
	}


	/**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	*/
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="bulk-import[]" data-item_sku = "' . $item['item']['item_sku'] . '" value="' . $item['item']['item_id'] . '" />'
		);
	}

	/**
	 * Handles data query and filter, sorting, and pagination.
	*/
	public function prepare_items() {

		/** Process bulk action */
		$this->process_bulk_action();

		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers =array($columns, $hidden, $sortable);
	}
}
