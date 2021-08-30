# wp-rss-feeds
Aggregate multiply RSS feeds by using shortcode within your WordPress posts or content. 

# Installation
Upload the files to your theme directories base. 

Inside your functions.php, include wp-rss-feeds.php
```
// Inlcude WP RSS Feeds
include( get_template_directory() . '/wp-rss-feeds.php' );
```

# Usage
In your posts, or content you can call the shortcode **[wp-rss-feed feeds="URL_LIST" ( entrylimit="30" charlimit="0" timeout="4" timezone="America/New_York" dateformat="D, dS F Y H:i:s A" dofutureposts="0" )]** where attributes in parentheses are optional. 
