<?php
	
	// Register WP RSS Feeds Stylesheet Last (PHP_INT_MAX)
	add_action('wp_enqueue_scripts', 'wp_rss_feeds_style', PHP_INT_MAX);

	// Register WP RSS Feeds
	add_shortcode( "wp-rss-feeds", "wp_rss_feeds_function" );

	function wp_rss_feeds_function( $atts ) {

		$atts = shortcode_atts(array(
			 'feeds' => '',
			 'entrylimit' => 30,
			 'charlimit' => 0,
			 'fullcat' => 0,
			 'order' => 0,
			 'target' => '_blank',
			 'timeout' => 4,
			 'timezone' => 'server',
			 'dateformat' => 'D, dS F Y g:i:s A',
			 'dofutureposts' => 0,
			 'fallback' => 0,
			 'tmp' => get_temp_dir() . 'rss/'
		), $atts);
		extract( $atts );

		// Cast integer attr (is this necessary?)
		$entrylimit = (int) $entrylimit;
		$charlimit = (int) $charlimit;
		$fullcat = (int) $fullcat;
		$order = (int) $order;
		$target = ( in_array( $target, array('_blank','_self','_parent','_top') ) ) ? $target : '_blank';
		$timeout = (int) $timeout;
		$dofutureposts = (int) $dofutureposts;
		$fallback = (int) $fallback;
		
		$template_dir = get_template_directory();
		$time = time();
		$feedfiles = array();
		$feedsarray = array();
		$fallback = false;

		// Any feeds available?
		if ( $feeds == '' )
			return 'No RSS feed(s) supplied.';
		
		// Feeds list as array
		$feeds = explode( ',', $feeds );
		
		// Setup timezone
		if ( $timezone == 'server' ) {
			
			$timezone = new DateTimeZone( date_default_timezone_get() );
			
		} else {
			
			if ( ! ( isValidTimezone( $timezone ) ) ) {
				
				$timezone = new DateTimeZone( date_default_timezone_get() );
				
			} else {
				
				$timezone = new DateTimeZone($timezone);
				
			}
			
		}

		// Cache directory available?
		if ( ! ( is_dir ( $tmp ) ) )
			if ( ! ( @mkdir( $tmp ) ) )
				$fallback = true;

		// Setup SimpleXML Object Files
		foreach ( $feeds as $f ) {
			
			// Cache file
			$cache = $tmp . 'rss-' . md5( $f ) . '.dat';
			
			// Multiply hours by milliseconds
			$ctm = ( (int) $ct * 3600 );

			// Load live or from cache
			if ( $ctm > 0 && is_file( $cache ) && ( filemtime($cache) + $ctm > time() ) && ! ( $fallback ) ) {
			
				$rss = @simplexml_load_file( $cache );
			
			} else {
			
				$xml = @file_get_contents( $f );
				if ( $xml !== false ) {
					$rss = @simplexml_load_string( $xml );
					if ( $ctm > 0 && ! ( $fallback ) && $rss !== false )
						$rss->asXML( $cache );
				}
			
			}
				
			if ( $rss !== false )
				array_push($feedfiles, $rss);
			
		}

		foreach ( $feedfiles as $v ) {
			
			// Channel Title
			$title = sanitize_text_field( (string) $v->channel->title );
			
			// Iterate through items
			if ( isset( $v->channel->item ) ) {
				
				foreach ( $v->channel->item as $item ) {
					
					$description = wp_kses( $item->description, array(
						'a' => array(
							'href' => array(),
							'title' => array()
						),
						'img' => array(
							'src' => array(),
							'alt' => array()
						),
						'b' => array(),
						'strong' => array(),
						'i' => array(),
						'u' => array(),
						'em' => array(),
						'br' => array()
					) );
					
					if ( ! ( empty( $description ) ) ) {
						
						// Extract first image
						preg_match('/<img.+src=[\'"](?P<src>.+?)[\'"].*>/i', $description, $image);
						$src = $image['src'];
						
						// Remove Image Links
						$desc = preg_replace( '/(<a.*?<img.*?>.*?<\/a>)/', '', $description );
						
						// Add link targets
						$desc = str_replace( '<a ', '<a target="' . $target . '" ', $desc );
						
					}
					
					$dateTime = strtotime( $item->pubDate );
					
					// Extract creator or use channel title
					$creator = ( isset( $item->children('dc', true)->creator ) ) ? (string) $item->children('dc', true)->creator : $title;
					
					// Define Categories
					$category = null;
					
					if ( ! ( $fullcat ) ) {

						$category = (string) $item->category[ count( $item->category )-1 ];
					
					} else {
						
						if ( count( $item->category ) > 0 ) {
							
							$i = 0;
							
							foreach ( $item->category as $cat ) {
								
								$suffix = '';
								
								if ( $i < count( $item->category )-1 )
									$suffix = ' > ';
								
								// No runaway duplicate categories
								if ( strpos( $category, (string) $cat ) == false )
									$category .= sanitize_text_field( (string) $cat ) . $suffix;
								
								$i++;
								
							}
							
						} else {
							
							$category = $title;
							
						}
						
					}
					
					// Define Item Array
					$item = array(
					  
						'site'  => $title,
						'title' => sanitize_text_field( (string) $item->title ),
						'desc'  => $desc,
						'link'  => esc_url_raw( (string) $item->link ),
						'image' => esc_url_raw( $src ),
						'date'  => $dateTime,
						'creator' => sanitize_text_field( $creator ),
						'category' => $category
						
					);
				  
				// Store or discard future posts
				if ( ! ( $dofutureposts ) ) {
					
					if ( $dateTime <= $time )
						array_push( $feedsarray, $item );
					
				} else {
					
					array_push( $feedsarray, $item );
					
				}
			  
			  }
			  
			}
		  
		}
		
		// Check for feed entries
		if ( empty( $feedsarray ) )
			return 'No entries available from feeds(s).';

		// Sort Feed Items by Date
		if ( $order ) {
			
			usort($feedsarray, function ( $a, $b): int {
				
				return $a['date'] <=> $b['date'];
				
			});
			
		} else {
			
			usort($feedsarray, function ( $a, $b): int {
				
				return $b['date'] <=> $a['date'];
				
			});
			
		}

		$c = 0;

		// Render output		
		ob_start();
		
		// Include entries header
		include( $template_dir . '/template-parts/wp-rss-feeds/wp-rss-feeds-header.php' );
		
		// Iterate through entries
		foreach ( $feedsarray as $feed ) {
			
			// Limit entries to entrylimit attr or default
			if ( $c == $entrylimit )
				break;
			
			// Format date
			$date = new DateTime();
			$date->setTimestamp( $feed['date'] );
			$date->setTimezone( $timezone );
			
			// Limit entry description if charlimit attr set
			$desc = ( $charlimit > 0 && strlen( $feed['desc'] ) > $charlimit ) ? str_limit_html( $feed['desc'], $charlimit ) . 
				' <a class="wp-rss-feed-entry-desc-readmore" href="'. $feed['link'] . '" target="' . $target . '" title="'. $feed['title'] . '">Read more</a>' : $feed['desc'];

			// Include entry template
			include( $template_dir . '/template-parts/wp-rss-feeds/wp-rss-feeds-entry.php' );
					
			$c++;
				
		}
		
		// Include entries footer
		include( $template_dir . '/template-parts/wp-rss-feeds/wp-rss-feeds-footer.php' );
		
		// Get rendered output
		$result = ob_get_contents();
		ob_end_clean();
		
		// Return rendered output
		return $result;
		
	}
	
	function wp_rss_feeds_style() {
		
		$style = get_template_directory_uri() . '/wp-rss-feeds.css';
		wp_enqueue_style('wp-rss-feeds-stylesheet', $style, array(), rand(100,9999), 'all');
		
	}

	// Check if valid timezone ID
	function isValidTimezone( $id ) {
		
		if ( empty( $id ) )
			return false;
			
		foreach ( timezone_abbreviations_list() as $z ) {
			
			foreach ( $z as $item ) {
				
				if ( $item["timezone_id"] == $id )
					return true;
					
			}
		}
		
		return false;
		
	}
	
	/**
	 * Limit string without break html tags.
	 * Supports UTF8
	 * 
	 * @param string $value
	 * @param int $limit Default 100
	 *
	 * Created by SnakeDrak
	 */
	function str_limit_html($value, $limit = 100)
	{

		if (mb_strwidth($value, 'UTF-8') <= $limit) {
			return $value;
		}

		// Strip text with HTML tags, sum html len tags too.
		// Is there another way to do it?
		do {
			$len          = mb_strwidth($value, 'UTF-8');
			$len_stripped = mb_strwidth(strip_tags($value), 'UTF-8');
			$len_tags     = $len - $len_stripped;

			$value = mb_strimwidth($value, 0, $limit + $len_tags, '', 'UTF-8');
		} while ($len_stripped > $limit);

		// Load as HTML ignoring errors
		$dom = new DOMDocument();
		@$dom->loadHTML('<?xml encoding="utf-8" ?>'.$value, LIBXML_HTML_NODEFDTD);

		// Fix the html errors
		$value = $dom->saveHtml($dom->getElementsByTagName('body')->item(0));

		// Remove body tag
		$value = mb_strimwidth($value, 6, mb_strwidth($value, 'UTF-8') - 13, '', 'UTF-8'); // <body> and </body>
		// Remove empty tags
		return preg_replace('/<(\w+)\b(?:\s+[\w\-.:]+(?:\s*=\s*(?:"[^"]*"|"[^"]*"|[\w\-.:]+))?)*\s*\/?>\s*<\/\1\s*>/', '', $value);
	}
		
