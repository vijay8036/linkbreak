<?php

class LinkBrack_Scanner {

    private $db_table;

    public function __construct() {
        global $wpdb;
        $this->db_table = $wpdb->prefix . 'linkbrack_urls';
    }

    /**
     * Get total number of items to scan (Posts + Pages + CPTs)
     */
    public function get_total_items() {
        // For now, just count published posts & pages
        // Future: Add options query, etc.
        $count_posts = wp_count_posts('post');
        $count_pages = wp_count_posts('page');
        
        $total = $count_posts->publish + $count_pages->publish;
        return $total;
    }

    /**
     * Scan a batch of items.
     * @param int $offset
     * @param int $limit
     * @return array Results
     */
    public function scan_batch($offset = 0, $limit = 5) {
        $args = array(
            'post_type'      => array('post', 'page'),
            'post_status'    => 'publish',
            'posts_per_page' => $limit,
            'offset'         => $offset,
            'orderby'        => 'ID',
            'order'          => 'ASC',
        );

        $query = new WP_Query($args);
        $results = array();

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $post_id = get_the_ID();
                $content = get_the_content();
                
                $urls = $this->extract_urls($content);
                
                foreach ($urls as $url) {
                    $status = $this->check_url($url);
                    
                    // Prepare result item
                    $item = array(
                        'url' => $url,
                        'status_code' => $status['code'],
                        'status_label' => $status['label'],
                        'source_type' => get_post_type(),
                        'source_name' => get_the_title(),
                        'source_url' => get_permalink(),
                        'edit_url' => get_edit_post_link(),
                        'context_snippet' => $this->get_context($content, $url),
                    );
                    
                    // Save to DB (Optional in this step, but good practice)
                    $this->save_url($item, $post_id);
                    
                    $results[] = $item;
                }
            }
            wp_reset_postdata();
        }

        return $results;
    }

    /**
     * Extract URLs from content using Regex
     * NOTE: DOMDocument is better but Regex is faster for simple "href" extraction.
     * We will look for href, src.
     */
    private function extract_urls($content) {
        $urls = array();
        
        // Match href="..."
        if (preg_match_all('/href=["\']?([^"\'>]+)["\']?/', $content, $matches)) {
            foreach ($matches[1] as $url) {
                 if ($this->is_valid_url($url)) {
                     $urls[] = $url;
                 }
            }
        }
        
        // Match src="..."
        if (preg_match_all('/src=["\']?([^"\'>]+)["\']?/', $content, $matches)) {
            foreach ($matches[1] as $url) {
                 if ($this->is_valid_url($url)) {
                     $urls[] = $url;
                 }
            }
        }

        return array_unique($urls);
    }

    private function is_valid_url($url) {
        // Filter out anchors, javascript:, mailto:, etc.
        if (strpos($url, '#') === 0) return false;
        if (strpos($url, 'mailto:') === 0) return false;
        if (strpos($url, 'tel:') === 0) return false;
        if (strpos($url, 'javascript:') === 0) return false;
        
        return true;
    }

    /**
     * Check HTTP status of a URL
     */
    private function check_url($url) {
        // Resolve relative URLs if needed
        if (strpos($url, 'http') !== 0) {
            $url = site_url($url); 
        }

        $args = array(
            'timeout' => 5,
            'redirection' => 5,
            'httpversion' => '1.0',
            'sslverify' => false,
        );

        // Try HEAD first
        $response = wp_remote_head($url, $args);
        
        if (is_wp_error($response)) {
            return array('code' => 0, 'label' => 'error');
        }

        $code = wp_remote_retrieve_response_code($response);

        // If 405 Method Not Allowed, try GET
        if ($code == 405) {
            $response = wp_remote_get($url, $args);
            $code = wp_remote_retrieve_response_code($response);
        }

        $label = 'unknown';
        if ($code >= 200 && $code < 400) $label = 'working';
        elseif ($code == 404 || $code == 410) $label = 'broken';
        elseif ($code >= 500) $label = 'error';
        elseif ($code == 0) $label = 'timeout';

        return array('code' => $code, 'label' => $label);
    }

    private function get_context($content, $url) {
        // Find the URL position
        $pos = strpos($content, $url);
        if ($pos === false) return '...';
        
        $start = max(0, $pos - 50);
        $length = strlen($url) + 100;
        
        return '...' . esc_html(substr($content, $start, $length)) . '...';
    }

    private function save_url($item, $post_id) {
        global $wpdb;
        // Simple insert for now. 
        // In real app, we might want to update existing if scanned recently.
        $wpdb->insert(
            $this->db_table,
            array(
                'url' => $item['url'],
                'status_code' => $item['status_code'],
                'status_label' => $item['status_label'],
                'source_type' => $item['source_type'],
                'source_id' => $post_id,
                'source_name' => $item['source_name'],
                'context_snippet' => $item['context_snippet'],
                'last_scanned' => current_time('mysql')
            )
        );
    }

}
