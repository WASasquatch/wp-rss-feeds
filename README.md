# wp-rss-feeds
Aggregate multiple RSS feeds by using shortcode within your WordPress posts or content. 

**Please note:** this is in development software (and may remain in such a state indefinitely). Please do not install this on a WordPress installation if you *do not* have access to FTP or a file manager through cPanel. If a critical error occurs for any reason, you will not have access to WordPress Admin to fix the issue. 

# Requirements
- PHP 7.4+ required
- SimpleXML module (php-xml package) 
- WordPress 5.5+

# Installation
Upload the files to your theme directories base. 

Inside your functions.php, include wp-rss-feeds.php at the end of your file
```
// Inlcude WP RSS Feeds
include( get_template_directory() . '/wp-rss-feeds.php' );
```

# Usage
In your posts, or content you can call `[wp-rss-feeds feeds="URL"]` where `URL` is a list or single URL to a RSS feed.

The following is a list of attributes and their examples or defaults. Attributes denoted by ▫️ are optional.

| Attribute | Description | Default / Example |
| :---         |     :---:      |          ---: |
| `feeds`   | A RSS URL, or RSS URL list seperated by commas.     | `https://example.com/rss` or `https://example.com/rss,https://example2.com/rss`    |
| `entrylimit` ▫️    | RSS entries will be limited to this integer.       | `30`      |
| `charlimit` ▫️    | RSS description character limit, followed by read more link. `0` is no limit.       | `0`      |
| `fullcat` ▫️    | Display full category path `0` will only show current, or last category, `1` is full path.       | `0`      |
| `order` ▫️    | Order RSS entries by ascending, or descending. Default `0` is ascending, `1` is descending.       | `0`      |
| `timeout` ▫️    | RSS cache timeout in hours       | `4`      |
| `timezone` ▫️    | The PHP compatible time zone ID to use with RSS entries publish date. Default is `server` which is the server's time zone.       | `America/New_York`      |
| `dateformat` ▫️    | PHP `DateTime` compatible date format.       | `D, dS F Y g:i:s A`      |
| `dofutureposts` ▫️    | If encountering RSS entries published in the future, discard or display them. Default is discard.       | `0`      |
| `fallback` ▫️    | Fallback to retrieving live feeds from feeds list. Default `false`       | `0`      |
| `tmp` ▫️    | Path to the directory to store RSS cache. Default is `get_temp_dir() . 'rss/'` in WordPress cache location.      | `/path/to/tmp/`     |

## Example
```
[wp-rss-feeds feeds="https://www.blender.org/feed/,http://pixologic.com/blog/feed/" charlimit="300"]
```
