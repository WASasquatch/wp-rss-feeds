# wp-rss-feeds
Aggregate multiple RSS feeds by using shortcode within your WordPress posts or content. 

# Requirements
WordPress 5.5+ and SimpleXML module (php-xml package). I am not positive what PHP version would be required, I am assuming 5.6+

# Installation
Upload the files to your theme directories base. 

Inside your functions.php, include wp-rss-feeds.php
```
// Inlcude WP RSS Feeds
include( get_template_directory() . '/wp-rss-feeds.php' );
```

# Usage
In your posts, or content you can call the shortcode; where attributes in parentheses are optional. 

```
[wp-rss-feed feeds="URL_LIST" ( entrylimit="30" charlimit="0" timeout="4" timezone="America/New_York" dateformat="D, dS F Y H:i:s A" dofutureposts="0" )]
```

## Example
```
[wp-rss-feeds feeds="https://www.blender.org/feed/,http://pixologic.com/blog/feed/" charlimit="300"]
```
