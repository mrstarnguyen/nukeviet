<?php

/**
 * @Project NUKEVIET 3.x
 * @Author VINADES.,JSC (contact@vinades.vn)
 * @Copyright (C) 2012 VINADES.,JSC. All rights reserved
 * @Createdate 3/24/2010 23:58
 */

if( ! defined( 'NV_IS_MOD_BANNERS' ) ) die( 'Stop!!!' );

if( defined( 'NV_IS_BANNER_CLIENT' ) ) die( '&nbsp;' );

if( $nv_Request->get_int( 'save', 'post' ) == '1' )
{
	$login = strip_tags( $nv_Request->get_string( 'login', 'post', '' ) );
	$password = strip_tags( $nv_Request->get_string( 'password', 'post', '' ) );

	if( $global_config['gfx_chk'] )
	{
		$seccode = strip_tags( $nv_Request->get_string( 'seccode', 'post', '' ) );
	}

	$check_login = nv_check_valid_login( $login, NV_UNICKMAX, NV_UNICKMIN );
	$check_pass = nv_check_valid_pass( $password, NV_UPASSMAX, NV_UPASSMIN );

	if( ! empty( $check_login ) ) die( 'action' );
	elseif( ! empty( $check_pass ) ) die( 'action' );
	elseif( $global_config['gfx_chk'] and ! nv_capcha_txt( $seccode ) ) die( 'action' );
	else
	{
		$sql = "SELECT * FROM `" . NV_BANNERS_GLOBALTABLE. "_clients` WHERE `login` = " . $db->dbescape( $login ) . " AND `act`=1";
		$result = $db->sql_query( $sql );
		$numrows = $db->sql_numrows( $result );

		if( $numrows != 1 )
		{
			die( 'action' );
		}
		else
		{
			$row = $db->sql_fetchrow( $result );
			$db->sql_freeresult( $result );

			if( ! $crypt->validate( $password, $row['pass'] ) )
			{
				die( 'action' );
			}
			else
			{
				$checknum = $crypt->hash( nv_genpass( 10 ) );
				$current_login = NV_CURRENTTIME;
				$id = intval( $row['id'] );
				$agent = substr( NV_USER_AGENT, 0, 254 );
				$sql = "UPDATE `" . NV_BANNERS_GLOBALTABLE. "_clients` SET `check_num` = " . $db->dbescape( $checknum ) . ", `last_login` = " . $current_login . ", `last_ip` = " . $db->dbescape( $client_info['ip'] ) . ", `last_agent` = " . $db->dbescape( $agent ) . " WHERE `id`=" . $id;
				if( ! $db->sql_query( $sql ) ) die( 'action' );
				$client = array(
					'login' => $login,
					'checknum' => $checknum,
					'current_agent' => $agent,
					'last_agent' => $row['last_agent'],
					'current_ip' => $client_info['ip'],
					'last_ip' => $row['last_ip'],
					'current_login' => $current_login,
					'last_login' => intval( $row['last_login'] )
				);
				$client = serialize( $client );
				$nv_Request->set_Cookie( 'bncl', $client, NV_LIVE_COOKIE_TIME );
				echo "OK";
				exit();
			}
		}
	}
}

$contents = array();
$contents['client_info'] = sprintf( $lang_module['client_info'], NV_BASE_SITEURL . "index.php?" . NV_LANG_VARIABLE . "=" . NV_LANG_DATA . "&amp;" . NV_NAME_VARIABLE . "=contact" );
$contents['login'] = $lang_module['login'];
$contents['login_input_name'] = "lg_iavim";
$contents['login_input_maxlength'] = NV_UNICKMAX;
$contents['password'] = $lang_module['password'];
$contents['pass_input_name'] = "pw_iavim";
$contents['pass_input_maxlength'] = NV_UPASSMAX;
$contents['gfx_chk'] = $global_config['gfx_chk'];
$contents['captcha'] = $lang_global['securitycode'];
$contents['captcha_name'] = "seccode_iavim";
$contents['captcha_img'] = NV_BASE_SITEURL . "index.php?scaptcha=captcha&cch=" . nv_genpass( 10 );
$contents['captcha_maxlength'] = NV_GFX_NUM;
$contents['captcha_refresh'] = $lang_global['captcharefresh'];
$contents['captcha_refr_src'] = NV_BASE_SITEURL . "images/refresh.png";
$contents['submit'] = $lang_global['loginsubmit'];
$contents['sm_button_name'] = "sm_button";
$contents['sm_button_onclick'] = "nv_cl_login_submit(" . NV_UNICKMAX . ", " . NV_UNICKMIN . ", " . NV_UPASSMAX . ", " . NV_UPASSMIN . ", " . NV_GFX_NUM . ", " . $global_config['gfx_chk'] . ",'lg_iavim','pw_iavim','seccode_iavim','sm_button');";

$contents = logininfo_theme( $contents );

include ( NV_ROOTDIR . '/includes/header.php' );
echo $contents;
include ( NV_ROOTDIR . '/includes/footer.php' );

?>