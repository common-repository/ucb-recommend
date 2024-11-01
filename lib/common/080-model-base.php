<?php
namespace UCBRecommendModel;

if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;

abstract class UCBRecommend_Model_Base extends \UCBRecommendBase\UCBRecommend_Base_Class
{

	abstract protected function get_table();

	private function _get_table()
	{
		global $ucbr_db;
		return $ucbr_db->get_table( $this->get_table() );
	}

	private function type2sig( $type )
	{
		if ( stristr( $type, 'INT' ) !== false )
			return "i";
		if ( stristr( $type, 'BIT' ) !== false )
			return "i";
		if ( stristr( $type, 'BOOLEAN' ) !== false )
			return "i";
		if ( stristr( $type, 'DECIMAL' ) !== false )
			return "d";
		if ( stristr( $type, 'FLOAT' ) !== false )
			return "d";
		if ( stristr( $type, 'DOUBLE' ) !== false )
			return "d";
		if ( stristr( $type, 'REAL' ) !== false )
			return "d";
		return "s";
	}

	private function build_where( $where, $bind, $glue )
	{
		if ( !is_array( $where ) )
			return array( "sql" => "", "bind" => $bind );
		global $ucbr_db;
		$def = $ucbr_db->get_field_defines( $this->get_table() );
		$sql = "";
		foreach ( $where as $arr ) {
			$g = $arr[0];
			$a = $arr[1];
			$first = true;
			$sql2 = false;
			foreach ( $a as $value ) {
				$check = false;
				foreach ( $def as $s => $d ) {
					if ( $s == $value[0] ) {
						$check = $d;
						break;
					}
				}
				if ( $check ) {
					if ( $first )
						$first = false;
					else $sql2 .= $g . " ";
					$sql2 .= $check[0] . " " . $value[1] . " " . $value[2] . " ";
					if ( count( $value ) > 3 ) {
						if ( is_array( $value[3] ) ) {
							if ( count( $value[3] ) <= 0 ) {
								if ( false === strpos( $value[2], '?' ) ) {
									$sql2 .= '(?)';
								}
								$value[3] = array( '' );
							}
							$bind = $ucbr_db->add_binds( $bind, array_fill( 0, count( $value[3] ), $this->type2sig( $check[1] ) ), $value[3] );
						} else {
							$bind = $ucbr_db->add_bind( $bind, $this->type2sig( $check[1] ), $value[3] );
						}
					}
				}
			}
			if ( $sql2 ) {
				if ( $sql )
					$sql .= $glue . " (" . $sql2 . ") ";
				else $sql = " AND ( (" . $sql2 . ") ";
			}
		}
		if ( $sql )
			$sql .= ") ";
		return array( "sql" => $sql, "bind" => $bind );
	}

	private function build_orderby( $orderby )
	{
		if ( !$orderby )
			return "";
		if ( !is_array( $orderby ) )
			$orderby = array( $orderby );
		global $ucbr_db;
		$def = $ucbr_db->get_field_defines( $this->get_table() );
		$items = array();
		foreach ( $orderby as $key => $value ) {
			if ( strtolower( trim( $value ) ) == "desc" )
				$o = "DESC";
			else $o = "ASC";
			$check = false;
			foreach ( $def as $s => $d ) {
				if ( $s == $key ) {
					$check = $d;
					break;
				}
			}
			if ( $check ) {
				$items[] = $check[0] . " " . $o;
			}
		}
		if ( count( $items ) > 0 ) {
			return "ORDER BY " . implode( ", ", $items ) . " ";
		}
		return "";
	}

	public function fetch_all( $where, $orderby = null, $num = null, $offset = null, $forupdate = null, $glue = "AND" )
	{
		if ( is_null( $orderby ) ) {
			$orderby = false;
		}
		if ( is_null( $num ) ) {
			$num = 0;
		}
		if ( is_null( $offset ) ) {
			$offset = 0;
		}
		if ( is_null( $forupdate ) ) {
			$forupdate = false;
		}
		if ( is_null( $glue ) ) {
			$glue = "AND";
		}
		global $ucbr_db;
		$sql = "SELECT * FROM " . $this->_get_table() . " WHERE deleted_at IS NULL ";
		$bind = $ucbr_db->init_bind();
		$tmp = $this->build_where( $where, $bind, $glue );
		$sql .= $tmp["sql"];
		$bind = $tmp["bind"];
		$sql .= $this->build_orderby( $orderby );
		if ( $num > 0 ) {
			$sql .= "LIMIT " . ( $num - 0 ) . " ";
			if ( $offset > 0 )
				$sql .= "OFFSET " . ( $offset - 0 ) . " ";
		}
		if ( $forupdate ) {
			$sql .= "FOR UPDATE ";
		}
		return $this->_select( $sql, $bind );
	}

	public function _fetch_all( $orderby = null, $num = null, $offset = null, $forupdate = null )
	{
		return $this->fetch_all( NULL, $orderby, $num, $offset, $forupdate );
	}

	public function fetch( $where, $orderby = null, $offset = null, $forupdate = null, $glue = "AND" )
	{
		if ( is_null( $orderby ) ) {
			$orderby = false;
		}
		if ( is_null( $offset ) ) {
			$offset = 0;
		}
		if ( is_null( $forupdate ) ) {
			$forupdate = false;
		}
		if ( is_null( $glue ) ) {
			$glue = "AND";
		}
		global $ucbr_db;
		$sql = "SELECT * FROM " . $this->_get_table() . " WHERE deleted_at IS NULL ";
		$bind = $ucbr_db->init_bind();
		$tmp = $this->build_where( $where, $bind, $glue );
		$sql .= $tmp["sql"];
		$bind = $tmp["bind"];
		$sql .= $this->build_orderby( $orderby );
		$sql .= "LIMIT 1 ";
		if ( $offset > 0 )
			$sql .= "OFFSET " . ( $offset - 0 ) . " ";
		if ( $forupdate ) {
			$sql .= "FOR UPDATE ";
		}
		return $this->_fetch( $sql, $bind );
	}

	public function find( $uuid )
	{
		return $this->fetch(
			array(
				"AND" => array(
					array( "uuid", "LIKE", "?", $uuid )
				)
			)
		);
	}

	public function insert( $fields, $ignore = false )
	{
		if ( !is_array( $fields ) )
			$fields = array();
		global $ucbr_db, $ucbr_user;
		$def = $ucbr_db->get_field_defines( $this->get_table() );

		$sql = "INSERT " . ( $ignore ? "IGNORE " : "" ) . "INTO " . $this->_get_table() . " (uuid, ";
		$uuid = $this->uuid();
		$bind = $ucbr_db->init_bind( "s", $uuid );
		$num = 0;
		foreach ( $fields as $key => $value ) {
			$check = false;
			foreach ( $def as $s => $d ) {
				if ( $s == $key ) {
					$check = $d;
					break;
				}
			}
			if ( $check ) {
				$num++;
				$sql .= $check[0] . ", ";
				$bind = $ucbr_db->add_bind( $bind, $this->type2sig( $check[1] ), $value );
			}
		}
		$sql .= "created_at, created_by, updated_at, updated_by) VALUES ";
		$sql .= "(?,";
		$sql .= str_repeat( "?,", $num );
		$sql .= "NOW(),?,NOW(),?) ";
		$bind = $ucbr_db->add_binds( $bind, array( "s", "s" ), array( $ucbr_user->user_name, $ucbr_user->user_name ) );
		$this->_execute( $sql, $bind );
		return $uuid;
	}

	public function insert_all( $fields, $values, $ignore = false )
	{
		if ( !is_array( $fields ) || !is_array( $values ) ) {
			return false;
		}
		if ( count( $fields ) <= 0 || count( $values ) <= 0 ) {
			return true;
		}

		global $ucbr_db, $ucbr_user;
		$def = $ucbr_db->get_field_defines( $this->get_table() );

		$sql = "INSERT " . ( $ignore ? "IGNORE " : "" ) . "INTO " . $this->_get_table() . " (uuid, ";
		$num = 0;
		$types = array();
		foreach ( $fields as $field ) {
			$check = false;
			foreach ( $def as $s => $d ) {
				if ( $s == $field ) {
					$check = $d;
					break;
				}
			}
			if ( $check ) {
				$sql .= $check[0] . ", ";
				$types[$num++] = $check[1];
			}
		}
		$sql .= "created_at, created_by, updated_at, updated_by) VALUES";
		$vsql = " (?," . str_repeat( "?,", $num ) . "NOW(),?,NOW(),?)";
		$bind = $ucbr_db->init_bind();
		$uuids = array();
		$first = true;
		foreach ( $values as $value ) {
			if ( count( $value ) !== $num ) {
				continue;
			}
			if ( $first ) {
				$first = false;
				$sql .= $vsql;
			} else {
				$sql .= "," . $vsql;
			}
			$uuid = $this->uuid();
			$uuids[] = $uuid;
			$bind = $ucbr_db->add_bind( $bind, "s", $uuid );
			$n = 0;
			foreach ( $value as $v ) {
				$bind = $ucbr_db->add_bind( $bind, $this->type2sig( $types[$n++] ), $v );
			}
			$bind = $ucbr_db->add_binds( $bind, array( "s", "s" ), array( $ucbr_user->user_name, $ucbr_user->user_name ) );
		}
		$this->_execute( $sql, $bind );
		return $uuids;
	}

	public function update( $fields, $where, $glue = "AND" )
	{
		if ( !is_array( $fields ) )
			$fields = array();
		global $ucbr_db, $ucbr_user;
		$def = $ucbr_db->get_field_defines( $this->get_table() );

		$sql = "UPDATE " . $this->_get_table() . " SET ";
		$bind = $ucbr_db->init_bind();
		foreach ( $fields as $key => $value ) {
			$check = false;
			foreach ( $def as $s => $d ) {
				if ( $s == $key ) {
					$check = $d;
					break;
				}
			}
			if ( $check ) {
				if ( is_array( $value ) ) {
					$sql .= $check[0] . "=" . implode( $value ) . ", ";
				} else {
					$sql .= $check[0] . "=?, ";
					$bind = $ucbr_db->add_bind( $bind, $this->type2sig( $check[1] ), $value );
				}
			}
		}
		$sql .= "updated_at=NOW(), updated_by=? ";
		$bind = $ucbr_db->add_bind( $bind, "s", $ucbr_user->user_name );
		$sql .= "WHERE deleted_at IS NULL ";
		$tmp = $this->build_where( $where, $bind, $glue );
		$sql .= $tmp["sql"];
		$bind = $tmp["bind"];
		return $this->_execute( $sql, $bind );
	}

	public function insert_or_update( $fields, $where, $glue = "AND" )
	{
		if ( $this->fetch( $where, false, 0, false, $glue ) ) {
			return $this->update( $fields, $where, $glue );
		}
		return $this->insert( $fields );
	}

	public function delete( $where, $glue = "AND" )
	{
		if ( !is_array( $where ) )
			$where = array();
		global $ucbr_db, $ucbr_user;

		$sql = "UPDATE " . $this->_get_table() . " SET ";
		$sql .= "deleted_at=NOW(), deleted_by=? ";
		$bind = $ucbr_db->init_bind( "s", $ucbr_user->user_name );
		$sql .= "WHERE deleted_at IS NULL ";
		$tmp = $this->build_where( $where, $bind, $glue );
		$sql .= $tmp["sql"];
		$bind = $tmp["bind"];
		return $this->_execute( $sql, $bind );
	}

	public function clear( $where, $glue = "AND" )
	{
		if ( !is_array( $where ) )
			$where = array();
		global $ucbr_db, $ucbr_user;

		$bind = $ucbr_db->init_bind();
		$sql = "DELETE FROM " . $this->_get_table() . " ";
		$sql .= "WHERE 1=1 ";
		$tmp = $this->build_where( $where, $bind, $glue );
		$sql .= $tmp["sql"];
		$bind = $tmp["bind"];
		return $this->_execute( $sql, $bind );
	}

	public function execute( $sql, $bind )
	{
		global $ucbr_db;
		return $ucbr_db->execute( $sql, $bind, $this->get_table(), __FILE__, __LINE__ );
	}

	public function count( $where, $glue = "AND" )
	{
		global $ucbr_db;
		$sql = "SELECT COUNT(*) as num FROM " . $this->_get_table() . " WHERE deleted_at IS NULL ";
		$bind = $ucbr_db->init_bind();
		$tmp = $this->build_where( $where, $bind, $glue );
		$sql .= $tmp["sql"];
		$bind = $tmp["bind"];
		$result = $this->_fetch( $sql, $bind );
		return $result->num;
	}

	public function count_all()
	{
		return $this->count( NULL );
	}

	public function _execute( $sql, $bind )
	{
		global $ucbr_db;
		return $ucbr_db->execute( $sql, $bind, $this->get_table(), __FILE__, __LINE__ );
	}

	public function _select( $sql, $bind )
	{
		global $ucbr_db;
		return $ucbr_db->fetch_all( $sql, $bind, $this->get_table(), __FILE__, __LINE__ );
	}

	public function _fetch( $sql, $bind )
	{
		global $ucbr_db;
		return $ucbr_db->fetch( $sql, $bind, $this->get_table(), __FILE__, __LINE__ );
	}

	public function get_value( $result, $field )
	{
		global $ucbr_db;
		return $ucbr_db->get_value( $result, $this->get_table(), $field );
	}

	public function get_in_placeholder( $items )
	{
		if ( count( $items ) <= 0 ) {
			return '';
		}
		return '(?' . str_repeat( ',?', count( $items ) - 1 ) . ')';
	}

	public static function get_prefix()
	{
		return "ucbr_model_";
	}

	public static function get_slug( $file )
	{
		return str_replace( "-", "_", preg_replace( "/^(.+)\\.php$/", "$1", basename( $file ) ) );
	}

	public static function get_name( $file )
	{
		return self::get_prefix() . self::get_slug( $file );
	}

}
