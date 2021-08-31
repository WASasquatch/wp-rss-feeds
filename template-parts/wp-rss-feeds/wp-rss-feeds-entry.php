	<li class="wp-rss-feed-entry">
		<div class="wp-rss-feed-entry-title"><a class="wp-rss-feed-entry-title-link" href="<?php echo $feed['link']; ?>" target="<?php echo $target; ?>" title="<?php echo $feed['title']; ?>"><?php echo $feed['title']; ?></a></div>
			<div class="wp-rss-feed-entry-sub">
				Posted by <span class="wp-rss-feed-entry-creator"><?php echo $feed['creator']; ?></span> 
				<span class="wp-rss-feed-entry-site">(<?php echo $feed['site']; ?>)</span> <?php if ( ! ( empty( $feed['category'] ) ) ) { ?>in <span class="wp-rss-feed-entry-category"><?php echo $feed['category']; ?></span><?php } ?> on
				<span class="wp-rss-feed-entry-date"><?php echo $date->format( $dateformat ); ?></span>
			</div>
<?php if ( ! ( empty( $feed['image'] ) ) ) { ?>
			<div class="wp-rss-feed-entry-image-container"><a href="<?php echo $feed['link']; ?>" title="<?php echo $feed['title']; ?>"><img class="wp-rss-feed-entry-image" src="<?php echo $feed['image']; ?>" alt="<?php echo $feed['title']; ?>>" /></a></div>
<?php } ?>
			<div class="wp-rss-feed-entry-description"><?php echo $desc ?></div>
	</li>