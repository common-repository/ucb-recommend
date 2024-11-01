<?php
if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;
?>
<style>
	/*
	Eric Meyer's CSS Reset
	http://meyerweb.com/eric/tools/css/reset/
	v1.0 | 20080212
	CSSresetr.com
	*/
	html, body, div, span, applet, object, iframe,
	h1, h2, h3, h4, h5, h6, p, blockquote, pre,
	a, abbr, acronym, address, big, cite, code,
	del, dfn, em, font, img, ins, kbd, q, s, samp,
	small, strike, strong, sub, sup, tt, var,
	b, u, i, center,
	dl, dt, dd, ol, ul, li,
	fieldset, form, label, legend,
	table, caption, tbody, tfoot, thead, tr, th, td {
		margin: 0;
		padding: 0;
		border: 0;
		outline: 0;
		font-size: 100%;
		vertical-align: baseline;
		background: transparent;
	}

	body {
		line-height: 1;
	}

	ol, ul {
		list-style: none;
	}

	blockquote, q {
		quotes: none;
	}

	blockquote:before, blockquote:after,
	q:before, q:after {
		content: '';
		content: none;
	}

	/* remember to define focus styles! */
	:focus {
		outline: 0;
	}

	/* remember to highlight inserts somehow! */
	ins {
		text-decoration: none;
	}

	del {
		text-decoration: line-through;
	}

	/* tables still need 'cellspacing=0' in the markup */
	table {
		border-collapse: collapse;
		border-spacing: 0;
	}

	/*ucbr default*/
	.ucbr-loading-widget {
		text-align: center;
		height: 50px;
	}

	.ucbr-loading-widget img {
		height: 100%;
	}

	h2 {
		margin-bottom: 10px;
		font-size: 1.5em;
	}

	.ucbr-hover-card {
		cursor: pointer;
		text-decoration: none;
		display: block;
		box-shadow: none;
	}

	.ucbr-hover-card:hover {
		background-color: #f7f7f7;
	}

	.ucbr-related-entry {
		border-bottom: 1px solid #F0F0F0;
		padding-bottom: 10px;
		line-height: 150%;
		margin-right: 10px;
		margin-bottom: 0;
		padding-top: 10px;
		clear: both;
	}

	.ucbr-related-entry-thumb {
		float: left;
		margin-top: 3px;
		margin-bottom: 5px;
		padding-bottom: 5px;
	}

	.ucbr-related-entry-thumb a {
		display: block;
	}

	.ucbr-related-entry-thumb img {
		border: 0;
		width: 100px;
		height: 100px;
		display: block;
	}

	.ucbr-related-entry-content {
		margin-left: 110px;
	}

	.ucbr-related-entry-title {
		margin-bottom: 5px;
		clear: none;
	}

	.ucbr-related-entry-title a {
		text-decoration: none;
		font-size: 18px;
		font-weight: bold;
		color: #333;
	}

	.ucbr-related-entry-snippet {
		margin: 0 0 5px 0;
		color: #555;
		word-wrap: break-word;
	}

	.ucbr-related-entry-title {
		margin-top: 0;
	}

	.clearfix:after {
		content: ".";
		display: block;
		clear: both;
		height: 0;
		visibility: hidden;
	}

	.clearfix {
		display: inline-block;
	}

	* html .clearfix {
		height: 1%;
	}

	.clearfix {
		display: block;
	}
</style>
