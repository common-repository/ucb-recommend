<?php
if ( !defined( 'UCB_RECOMMEND_PLUGIN' ) )
	exit;
?>
<a class="ucbr-hover-card" href="{$url}">
	<article class="ucbr-related-entry clearfix">
		<div class="ucbr-related-entry-thumb">
			<img width="100" height="100" src="{$thumbnail{thumb100}}" class="ucbr-related-entry-thumb-image wp-post-image" alt="{$post->post_title}">
		</div>

		<div class="ucbr-related-entry-content">
			<header>
				<h3 class="ucbr-related-entry-title">
					{$post->post_title}
				</h3>
			</header>
			<p class="ucbr-related-entry-snippet">
				{$post->post_excerpt}
			</p>
		</div>
	</article>
</a>
