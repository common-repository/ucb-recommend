<?php
namespace UCBRecommendService;

if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;

class UCBRecommend_Data extends UCBRecommend_Service_Base
{

	private static $_instance = null;

	public static function get_instance()
	{
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new UCBRecommend_Data();
		}
		return self::$_instance;
	}

	public function register( $widget_id, $context, $post_ids )
	{
		global $ucbr_condition;
		$widget = $ucbr_condition->get_widget( $widget_id );
		if ( !$widget ) {
			return array();
		}
		global $ucbr_model_widget;
		$widget_id = $ucbr_model_widget->get_value( $widget, 'uuid' );

		if ( !array( $post_ids ) ) {
			$post_ids = array( $post_ids );
		}
		if ( count( $post_ids ) <= 0 ) {
			return array();
		}

		$data = array();
		foreach ( $post_ids as $p ) {
			$data[$p] = array( $p, $this->uuid() );
		}

		global $ucbr_model_test, $ucbr_model_number;
		$ucbr_model_test->insert_all(
			array(
				'widget_id',
				'post_id',
				'context',
				'hash'
			),
			array_map( function ( $d ) use ( $context, $widget_id ) {
				return array( $widget_id, $d[0], $context, $this->get_hash( $d[1] ) );
			}, $data )
		);

		$registered = array_map( function ( $d ) use ( $ucbr_model_number ) {
			return $ucbr_model_number->get_value( $d, 'post_id' ) - 0;
		}, $ucbr_model_number->fetch_all(
			array(
				array(
					'AND',
					array(
						array( 'widget_id', 'like', '?', $widget_id ),
						array( 'context', '=', '?', $context ),
						array( 'post_id', 'in', $ucbr_model_number->get_in_placeholder( $post_ids ), $post_ids )
					)
				)
			)
		) );

		$not_registered = array_diff( $post_ids, $registered );

		if ( count( $registered ) > 0 ) {
			$ucbr_model_number->update(
				array(
					'number' => array( 'number + 1' )
				),
				array_map( function ( $d ) use ( $context, $widget_id ) {
					return array(
						'AND',
						array(
							array( 'widget_id', 'like', '?', $widget_id ),
							array( 'context', '=', '?', $context ),
							array( 'post_id', '=', '?', $d )
						)
					);
				}, $registered ),
				'OR'
			);
		}

		$ucbr_model_number->insert_all(
			array(
				'widget_id',
				'post_id',
				'context',
				'number',
				'clicked'
			),
			array_map( function ( $d ) use ( $context, $widget_id ) {
				return array( $widget_id, $d, $context, 1, 0 );
			}, $not_registered )
		);

		return $data;
	}

	public function update( $key )
	{
		global $ucbr_model_test, $ucbr_model_number;
		$row = $this->find( $key );
		if ( !$row ) {
			return false;
		}

		$ucbr_model_test->delete(
			array(
				array(
					'AND',
					array(
						array( 'uuid', 'LIKE', '?', $ucbr_model_test->get_value( $row, 'uuid' ) )
					)
				)
			)
		);

		$widget_id = $ucbr_model_test->get_value( $row, 'widget_id' );
		$post_id = $ucbr_model_test->get_value( $row, 'post_id' );
		$context = $ucbr_model_test->get_value( $row, 'context' );
		$ucbr_model_number->update(
			array(
				'clicked' => array( 'clicked + 1' )
			),
			array(
				array(
					'AND',
					array(
						array( 'widget_id', 'like', '?', $widget_id ),
						array( 'post_id', '=', '?', $post_id ),
						array( 'context', '=', '?', $context )
					)
				)
			)
		);

		return true;
	}

	private function find( $key )
	{
		global $ucbr_model_test;
		$hash = $this->get_hash( $key );

		return $ucbr_model_test->fetch(
			array(
				array(
					'AND',
					array(
						array( 'hash', 'LIKE', '?', $hash )
					)
				)
			)
		);
	}

	public function delete( $widget_id )
	{
		global $ucbr_model_test, $ucbr_model_number;
		$ucbr_model_test->clear(
			array(
				array(
					'AND',
					array(
						array( 'widget_id', 'LIKE', '?', $widget_id )
					)
				)
			)
		);
		$ucbr_model_number->clear(
			array(
				array(
					'AND',
					array(
						array( 'widget_id', 'LIKE', '?', $widget_id )
					)
				)
			)
		);
	}
}

$GLOBALS['ucbr_data'] = UCBRecommend_Data::get_instance();
