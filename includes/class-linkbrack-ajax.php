<?php
/**
 * Ajax handlers for scanning process.
 */
class LinkBrack_Ajax {

    public function start_scan() {
        check_ajax_referer( 'linkbrack_scan_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Permission denied' ) );
        }

        // Initialize Scan
        // In a real app, we might store a "scan_id" in options.
        // Get total items count.
        $scanner = new LinkBrack_Scanner();
        $total = $scanner->get_total_items();

        // Clear previous results from DB?
        // global $wpdb; 
        // $wpdb->query("TRUNCATE TABLE {$wpdb->prefix}linkbrack_urls"); // Optional: Reset

        wp_send_json_success( array( 
            'message' => 'Scan started',
            'total_items' => $total
        ) );
    }

    public function scan_batch() {
        check_ajax_referer( 'linkbrack_scan_nonce', 'nonce' );

        $offset = isset( $_POST['scanned_count'] ) ? intval( $_POST['scanned_count'] ) : 0;
        $batch_size = 5; // Small batch for responsiveness

        $scanner = new LinkBrack_Scanner();
        $results = $scanner->scan_batch( $offset, $batch_size );

        $count = count($results); // How many urls found? 
        // Wait, scan_batch returns URLs found in that batch of POSTS, not "count of posts processed".
        // The frontend tracks "items scanned" as "Post Scanned". 
        // So we need to return how many *posts* we processed, so the frontend can update offset.
        
        // Actually, $scanner->scan_batch uses WP_Query with offset. 
        // If we processed 5 posts, we should increment offset by 5.
        // But $results only contains URLs found.
        // We need to know if we are done.

        $total_posts_scanned = $batch_size; // We asked for 5.
        
        // If results are empty, it might mean those 5 posts had no links, OR we ran out of posts.
        // We need a way to know if we are finished.
        
        $total_in_db = $scanner->get_total_items();
        $is_complete = ($offset + $batch_size) >= $total_in_db;

        wp_send_json_success( array( 
            'message' => 'Batch complete',
            'results' => $results,
            'count' => $batch_size, // We processed 5 posts
            'is_complete' => $is_complete
        ) );
    }

}
