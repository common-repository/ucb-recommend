<?php
namespace UCBRecommendService;

if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;

class UCBRecommend_Calculate extends UCBRecommend_Service_Base
{

	private static $_instance = null;

	public static function get_instance()
	{
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new UCBRecommend_Calculate();
		}
		return self::$_instance;
	}

	public function get_bandits( $widget_id, $context, $number = null, $post_ids = null )
	{
		global $ucbr_condition;
		$widget = $ucbr_condition->get_widget( $widget_id );
		if ( !$widget ) {
			return array();
		}
		global $ucbr_model_widget;
		$widget_id = $ucbr_model_widget->get_value( $widget, 'uuid' );

		global $ucbr_model_number;
		if ( is_null( $post_ids ) ) {
			$rows = $ucbr_model_number->fetch_all(
				array(
					array(
						'AND',
						array(
							array( 'widget_id', 'like', '?', $widget_id ),
							array( 'context', '=', '?', $context )
						)
					)
				)
			);
		} else {
			if ( count( $post_ids ) <= 0 ) {
				return array();
			}
			$rows = $ucbr_model_number->fetch_all(
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
			);
		}
		if ( is_null( $number ) ) {
			$number = $this->apply_filters( 'data_number', UCB_RECOMMEND_GET_DATA_NUMBER, -1 );
		}

		if ( count( $rows ) <= 0 ) {
			if ( is_null( $post_ids ) ) {
				$posts = get_posts(
					array(
						'fields' => 'ids',
						'numberposts' => $number,
						'orderby' => 'rand'
					)
				);
			} else {
				global $ucbr_custom_post_type;
				$post_type = $ucbr_custom_post_type->get_post_type();
				$types = get_post_types( array( 'exclude_from_search' => false ) );
				$types[$post_type] = $post_type;
				$posts = get_posts(
					array(
						'fields' => 'ids',
						'numberposts' => $number,
						'orderby' => 'rand',
						'include' => join( ',', $post_ids ),
						'post_type' => $types
					)
				);
			}
			return array_slice( array_map( function ( $p ) {
				return array( 'post_id' => $p, 'bandit' => PHP_INT_MAX, 'score' => PHP_INT_MAX, 'n' => 0, 'c' => 0 );
			}, $posts ), 0, $number );
		}

		if ( is_null( $post_ids ) ) {
			$posts = get_posts(
				array(
					'fields' => 'ids',
					'numberposts' => $number,
					'orderby' => 'rand',
					'exclude' => join( ',', array_map( function ( $d ) use ( $ucbr_model_number ) {
						return $ucbr_model_number->get_value( $d, 'post_id' );
					}, $rows ) )
				)
			);
			$posts = array_map( function ( $p ) {
				return array( 'post_id' => $p, 'bandit' => PHP_INT_MAX, 'score' => PHP_INT_MAX, 'n' => 0, 'c' => 0 );
			}, $posts );

		} else {
			$exists = array_map( function ( $d ) use ( $ucbr_model_number ) {
				return $ucbr_model_number->get_value( $d, 'post_id' ) - 0;
			}, $rows );
			$not_exists = array_diff( $post_ids, $exists );

			$posts = array_map( function ( $p ) {
				return array( 'post_id' => $p, 'bandit' => PHP_INT_MAX, 'score' => PHP_INT_MAX, 'n' => 0, 'c' => 0 );
			}, $not_exists );
		}

		if ( count( $posts ) >= $number ) {
			return array_slice( $posts, 0, $number );
		}

		$const = $this->apply_filters( 'bandit_const', UCB_RECOMMEND_UCB_CONST ) - 0;

		$total = array_sum( array_map( function ( $d ) use ( $ucbr_model_number ) {
			return $ucbr_model_number->get_value( $d, 'number' ) - 0;
		}, $rows ) );
		$log = log( $total );
		$rows = array_map( function ( $d ) use ( $ucbr_model_number, $log, $const ) {
			$post_id = $ucbr_model_number->get_value( $d, 'post_id' ) - 0;
			$n_i = $ucbr_model_number->get_value( $d, 'number' ) - 0;
			$c_i = $ucbr_model_number->get_value( $d, 'clicked' ) - 0;
			if ( $const <= 0 ) {
				$bandit = 1.0 * $c_i / $n_i;
				$score = $this->get_score( $bandit );
			} elseif ( $n_i <= 0 ) {
				$bandit = PHP_INT_MAX;
				$score = PHP_INT_MAX;
			} else {
				$bandit = 1.0 * $c_i / $n_i + $const * sqrt( $log / $n_i );
				$score = $this->get_score( $bandit );
			}
			return array( 'post_id' => $post_id, 'bandit' => $bandit, 'score' => $score, 'n' => $n_i, 'c' => $c_i );
		}, $rows );

		$rows = array_merge( $posts, $rows );
		usort( $rows, function ( $a, $b ) {
			if ( $a['score'] == $b['score'] ) {
				return 0;
			}
			return ( $a['score'] > $b['score'] ) ? -1 : 1;
		} );

		return array_slice( $rows, 0, $number );
	}

	private function get_score( $bandit )
	{
		if ( $bandit >= PHP_INT_MAX ) {
			return $bandit;
		}
		$std_dev = $this->apply_filters( 'std_dev', UCB_RECOMMEND_BANDIT_RANDOM_STD_DEV );
		if ( $std_dev <= 0 ) {
			return $bandit;
		}
		return self::generate_norm( $bandit, $std_dev );
	}
}

$GLOBALS['ucbr_calculate'] = UCBRecommend_Calculate::get_instance();
