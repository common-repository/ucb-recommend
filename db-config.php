<?php
if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;

global $ucbr_db;

//test
$ucbr_db->add_table(
	'test', 'test',
	array(
		'widget_id' => array( 'widget_id', 'VARCHAR(32)', 'NOT NULL' ),
		'post_id' => array( 'post_id', 'BIGINT(20)', 'NOT NULL' ),
		'context' => array( 'context', 'BIGINT(20)', 'NOT NULL' ),
		'hash' => array( 'hash', 'VARCHAR(64)', 'NOT NULL' ),
	),
	array( 'hash' )
);

//number
$ucbr_db->add_table(
	'number', 'number',
	array(
		'widget_id' => array( 'widget_id', 'VARCHAR(32)', 'NOT NULL' ),
		'post_id' => array( 'post_id', 'BIGINT(20)', 'NOT NULL' ),
		'context' => array( 'context', 'BIGINT(20)', 'NOT NULL' ),
		'number' => array( 'number', 'INT(11)', 'NOT NULL' ),
		'clicked' => array( 'clicked', 'INT(11)', 'NOT NULL' )
	),
	array( 'widget_id', 'post_id', 'context' )
);

//widget
$ucbr_db->add_table(
	'widget', 'widget',
	array(
		'id' => array( 'id', 'INT(11)', 'NOT NULL' ),
		'name' => array( 'name', 'VARCHAR(255)', 'NOT NULL' ),
	),
	array( 'id' )
);

//condition
$ucbr_db->add_table(
	'join_table', 'join_table',
	array(
		'widget_id' => array( 'widget_id', 'VARCHAR(32)', 'NOT NULL' ),
		'join_order' => array( 'join_order', 'INT(11)', 'NOT NULL' ),
		'type' => array( 'type', 'INT(11)', 'NOT NULL' ),
		'table1' => array( 'table1', 'VARCHAR(255)', 'NOT NULL' ),
		'column1' => array( 'column1', 'VARCHAR(255)', 'NOT NULL' ),
		'table2' => array( 'table2', 'VARCHAR(255)', 'NOT NULL' ),
		'column2' => array( 'column2', 'VARCHAR(255)', 'NOT NULL' ),
	),
	array( 'widget_id' )
);
$ucbr_db->add_table(
	'condition_group', 'condition_group',
	array(
		'name' => array( 'name', 'VARCHAR(255)', 'NOT NULL' ),
		'widget_id' => array( 'widget_id', 'VARCHAR(32)', 'NOT NULL' ),
	),
	array( 'widget_id' )
);
$ucbr_db->add_table(
	'condition', 'condition',
	array(
		'group_id' => array( 'group_id', 'VARCHAR(32)', 'NOT NULL' ),
		'table' => array( 'table_name', 'VARCHAR(255)', 'NOT NULL' ),
		'column' => array( 'column_name', 'VARCHAR(255)', 'NOT NULL' ),
		'verb' => array( 'verb', 'INT(11)', 'NOT NULL' ),
	),
	array( 'group_id' )
);
$ucbr_db->add_table(
	'object', 'object',
	array(
		'condition_id' => array( 'condition_id', 'VARCHAR(32)', 'NOT NULL' ),
		'value' => array( 'value', 'TEXT', 'NOT NULL' ),
	),
	array( 'condition_id' )
);

