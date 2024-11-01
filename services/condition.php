<?php
namespace UCBRecommendService;

if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;

class UCBRecommend_Condition extends UCBRecommend_Service_Base
{

	private static $_instance = null;

	private $data_types = array(
		'string' => array( '', '%s' ),
		'int' => array( 'intval', '%d' ),
		'float' => array( 'floatval', '%f' ),
	);

	private $join_types = array(
		'INNER JOIN',
		'LEFT JOIN',
		'RIGHT JOIN',
	);

	private $verbs = array(
		'string' => array(
			'=' => false,
			'>' => false,
			'<' => false,
			'>=' => false,
			'<=' => false,
			'!=' => false,
			'like' => false,
			'like %%[?]' => false,
			'like [?]%%' => false,
			'like %%[?]%%' => false,
			'not like' => false,
			'not like %%[?]' => false,
			'not like [?]%%' => false,
			'not like %%[?]%%' => false,
			'in ([?])' => true,
			'not in ([?])' => true,
			//			'between' => false,
			//			'not between' => false,
			//			'is null' => false,
			//			'is not null' => false,
			'regexp' => false,
			'not regexp' => false
		),
		'int' => array(
			'=' => false,
			'>' => false,
			'<' => false,
			'>=' => false,
			'<=' => false,
			'!=' => false,
			'like' => false,
			'like %%[?]' => false,
			'like [?]%%' => false,
			'like %%[?]%%' => false,
			'not like' => false,
			'not like %%[?]' => false,
			'not like [?]%%' => false,
			'not like %%[?]%%' => false,
			'in ([?])' => true,
			'not in ([?])' => true,
			//			'between' => false,
			//			'not between' => false,
			//			'is null' => false,
			//			'is not null' => false,
		),
		'float' => array(
			'=' => false,
			'>' => false,
			'<' => false,
			'>=' => false,
			'<=' => false,
			'!=' => false,
			'like' => false,
			'like %%[?]' => false,
			'like [?]%%' => false,
			'like %%[?]%%' => false,
			'not like' => false,
			'not like %%[?]' => false,
			'not like [?]%%' => false,
			'not like %%[?]%%' => false,
			'in ([?])' => true,
			'not in ([?])' => true,
			//			'between' => false,
			//			'not between' => false,
			//			'is null' => false,
			//			'is not null' => false,
		),
	);

	private $mysql_data_types = array(
		'TINYINT' => 'int',
		'SMALLINT' => 'int',
		'MEDIUMINT' => 'int',
		'INT' => 'int',
		'BIGINT' => 'int',
		'FLOAT' => 'float',
		'DOUBLE' => 'float',
		'DECIMAL' => 'float',
	);

	private $widgets = array();

	public static function get_instance()
	{
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new UCBRecommend_Condition();
		}
		return self::$_instance;
	}

	public function get_types()
	{
		return $this->apply_filters( 'get_types', $this->data_types );
	}

	public function get_type_values()
	{
		return array_keys( $this->get_types() );
	}

	public function get_type( $type )
	{
		$types = $this->get_types();
		if ( !array_key_exists( $type, $types ) ) {
			return array( '', '%s' );
		}
		return $types[$type];
	}

	public function get_verbs( $type, $convert = false )
	{
		if ( $convert ) {
			$type = $this->convert_mysql_column_type( $type );
		}
		$verbs = $this->apply_filters( 'get_verbs', $this->verbs );
		if ( isset( $verbs[$type] ) ) {
			return $verbs[$type];
		}
		return array();
	}

	public function get_verb( $type, $index, $convert = false )
	{
		$verbs = $this->get_verbs( $type, $convert );
		if ( $index < 0 || $index >= count( $verbs ) ) {
			return array( '=' => false );
		}
		return array_slice( $verbs, $index, 1, true );
	}

	public function get_verb_values( $type, $convert = false )
	{
		return array_keys( $this->get_verbs( $type, $convert ) );
	}

	public function get_join_types()
	{
		return $this->apply_filters( 'join_types', $this->join_types );
	}

	public function get_join_type( $index )
	{
		$join_types = $this->get_join_types();
		if ( $index < 0 || $index >= count( $join_types ) ) {
			return 'INNER JOIN';
		}
		return $join_types[$index];
	}

	private function get_default_join_tables()
	{
		global $wpdb;
		return array(
			array( 1, $wpdb->postmeta, 'post_id', $wpdb->posts, 'ID' ),
			array( 1, $wpdb->term_relationships, 'object_id', $wpdb->posts, 'ID' ),
			array( 1, $wpdb->term_taxonomy, 'term_taxonomy_id', $wpdb->term_relationships, 'term_taxonomy_id' ),
			array( 1, $wpdb->terms, 'term_id', $wpdb->term_taxonomy, 'term_id' )
		);
	}

	public function get_condition_set()
	{
		global $wpdb, $ucbr_custom_post_type;
		static $list = null;
		if ( !is_null( $list ) ) {
			return $list;
		}
		$list = array(
			'post-status' => array(
				__( 'Post Status', UCB_RECOMMEND_TEXT_DOMAIN ),
				array(
					array( $wpdb->posts, 'post_status', 6, array( 'publish' ) )
				),
				true
			),
			'post-type' => array(
				__( 'Post Type', UCB_RECOMMEND_TEXT_DOMAIN ),
				array(
					array( $wpdb->posts, 'post_type', 14, array() )
				),
				true
			),
			'post-id' => array(
				__( 'Post ID', UCB_RECOMMEND_TEXT_DOMAIN ),
				array(
					array( $wpdb->posts, 'ID', 14, array() )
				),
				false
			),
			'category-id' => array(
				__( 'Category ID', UCB_RECOMMEND_TEXT_DOMAIN ),
				array(
					array( $wpdb->term_taxonomy, 'taxonomy', 6, array( 'category' ) ),
					array( $wpdb->terms, 'term_id', 14, array() )
				),
				false
			),
			'category-slug' => array(
				__( 'Category Slug', UCB_RECOMMEND_TEXT_DOMAIN ),
				array(
					array( $wpdb->term_taxonomy, 'taxonomy', 6, array( 'category' ) ),
					array( $wpdb->terms, 'slug', 14, array() )
				),
				false
			),
			'tag-id' => array(
				__( 'Tag ID', UCB_RECOMMEND_TEXT_DOMAIN ),
				array(
					array( $wpdb->term_taxonomy, 'taxonomy', 6, array( 'post_tag' ) ),
					array( $wpdb->terms, 'term_id', 14, array() )
				),
				false
			),
			'tag-slug' => array(
				__( 'Tag Slug', UCB_RECOMMEND_TEXT_DOMAIN ),
				array(
					array( $wpdb->term_taxonomy, 'taxonomy', 6, array( 'post_tag' ) ),
					array( $wpdb->terms, 'slug', 14, array() )
				),
				false
			),
			'design' => array(
				__( 'Design Parts', UCB_RECOMMEND_TEXT_DOMAIN ),
				array(
					array( $wpdb->posts, 'post_type', 6, array( $ucbr_custom_post_type->get_post_type() ) ),
					array( $wpdb->term_taxonomy, 'taxonomy', 6, array( $ucbr_custom_post_type->get_taxonomy() ) ),
					array( $wpdb->terms, 'name', 14, array( 'sample tag name' ) )
				),
				false
			)
		);
		return $list;
	}

	public function convert_mysql_column_type( $type )
	{
		if ( array_key_exists( strtolower( $type ), $this->mysql_data_types ) ) {
			return $this->mysql_data_types[strtolower( $type )];
		}
		if ( array_key_exists( strtoupper( $type ), $this->mysql_data_types ) ) {
			return $this->mysql_data_types[strtoupper( $type )];
		}
		return 'string';
	}

	private function get_initial_condition_set()
	{
		return array_filter( $this->get_condition_set(), function ( $d ) {
			return $d[2];
		} );
	}

	public function add_condition_set( $widget_id, $slug )
	{
		if ( is_string( $slug ) ) {
			$set = $this->get_condition_set();
			if ( !isset( $set[$slug] ) ) {
				return false;
			}
			$set = $set[$slug];
		} else {
			$set = $slug;
		}

		$group_id = $this->save_condition_group( false, $widget_id, $set[0] );

		foreach ( $set[1] as $d ) {
			$this->save_condition( false, $group_id, $d[0], $d[1], $d[2], $d[3] );
		}
		return $group_id;
	}

	private function add_initial_condition_set( $widget_id )
	{
		foreach ( $this->get_initial_condition_set() as $set ) {
			$this->add_condition_set( $widget_id, $set );
		}
		return true;
	}

	public function get_tables()
	{
		global $wpdb;
		$sql = "SELECT DISTINCT TABLE_NAME
				FROM INFORMATION_SCHEMA.COLUMNS
				WHERE
					TABLE_SCHEMA = %s";

		$rows = $wpdb->get_results( $wpdb->prepare( $sql, DB_NAME ) );

		return array_map( function ( $d ) {
			return $d->TABLE_NAME;
		}, $rows );
	}

	public function check_table( $table )
	{
		global $wpdb;
		$sql = "SELECT COLUMN_NAME
				FROM INFORMATION_SCHEMA.COLUMNS
				WHERE
					TABLE_SCHEMA = %s
					AND TABLE_NAME = %s";

		$rows = $wpdb->get_results( $wpdb->prepare( $sql, DB_NAME, $table ) );

		return !empty( $rows );
	}

	public function get_columns( $table = null )
	{
		global $wpdb;
		$sql = "SELECT DISTINCT TABLE_NAME, COLUMN_NAME, DATA_TYPE
				FROM INFORMATION_SCHEMA.COLUMNS
				WHERE
					TABLE_SCHEMA = %s";
		if ( !is_null( $table ) ) {
			$sql .= "AND TABLE_NAME = %s";
			$rows = $wpdb->get_results( $wpdb->prepare( $sql, DB_NAME, $table ) );
		} else {
			$rows = $wpdb->get_results( $wpdb->prepare( $sql, DB_NAME ) );
		}

		return array_map( function ( $d ) {
			return array( $d->COLUMN_NAME, $d->DATA_TYPE, $d->TABLE_NAME );
		}, $rows );
	}

	public function get_column_type( $table, $column )
	{
		global $wpdb;
		$sql = "SELECT DISTINCT DATA_TYPE
				FROM INFORMATION_SCHEMA.COLUMNS
				WHERE
					TABLE_SCHEMA = %s
					AND TABLE_NAME = %s
					AND COLUMN_NAME = %s";

		$row = $wpdb->get_row( $wpdb->prepare( $sql, DB_NAME, $table, $column ) );
		return $row->DATA_TYPE;
	}

	public function check_column( $table, $column )
	{
		global $wpdb;
		$sql = "SELECT COLUMN_NAME
				FROM INFORMATION_SCHEMA.COLUMNS
				WHERE
					TABLE_SCHEMA = %s
					AND TABLE_NAME = %s
					AND COLUMN_NAME = %s";

		$rows = $wpdb->get_results( $wpdb->prepare( $sql, DB_NAME, $table, $column ) );

		return !empty( $rows );
	}

	public function save_join_table( $uuid, $widget_id, $order, $type, $table1, $column1, $table2, $column2 )
	{
		if ( !$this->check_column( $table1, $column1 ) ) {
			return false;
		}

		if ( !$this->check_column( $table2, $column2 ) ) {
			return false;
		}

		global $ucbr_model_join_table;
		if ( empty( $uuid ) ) {
			$uuid = $ucbr_model_join_table->insert(
				array(
					'widget_id' => $widget_id,
					'join_order' => $order,
					'type' => $type,
					'table1' => $table1,
					'column1' => $column1,
					'table2' => $table2,
					'column2' => $column2,
				)
			);
		} else {
			if ( $ucbr_model_join_table->fetch(
				array(
					array(
						'AND',
						array(
							array( 'uuid', 'like', '?', $uuid )
						)
					)
				)
			)
			) {
				$ucbr_model_join_table->update(
					array(
						'widget_id' => $widget_id,
						'join_order' => $order,
						'type' => $type,
						'table1' => $table1,
						'column1' => $column1,
						'table2' => $table2,
						'column2' => $column2,
					),
					array(
						array(
							'AND',
							array(
								array( 'uuid', 'like', '?', $uuid )
							)
						)
					)
				);
			} else {
				$this->delete_table( $uuid );
				return $this->save_table( false, $widget_id, $order, $type, $table1, $column1, $table2, $column2 );
			}
		}
		return $uuid;
	}

	public function delete_join_table( $uuid )
	{
		global $ucbr_model_join_table;
		$ucbr_model_join_table->clear(
			array(
				array(
					'AND',
					array(
						array( 'uuid', 'like', '?', $uuid )
					)
				)
			)
		);
		return true;
	}

	public function get_join_tables( $widget_id )
	{
		global $ucbr_model_join_table;
		return $ucbr_model_join_table->fetch_all(
			array(
				array(
					'AND',
					array(
						array( 'widget_id', 'like', '?', $widget_id )
					)
				)
			),
			array(
				'join_order' => 'asc',
				'created_at' => 'asc'
			)
		);
	}

	public function save_condition_group( $uuid, $widget_id, $name )
	{
		global $ucbr_model_condition_group;
		if ( empty( $uuid ) ) {
			$uuid = $ucbr_model_condition_group->insert(
				array(
					'name' => $name,
					'widget_id' => $widget_id
				)
			);
		} else {
			if ( $ucbr_model_condition_group->fetch(
				array(
					array(
						'AND',
						array(
							array( 'uuid', 'like', '?', $uuid )
						)
					)
				)
			)
			) {
				$ucbr_model_condition_group->update(
					array(
						'name' => $name,
						'widget_id' => $widget_id
					),
					array(
						array(
							'AND',
							array(
								array( 'uuid', 'like', '?', $uuid )
							)
						)
					)
				);
			} else {
				$this->delete_condition_group( $uuid );
				return $this->save_condition_group( false, $widget_id, $name );
			}
		}
		return $uuid;
	}

	public function delete_condition_group( $uuid )
	{
		global $ucbr_model_condition, $ucbr_model_condition_group;
		$conditions = $ucbr_model_condition->fetch_all(
			array(
				array(
					'AND',
					array(
						array( 'group_id', 'like', '?', $uuid )
					)
				)
			)
		);
		foreach ( $conditions as $condition ) {
			$this->delete_condition( $ucbr_model_condition->get_value( $condition, 'uuid' ) );
		}

		$ucbr_model_condition_group->clear(
			array(
				array(
					'AND',
					array(
						array( 'uuid', 'like', '?', $uuid )
					)
				)
			)
		);
		return true;
	}

	public function get_condition_group( $uuid )
	{
		global $ucbr_model_condition_group;
		return $ucbr_model_condition_group->fetch(
			array(
				array(
					'AND',
					array(
						array( 'uuid', 'like', '?', $uuid )
					)
				)
			)
		);
	}

	public function get_condition_groups( $widget_id )
	{
		global $ucbr_model_condition_group;
		return $ucbr_model_condition_group->fetch_all(
			array(
				array(
					'AND',
					array(
						array( 'widget_id', 'like', '?', $widget_id )
					)
				)
			)
		);
	}

	public function save_condition( $uuid, $group_id, $table, $column, $verb, $objects = false )
	{
		$group = $this->get_condition_group( $group_id );
		if ( !$group ) {
			return false;
		}

		global $ucbr_model_condition;
		if ( empty( $uuid ) ) {
			$uuid = $ucbr_model_condition->insert(
				array(
					'group_id' => $group_id,
					'table' => $table,
					'column' => $column,
					'verb' => $verb
				)
			);
		} else {
			if ( $ucbr_model_condition->fetch(
				array(
					array(
						'AND',
						array(
							array( 'uuid', 'like', '?', $uuid )
						)
					)
				)
			)
			) {
				$ucbr_model_condition->update(
					array(
						'group_id' => $group_id,
						'table' => $table,
						'column' => $column,
						'verb' => $verb
					),
					array(
						array(
							'AND',
							array(
								array( 'uuid', 'like', '?', $uuid )
							)
						)
					)
				);
			} else {
				$this->delete_condition( $uuid );
				return $this->save_condition( false, $group_id, $table, $column, $verb, $objects );
			}
		}
		if ( $uuid ) {
			$this->save_objects( $uuid, $objects );
		}
		return $uuid;
	}

	public function delete_condition( $uuid )
	{
		global $ucbr_model_condition, $ucbr_model_object;
		$ucbr_model_object->clear(
			array(
				array(
					'AND',
					array(
						array( 'condition_id', 'like', '?', $uuid )
					)
				)
			)
		);
		$ucbr_model_condition->clear(
			array(
				array(
					'AND',
					array(
						array( 'uuid', 'like', '?', $uuid )
					)
				)
			)
		);
		return true;
	}

	public function get_conditions( $group_id )
	{
		global $ucbr_model_condition;
		return $ucbr_model_condition->fetch_all(
			array(
				array(
					'AND',
					array(
						array( 'group_id', 'like', '?', $group_id )
					)
				)
			)
		);
	}

	public function save_objects( $condition_id, $objects )
	{
		if ( false === $objects ) {
			return false;
		}
		if ( !is_array( $objects ) ) {
			$objects = array( $objects );
		}

		global $ucbr_model_object;
		$rows = $ucbr_model_object->fetch_all(
			array(
				array(
					'AND',
					array(
						array( 'condition_id', 'like', '?', $condition_id )
					)
				)
			)
		);
		$objects = array_map( function ( $d ) {
			return (string)$d;
		}, array_unique( $objects ) );
		$rows = array_map( function ( $r ) use ( $ucbr_model_object ) {
			return $ucbr_model_object->get_value( $r, 'value' );
		}, $rows );
		$insert = array_values( array_diff( $objects, $rows ) );
		$delete = array_values( array_diff( $rows, $objects ) );
		$ucbr_model_object->insert_all(
			array(
				'condition_id',
				'value'
			),
			array_map( function ( $d ) use ( $condition_id ) {
				return array( $condition_id, $d );
			}, $insert )
		);
		if ( count( $delete ) > 0 ) {
			$ucbr_model_object->clear(
				array(
					array(
						'AND',
						array(
							array( 'condition_id', 'like', '?', $condition_id ),
							array( 'value', 'in', $ucbr_model_object->get_in_placeholder( $delete ), $delete )
						)
					)
				)
			);
		}
		return true;
	}

	public function delete_objects( $condition_id )
	{
		return $this->save_objects( $condition_id, array() );
	}

	public function get_objects( $condition_id )
	{
		global $ucbr_model_object;
		return $ucbr_model_object->fetch_all(
			array(
				array(
					'AND',
					array(
						array( 'condition_id', 'like', '?', $condition_id )
					)
				)
			)
		);
	}

	public function get_widgets()
	{
		global $ucbr_model_widget;
		return $ucbr_model_widget->fetch_all( false, array(
			'id' => 'asc'
		) );
	}

	public function get_widget( $id )
	{
		if ( !array_key_exists( $id, $this->widgets ) ) {
			global $ucbr_model_widget;
			$this->widgets[$id] = $ucbr_model_widget->fetch(
				array(
					array(
						'AND',
						array(
							array( 'id', '=', '?', $id )
						)
					)
				)
			);
		}
		return $this->widgets[$id];
	}

	private function get_widget_id()
	{
		global $ucbr_model_widget;
		$ids = array_map( function ( $d ) use ( $ucbr_model_widget ) {
			return $ucbr_model_widget->get_value( $d, 'id' );
		}, $this->get_widgets() );
		if ( count( $ids ) <= 0 ) {
			return 1;
		}
		return max( $ids ) + 1;
	}

	public function save_widget( $uuid, $name )
	{
		global $ucbr_model_widget;
		if ( empty( $uuid ) ) {
			$id = $this->get_widget_id();
			$uuid = $ucbr_model_widget->insert(
				array(
					'id' => $id,
					'name' => $name
				)
			);
			$this->add_initial_condition_set( $uuid );
		} else {
			$widget = $ucbr_model_widget->fetch(
				array(
					array(
						'AND',
						array(
							array( 'uuid', 'like', '?', $uuid )
						)
					)
				)
			);
			if ( $widget ) {
				$ucbr_model_widget->update(
					array(
						'name' => $name
					),
					array(
						array(
							'AND',
							array( 'uuid', 'like', '?', $uuid )
						)
					)
				);
				$id = $ucbr_model_widget->get_value( $widget, 'id' );
				unset( $this->widgets[$id] );
			} else {
				$this->delete_widget( $uuid );
				return $this->save_widget( false, $name );
			}
		}

		return array(
			'uuid' => $uuid,
			'id' => $id,
			'name' => $name
		);
	}

	public function delete_widget( $uuid )
	{
		global $ucbr_model_widget, $ucbr_model_join_table, $ucbr_model_condition_group, $ucbr_data;
		$ucbr_model_join_table->clear(
			array(
				array(
					'AND',
					array(
						array( 'widget_id', 'like', '?', $uuid )
					)
				)
			)
		);
		foreach ( $ucbr_model_condition_group->fetch_all(
			array(
				array(
					'AND',
					array(
						array( 'widget_id', 'like', '?', $uuid )
					)
				)
			)
		) as $group ) {
			$this->delete_condition_group( $ucbr_model_condition_group->get_value( $group, 'uuid' ) );
		}
		$widget = $ucbr_model_widget->fetch(
			array(
				array(
					'AND',
					array(
						array( 'uuid', 'like', '?', $uuid )
					)
				)
			)
		);
		$ucbr_data->delete( $uuid );

		if ( $widget ) {
			$ucbr_model_widget->clear(
				array(
					array(
						'AND',
						array(
							array( 'uuid', 'like', '?', $uuid )
						)
					)
				)
			);

			$id = $ucbr_model_widget->get_value( $widget, 'id' );
			global $ucbr_design, $ucbr_widget_settings;
			$ucbr_design->delete_all( $id );
			$ucbr_widget_settings->delete_all( $id );
			unset( $this->widgets[$id] );
		}
		return true;
	}

	private function get_widget_conditions( $widget_id )
	{
		global $ucbr_model_condition_group, $ucbr_model_condition;
		$rows = $ucbr_model_condition_group->fetch_all(
			array(
				array(
					'AND',
					array(
						array( 'widget_id', 'like', '?', $widget_id )
					)
				)
			)
		);
		if ( count( $rows ) <= 0 ) {
			return array();
		}
		return $ucbr_model_condition->fetch_all(
			array(
				array(
					'AND',
					array(
						array(
							'group_id', 'in', $ucbr_model_condition_group->get_in_placeholder( $rows ),
							array_map( function ( $r ) use ( $ucbr_model_condition_group ) {
								return $ucbr_model_condition_group->get_value( $r, 'uuid' );
							}, $rows )
						)
					)
				)
			)
		);
	}

	public function get_post_ids( $id, $post_id = null )
	{
		$widget = $this->get_widget( $id );
		if ( !$widget ) {
			return array();
		}

		global $ucbr_model_widget;
		$widget_id = $ucbr_model_widget->get_value( $widget, 'uuid' );

		$conditions = $this->get_widget_conditions( $widget_id );
		if ( count( $conditions ) <= 0 ) {
			return array();
		}

		global $ucbr_widget_settings;
		$no_context = $ucbr_widget_settings->get_no_context_mode( $id );
		if ( $no_context ) {
			$post_id = 0;
		} elseif ( is_null( $post_id ) ) {
			$post_id = get_the_ID();
		}
		$post_id = $post_id - 0;

		global $wpdb, $ucbr_model_condition, $ucbr_model_join_table;
		$sql = "
			SELECT DISTINCT $wpdb->posts.ID
			FROM $wpdb->posts
		";

		$join_tables = array_map( function ( $d ) use ( $ucbr_model_join_table ) {
			return array(
				$ucbr_model_join_table->get_value( $d, 'type' ) - 0,
				$ucbr_model_join_table->get_value( $d, 'table1' ),
				$ucbr_model_join_table->get_value( $d, 'column1' ),
				$ucbr_model_join_table->get_value( $d, 'table2' ),
				$ucbr_model_join_table->get_value( $d, 'column2' ),
			);
		}, $this->get_join_tables( $widget_id ) );
		$merged = array_merge( $this->get_default_join_tables(), $join_tables );

		$join_tables = array();
		foreach ( $merged as $current ) {
			if ( !array_key_exists( $current[1], $join_tables ) && $wpdb->posts !== $current[1] ) {
				$join_tables[$current[1]] = $current;
			}
		}

		$join = '';
		foreach ( $join_tables as $table ) {
			$join_state = $this->get_join_type( $table[0] );
			$table1 = $table[1];
			$column1 = $table[2];
			$table2 = $table[3];
			$column2 = $table[4];

			$join .= "
				$join_state $table1
					ON {$table1}.{$column1} = {$table2}.{$column2}
			";
		}

		$where = "
			WHERE
				$wpdb->posts.ID != %d
		";
		$args = array( $post_id );
		global $ucbr_model_object;
		$objects = $ucbr_model_object->fetch_all(
			array(
				array(
					'AND',
					array(
						array(
							'condition_id', 'in', $ucbr_model_object->get_in_placeholder( $conditions ),
							array_map( function ( $d ) use ( $ucbr_model_condition ) {
								return $ucbr_model_condition->get_value( $d, 'uuid' );
							}, $conditions )
						)
					)
				)
			)
		);
		$columns = $this->get_columns();

		$conditions = array_map( function ( $d ) use ( $ucbr_model_condition, $ucbr_model_object, $objects, $columns ) {
			$condition_id = $ucbr_model_condition->get_value( $d, 'uuid' );
			$table_name = $ucbr_model_condition->get_value( $d, 'table' );
			$column = $ucbr_model_condition->get_value( $d, 'column' );
			$type = array_filter( $columns, function ( $d ) use ( $table_name, $column ) {
				return $table_name == $d[2] && $column == $d[0];
			} );
			$type = reset( $type );
			$type = $this->convert_mysql_column_type( $type[1] );
			$verb = $this->get_verb( $type, $ucbr_model_condition->get_value( $d, 'verb' ) - 0 );
			$type = $this->get_type( $type );
			$objects = array_map( function ( $d ) use ( $ucbr_model_object, $type ) {
				$format = $type[0];
				if ( $format ) {
					return $format( $ucbr_model_object->get_value( $d, 'value' ) );
				}
				return $ucbr_model_object->get_value( $d, 'value' );
			}, array_filter( $objects, function ( $d ) use ( $ucbr_model_object, $condition_id ) {
				return $condition_id == $ucbr_model_object->get_value( $d, 'condition_id' );
			} ) );
			return array( $table_name, $column, $verb, $type, $objects );
		}, $conditions );

		foreach ( $conditions as $condition ) {
			if ( count( $condition[4] ) <= 0 ) {
				if ( !$post_id ) {
					continue;
				}
				$sub = "
					SELECT {$condition[0]}.{$condition[1]}
					FROM $wpdb->posts
				";
				$sub .= $join;
				$sub .= "
					WHERE
						$wpdb->posts.ID = %d
				";
				if ( reset( $condition[2] ) ) {
					$verb = str_replace( '[?]', $sub, key( $condition[2] ) );
					$where .= "
						AND (
							{$condition[0]}.{$condition[1]} $verb
						)
					";
				} else {
					$verb = key( $condition[2] );
					if ( false !== strpos( $verb, '[?]' ) ) {
						continue;
					}
					$sub .= "
						LIMIT 1
					";
					$where .= "
						AND (
							{$condition[0]}.{$condition[1]} $verb ($sub)
						)
					";
				}
				$args = array_merge( $args, array( $post_id ) );
			} else {
				if ( reset( $condition[2] ) ) {
					$placeholder = $condition[3][1] . str_repeat( ',' . $condition[3][1], count( $condition[4] ) - 1 );
					$verb = str_replace( '[?]', $placeholder, key( $condition[2] ) );
					$where .= "
						AND (
							{$condition[0]}.{$condition[1]} $verb
						)
					";
				} else {
					$verb = key( $condition[2] );
					$where .= "
						AND (";
					if ( false !== strpos( $verb, '[?]' ) ) {
						$verb = str_replace( '[?]', $condition[3][1], $verb );
						$item = "
							{$condition[0]}.{$condition[1]} $verb
						";
					} else {
						$item = "
							{$condition[0]}.{$condition[1]} $verb {$condition[3][1]}
						";
					}
					$where .= $item . str_repeat( ' OR ' . $item, count( $condition[4] ) - 1 );
					$where .= "
						)
					";
				}
				$args = array_merge( $args, $condition[4] );
			}
		}

		$sql = $wpdb->prepare( $sql . $join . $where, $args );
		//	$this->log( $sql );
		$rows = $wpdb->get_results( $sql );

		return array_map( function ( $d ) {
			return $d->ID;
		}, $rows );
	}
}

$GLOBALS['ucbr_condition'] = UCBRecommend_Condition::get_instance();
