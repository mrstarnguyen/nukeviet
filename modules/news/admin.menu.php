<?php

/**
 * @Project NUKEVIET 3.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2013 VINADES.,JSC. All rights reserved
 * @createdate 07/30/2013 10:27
 */

if( ! defined( 'NV_ADMIN' ) ) die( 'Stop!!!' );

if( ! function_exists('nv_news_array_cat_admin') )
{
	/**
	 * nv_news_array_cat_admin()
	 *
	 * @return
	 */
	function nv_news_array_cat_admin( $module_data )
	{
		global $db;

		$array_cat_admin = array();
		$sql = "SELECT * FROM `" . NV_PREFIXLANG . "_" . $module_data . "_admins` ORDER BY `userid` ASC";
		$result = $db->sql_query( $sql );

		while( $row = $db->sql_fetchrow( $result ) )
		{
			$array_cat_admin[$row['userid']][$row['catid']] = $row;
		}

		return $array_cat_admin;
	}
}

$is_refresh = false;
$array_cat_admin = nv_news_array_cat_admin( $module_data );

if( ! empty( $module_info['admins'] ) )
{
	$module_admin = explode( ",", $module_info['admins'] );
	foreach( $module_admin as $userid_i )
	{
		if( ! isset( $array_cat_admin[$userid_i] ) )
		{
			$db->sql_query( "INSERT INTO `" . NV_PREFIXLANG . "_" . $module_data . "_admins` (`userid`, `catid`, `admin`, `add_content`, `pub_content`, `edit_content`, `del_content`, `comment`) VALUES ('" . $userid_i . "', '0', '1', '1', '1', '1', '1', '1')" );
			$is_refresh = true;
		}
	}
}
if( $is_refresh )
{
	$array_cat_admin = nv_news_array_cat_admin( $module_data );
}

$admin_id = $admin_info['admin_id'];
$NV_IS_ADMIN_MODULE = false;
$NV_IS_ADMIN_FULL_MODULE = false;
if( defined( 'NV_IS_SPADMIN' ) )
{
	$NV_IS_ADMIN_MODULE = true;
	$NV_IS_ADMIN_FULL_MODULE = true;
}
else
{
	if( isset( $array_cat_admin[$admin_id][0] ) )
	{
		$NV_IS_ADMIN_MODULE = true;
		if( intval( $array_cat_admin[$admin_id][0]['admin'] ) == 2 )
		{
			$NV_IS_ADMIN_FULL_MODULE = true;
		}
	}
}

$allow_func = array( 'main', 'exptime', 'publtime', 'waiting', 'declined', 're-published', 'content', 'rpc', 'del_content', 'comment', 'edit_comment', 'active_comment', 'del_comment', 'keywords', 'alias', 'topicajax', 'sourceajax', 'cat', 'change_cat', 'list_cat', 'del_cat' );

$submenu['cat'] = $lang_module['categories'];
$submenu['content'] = $lang_module['content_add'];
$submenu['comment'] = $lang_module['comment'];

if( $NV_IS_ADMIN_MODULE )
{
	$submenu['topics'] = $lang_module['topics'];
	$submenu['groups'] = $lang_module['block'];
	$submenu['sources'] = $lang_module['sources'];
	$submenu['setting'] = $lang_module['setting'];

	$allow_func[] = 'topicsnews';
	$allow_func[] = 'topics';
	$allow_func[] = 'topicdelnews';
	$allow_func[] = 'addtotopics';
	$allow_func[] = 'change_topic';
	$allow_func[] = 'list_topic';
	$allow_func[] = 'del_topic';

	$allow_func[] = 'sources';
	$allow_func[] = 'change_source';
	$allow_func[] = 'list_source';
	$allow_func[] = 'del_source';

	$allow_func[] = 'block';
	$allow_func[] = 'groups';
	$allow_func[] = 'del_block_cat';
	$allow_func[] = 'list_block_cat';
	$allow_func[] = 'chang_block_cat';
	$allow_func[] = 'change_block';
	$allow_func[] = 'list_block';

	$allow_func[] = 'setting';
}

if( file_exists( NV_ROOTDIR . "/modules/" . $module_file . "/admin/admins.php" ) )
{
	$submenu['admins'] = $lang_module['admin'];
	$allow_func[] = 'admins';
}

?>