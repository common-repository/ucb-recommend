<?php
if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;

?>
<style>
	.ucbr-loading {
		background: url(<?php echo $loading_image;?>);
		background-size: contain;
		background-repeat: no-repeat;
		text-align: center;
		margin: 0 auto;
		height: 30px;
		width: 30px;
		display: inline-block;
		vertical-align: middle;
	}

	#ucbr-modal {
		background: url(<?php echo $back_file;?>);
		background-size: cover;
		position: fixed;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		z-index: 10000;
	}

	#ucbr-modal .ucbr-loading {
		display: none;
		position: fixed;
		top: 50%;
		left: 50%;
		margin-top: -15px;
		margin-left: -15px;
	}

	#ucbr-modal .ucbr-loading-message {
		display: none;
		position: fixed;
		top: 50%;
		color: white;
		margin-top: 25px;
		width: 100%;
		text-align: center;
		max-height: 90%;
	}

	#ucbr-modal-message-warp {
		position: fixed;
		display: inline-block;
		color: black;
		width: 100%;
		max-height: 90%;
		z-index: 10001;
		overflow-y: scroll;
		text-align: center;
		top: 50%;
	}

	#ucbr-modal-message-warp #ucbr-modal-message {
		background: white;
		display: inline-block;
		color: black;
		padding: 20px;
	}
</style>
