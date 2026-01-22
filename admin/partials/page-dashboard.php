<div class="wrap link-analyzer-wrap">

    <!-- Header -->
    <div class="link-analyzer-header">
        <div class="link-analyzer-header-left">
            <div class="link-analyzer-header-content">
                <img src="<?php echo esc_url(LINK_ANALYZER_PLUGIN_URL . 'admin/images/logo.png'); ?>"
                    class="link-analyzer-logo-img" alt="Link_Analyzer Logo">
                <div>
                    <h1>Link Analyzer</h1>
                    <p class="link-analyzer-subtitle">Professional broken link detection for WordPress</p>
                </div>
            </div>
        </div>
        <div class="link-analyzer-header-stat">
            <span class="material-icons">error</span>
            <div class="stat-content">
                <h3>Broken Links</h3>
                <span class="stat-number" id="stat-broken-header">0</span>
            </div>
        </div>
    </div>

    <div class="link-analyzer-dashboard">

        <!-- Stats Overview -->
        <div class="link-analyzer-stats-row">
            <div class="stat-box success">
                <span class="material-icons">check_circle</span>
                <div class="stat-content">
                    <h3>Working Links</h3>
                    <span class="stat-number" id="stat-working">0</span>
                </div>
            </div>
            <div class="stat-box info">
                <span class="material-icons">analytics</span>
                <div class="stat-content">
                    <h3>Total Scanned</h3>
                    <span class="stat-number" id="stat-total">0</span>
                </div>
            </div>
        </div>

        <!-- Controls -->
        <div class="link-analyzer-card link-analyzer-controls">
            <h2>
                <span class="material-icons">settings</span>
                Scan Controls
            </h2>
            <div class="link-analyzer-actions">
                <button id="link-analyzer-start-scan" class="button button-primary button-large">
                    <span class="material-icons">play_arrow</span>
                    Start New Scan
                </button>
                <button id="link-analyzer-pause-scan" class="button button-secondary button-large" disabled>
                    <span class="material-icons">pause</span>
                    Pause
                </button>
                <span id="link-analyzer-status-text" class="status-message">Ready to scan.</span>
            </div>

            <!-- Progress Bar -->
            <div class="link-analyzer-progress-wrapper" style="display:none;">
                <div class="link-analyzer-progress-bar">
                    <div class="link-analyzer-progress-fill" style="width: 0%;"></div>
                </div>
                <div class="link-analyzer-progress-stats">
                    <span class="material-icons">analytics</span>
                    <span id="scanned-count">0</span> / <span id="total-count">0</span> items scanned
                </div>
            </div>
        </div>

        <!-- Results Table -->
        <div class="link-analyzer-card link-analyzer-results">
            <div class="link-analyzer-results-header">
                <h2>
                    <span class="material-icons">list</span>
                    Scan Results
                </h2>
                <div class="link-analyzer-header-controls">
                    <div class="link-analyzer-export-buttons">
                        <button id="link-analyzer-export-excel" class="button button-secondary">
                            <span class="material-icons">table_chart</span>
                            Export Excel
                        </button>
                        <button id="link-analyzer-export-pdf" class="button button-secondary">
                            <span class="material-icons">picture_as_pdf</span>
                            Export PDF
                        </button>
                    </div>
                    <div class="link-analyzer-filter-controls">
                        <span class="material-icons">filter_list</span>
                        <select id="link-analyzer-filter-status">
                            <option value="all">All Statuses</option>
                            <option value="broken">Broken (404)</option>
                            <option value="working">Working</option>
                            <option value="error">Errors</option>
                        </select>
                    </div>
                </div>
            </div>

            <table class="wp-list-table widefat fixed striped link-analyzer-table">
                <thead>
                    <tr>
                        <th width="35%">
                            <span class="material-icons">link</span>
                            URL
                        </th>
                        <th width="10%">
                            <span class="material-icons">info</span>
                            Status
                        </th>
                        <th width="20%">
                            <span class="material-icons">source</span>
                            Source
                        </th>
                        <th width="25%">
                            <span class="material-icons">code</span>
                            Context
                        </th>
                        <th width="10%">
                            <span class="material-icons">more_vert</span>
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody id="link-analyzer-results-body">
                    <tr class="link-analyzer-empty-state">
                        <td colspan="5">
                            <div class="empty-state-content">
                                <span class="material-icons">search</span>
                                <p>No scan results yet.</p>
                                <p class="empty-state-hint">Click "Start New Scan" to begin detecting broken links.</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>
</div>

<!-- Modal Template -->
<div id="link-analyzer-modal" class="link-analyzer-modal" style="display:none;">
    <div class="link-analyzer-modal-content">
        <span class="link-analyzer-close">
            <span class="material-icons">close</span>
        </span>
        <h2 id="modal-title">Link Details</h2>
        <div id="modal-body"></div>
    </div>
</div>