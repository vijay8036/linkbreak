<?php

class LinkBrack_Export {

    /**
     * Export results to CSV (Excel compatible)
     */
    public function export_to_csv() {
        check_ajax_referer( 'linkbrack_scan_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Permission denied' ) );
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'linkbrack_urls';

        $results = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY id DESC", ARRAY_A );

        if ( empty( $results ) ) {
            wp_send_json_error( array( 'message' => 'No results to export' ) );
        }

        // Set headers for CSV download
        header( 'Content-Type: text/csv; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename=linkbrack-report-' . date( 'Y-m-d-H-i-s' ) . '.csv' );
        header( 'Pragma: no-cache' );
        header( 'Expires: 0' );

        // Create file pointer
        $output = fopen( 'php://output', 'w' );

        // Add BOM for Excel UTF-8 support
        fprintf( $output, chr(0xEF).chr(0xBB).chr(0xBF) );

        // Add headers
        fputcsv( $output, array( 'URL', 'Status Code', 'Status', 'Source Type', 'Source Name', 'Context', 'Last Scanned' ) );

        // Add data
        foreach ( $results as $row ) {
            fputcsv( $output, array(
                $row['url'],
                $row['status_code'],
                $row['status_label'],
                $row['source_type'],
                $row['source_name'],
                strip_tags( $row['context_snippet'] ),
                $row['last_scanned']
            ) );
        }

        fclose( $output );
        exit;
    }

    /**
     * Export results to PDF
     */
    public function export_to_pdf() {
        check_ajax_referer( 'linkbrack_scan_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Permission denied' ) );
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'linkbrack_urls';

        $results = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY id DESC", ARRAY_A );

        if ( empty( $results ) ) {
            wp_send_json_error( array( 'message' => 'No results to export' ) );
        }

        // Count stats
        $stats = array(
            'total' => count( $results ),
            'working' => 0,
            'broken' => 0,
            'errors' => 0
        );

        foreach ( $results as $row ) {
            if ( $row['status_label'] === 'working' ) {
                $stats['working']++;
            } elseif ( $row['status_label'] === 'broken' ) {
                $stats['broken']++;
            } else {
                $stats['errors']++;
            }
        }

        // Generate HTML for PDF
        $html = $this->generate_pdf_html( $results, $stats );

        // Set headers
        header( 'Content-Type: text/html; charset=utf-8' );
        
        // For now, output HTML that can be printed to PDF
        // In production, you'd use a library like TCPDF or mPDF
        echo $html;
        exit;
    }

    /**
     * Generate HTML for PDF export
     */
    private function generate_pdf_html( $results, $stats ) {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>LinkBrack Scan Report</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 40px;
                    color: #333;
                }
                .header {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    padding: 30px;
                    border-radius: 8px;
                    margin-bottom: 30px;
                }
                .header h1 {
                    margin: 0 0 10px 0;
                    font-size: 28px;
                }
                .header p {
                    margin: 0;
                    opacity: 0.9;
                }
                .stats {
                    display: flex;
                    gap: 20px;
                    margin-bottom: 30px;
                }
                .stat-box {
                    flex: 1;
                    padding: 20px;
                    border: 2px solid #e5e7eb;
                    border-radius: 8px;
                    text-align: center;
                }
                .stat-box h3 {
                    margin: 0 0 10px 0;
                    font-size: 14px;
                    color: #6b7280;
                    text-transform: uppercase;
                }
                .stat-box .number {
                    font-size: 32px;
                    font-weight: bold;
                    color: #1f2937;
                }
                .stat-box.success { border-color: #10b981; }
                .stat-box.error { border-color: #ef4444; }
                .stat-box.warning { border-color: #f59e0b; }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 20px;
                }
                th {
                    background: #f9fafb;
                    padding: 12px;
                    text-align: left;
                    border-bottom: 2px solid #e5e7eb;
                    font-weight: 600;
                    font-size: 12px;
                    text-transform: uppercase;
                    color: #374151;
                }
                td {
                    padding: 10px 12px;
                    border-bottom: 1px solid #e5e7eb;
                    font-size: 12px;
                }
                tr:hover {
                    background: #f9fafb;
                }
                .status-badge {
                    display: inline-block;
                    padding: 4px 8px;
                    border-radius: 4px;
                    font-size: 11px;
                    font-weight: 600;
                }
                .status-working { background: #d1fae5; color: #065f46; }
                .status-broken { background: #fee2e2; color: #991b1b; }
                .status-error { background: #fef3c7; color: #92400e; }
                .footer {
                    margin-top: 40px;
                    padding-top: 20px;
                    border-top: 1px solid #e5e7eb;
                    text-align: center;
                    color: #6b7280;
                    font-size: 12px;
                }
                @media print {
                    body { margin: 20px; }
                    .no-print { display: none; }
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>🔗 LinkBrack Scan Report</h1>
                <p>Generated on <?php echo date( 'F j, Y \a\t g:i A' ); ?></p>
            </div>

            <div class="stats">
                <div class="stat-box success">
                    <h3>Working Links</h3>
                    <div class="number"><?php echo $stats['working']; ?></div>
                </div>
                <div class="stat-box error">
                    <h3>Broken Links</h3>
                    <div class="number"><?php echo $stats['broken']; ?></div>
                </div>
                <div class="stat-box warning">
                    <h3>Errors/Timeouts</h3>
                    <div class="number"><?php echo $stats['errors']; ?></div>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>URL</th>
                        <th>Status</th>
                        <th>Source</th>
                        <th>Context</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $results as $row ) : ?>
                    <tr>
                        <td><?php echo esc_html( $row['url'] ); ?></td>
                        <td>
                            <span class="status-badge status-<?php echo esc_attr( $row['status_label'] ); ?>">
                                <?php echo esc_html( $row['status_code'] ?: $row['status_label'] ); ?>
                            </span>
                        </td>
                        <td><?php echo esc_html( $row['source_type'] . ': ' . $row['source_name'] ); ?></td>
                        <td><?php echo esc_html( wp_trim_words( strip_tags( $row['context_snippet'] ), 10 ) ); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="footer">
                <p>LinkBrack - Professional Broken Link Scanner for WordPress</p>
                <p>Total URLs Scanned: <?php echo $stats['total']; ?></p>
            </div>

            <script class="no-print">
                // Auto-trigger print dialog
                window.onload = function() {
                    window.print();
                };
            </script>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
}
