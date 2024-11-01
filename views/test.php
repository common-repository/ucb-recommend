<?php
if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;
global $ucbr_minify;
$ucbr_minify->register_css( <<< EOS
.ucbr-now-loading{
	display: inline-block;
	margin: 5px;
}
.ucbr-now-loading-img{
	height: 15px;
	margin-left: 3px;
}
.ucbr-admin-message input[type="button"],
.ucbr-admin-message .ucbr-button,
#ucbr-test input[type="button"]{
	min-width: 100px;
	border: solid 2px #727272;
	box-shadow: #aaa 3px 3px 2px 2px;
	cursor: pointer;
	padding: 5px 30px;
	margin: 10px 5px;
	height: auto;
}
.ucbr-ng{
	color: red;
}
.ucbr-ok{
	color: green;
}
EOS
);
$button_value = $retest ? __( 'Retest', UCB_RECOMMEND_TEXT_DOMAIN ) : __( 'Test', UCB_RECOMMEND_TEXT_DOMAIN );
?>
<strong><?php echo UCB_RECOMMEND_PLUGIN_NAME; ?></strong>:
<input type="button" value="<?php echo esc_attr( $button_value ); ?>" id="ucbr-test-button">

<div id="ucbr-test-wrap" hidden="hidden" style="display:none"></div>

