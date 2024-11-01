<?php
if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;
global $ucbr_minify;
$ucbr_minify->register_css( <<< EOS
.ucbr-submit{
	float:right;
	margin-top:10px!important;
}
#ucbr-widget-menu{
	position: fixed;
	top: 45px;
	right: 10px;
	background: #eee;
	border: 1px solid #999;
	padding: 10px;
	box-shadow: 3px 3px 3px #555!important;
}
.ucbr-now-loading{
	display: inline-block;
	margin: 5px;
}
.ucbr-now-loading-img{
	height: 15px;
	margin-left: 3px;
}
.nav-tab{
	cursor: pointer;
}
#ucbr-board{
	margin: 10px;
	line-height: 1.6em;
}
#ucbr-board textarea,
#ucbr-board input[type="text"]{
	width: 90%;
}
#ucbr-board input[type="text"]#ucbr-add-condition-group-name{
	width: 200px;
}
#ucbr-board input[type="text"].ucbr-add-object-value{
	width: 200px;
}
#ucbr-board input[type="text"].ucbr-delete-object-label{
	width: 200px;
}
.ucbr-condition{
	padding: 10px;
	border: solid 1px #999;
}
#ucbr-main-contents .ucbr-objects-wrap input[type="button"]{
	margin: 2px 0;
}
.ucbr-objects-wrap ul{
	display: inline-block;
}
.ucbr-objects-wrap{
	vertical-align: middle;
}
.ucbr-condition span{
	display: inline-block;
}
#ucbr-condition-group,
.ucbr-delete-object-label{
	text-align: center;
}
.ucbr-objects-wrap{
	border: solid 1px #999;
	padding: 5px 10px;
	margin: 0 5px;
}
#ucbr-main-contents #ucbr-condition-group > ul{
	margin-bottom: 0;
}
#ucbr-main-contents #ucbr-add-condition-button{
	margin-top: 0;
}
#ucbr-add-condition-group-wrap{
	display: inline-block;
	padding: 10px;
	border: 1px solid #999;
	vertical-align: middle;
}
#ucbr-preview-iframe {
	width: 100%;
	background: white;
	border: 1px solid #999;
}
#ucbr-post-ids{
	border: 1px solid #999;
	margin-top: 25px;
}
#ucbr-get-post-ids-id,
#ucbr-get-post-ids-number {
	width: 80px;
}
#ucbr-post-ids-result table {
	margin: 10px auto;
	border: 1px solid #999;
	padding: 1px;
}
#ucbr-post-ids-result td,
#ucbr-post-ids-result th {
	padding: 5px 15px;
	border: 1px solid #999;
}
#ucbr-post-ids-result th {
	background: #bdbdbd;
}
#ucbr-main-contents input[type="button"]#ucbr-close-menu-button {
	float: right;
}
#ucbr-main-contents input[type="button"]#ucbr-close-menu-button.ucbr-closed {
	margin: 0;
}
.ucbr-widget-menu-item,
#ucbr-close-menu,
#ucbr-message {
	float: right;
	clear: both;
}
#ucbr-main-contents #ucbr-widget-menu input[type="button"] {
	width: 10em;
}
#ucbr-design-preview-iframe{
	width: 100%;
}
#ucbr-histories-total {
	padding: 10px;
	margin: 5px;
}
EOS
);
?>
<h2><?php _e( "Widget settings", UCB_RECOMMEND_TEXT_DOMAIN ); ?></h2>
<div id="ucbr-main-contents">
	<div id="ucbr-main">

	</div>
	<div id="ucbr-widget-menu">
		<div id="ucbr-select-widget" class="ucbr-widget-menu-item"></div>
		<div id="ucbr-new-widget" class="ucbr-widget-menu-item">
			<input type="text" id="ucbr-new-widget-name" placeholder="<?php _e( "Widget name", UCB_RECOMMEND_TEXT_DOMAIN ); ?>">
			<input type="button" value="<?php _e( "Create", UCB_RECOMMEND_TEXT_DOMAIN ); ?>" id="ucbr-new-widget-button">
		</div>
		<div id="ucbr-widget-menu-general-item" class="ucbr-widget-menu-item"></div>
		<div id="ucbr-close-menu">
			<input type="button" value="<?php _e( "Close", UCB_RECOMMEND_TEXT_DOMAIN ); ?>" id="ucbr-close-menu-button">
		</div>
		<div id="ucbr-message"></div>
	</div>
</div>

