<div class="wrap linkbrack-wrap">
    
    <!-- Header -->
    <div class="linkbrack-header">
        <div class="linkbrack-header-left">
            <div class="linkbrack-header-content">
                <span class="material-icons linkbrack-logo-icon">link</span>
                <div>
                    <h1>LinkBrack Scanner</h1>
                    <p class="linkbrack-subtitle">Professional broken link detection for WordPress</p>
                </div>
            </div>
        </div>
        <div class="linkbrack-header-stat">
            <span class="material-icons">error</span>
            <div class="stat-content">
                <h3>Broken Links</h3>
                <span class="stat-number" id="stat-broken-header">0</span>
            </div>
        </div>
    </div>
    
    <div class="linkbrack-dashboard">
        
        <!-- Stats Overview -->
        <div class="linkbrack-stats-row">
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
        <div class="linkbrack-card linkbrack-controls">
            <h2>
                <span class="material-icons">settings</span>
                Scan Controls
            </h2>
            <div class="linkbrack-actions">
                <button id="linkbrack-start-scan" class="button button-primary button-large">
                    <span class="material-icons">play_arrow</span>
                    Start New Scan
                </button>
                <button id="linkbrack-pause-scan" class="button button-secondary button-large" disabled>
                    <span class="material-icons">pause</span>
                    Pause
                </button>
                <span id="linkbrack-status-text" class="status-message">Ready to scan.</span>
            </div>
            
            <!-- Progress Bar -->
            <div class="linkbrack-progress-wrapper" style="display:none;">
                <div class="linkbrack-progress-bar">
                    <div class="linkbrack-progress-fill" style="width: 0%;"></div>
                </div>
                <div class="linkbrack-progress-stats">
                    <span class="material-icons">analytics</span>
                    <span id="scanned-count">0</span> / <span id="total-count">0</span> items scanned
                </div>
            </div>
        </div>

        <!-- Results Table -->
        <div class="linkbrack-card linkbrack-results">
            <div class="linkbrack-results-header">
                <h2>
                    <span class="material-icons">list</span>
                    Scan Results
                </h2>
                <div class="linkbrack-header-controls">
                    <div class="linkbrack-export-buttons">
                        <button id="linkbrack-export-excel" class="button button-secondary">
                            <span class="material-icons">table_chart</span>
                            Export Excel
                        </button>
                        <button id="linkbrack-export-pdf" class="button button-secondary">
                            <span class="material-icons">picture_as_pdf</span>
                            Export PDF
                        </button>
                    </div>
                    <div class="linkbrack-filter-controls">
                        <span class="material-icons">filter_list</span>
                        <select id="linkbrack-filter-status">
                            <option value="all">All Statuses</option>
                            <option value="broken">Broken (404)</option>
                            <option value="working">Working</option>
                            <option value="error">Errors</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <table class="wp-list-table widefat fixed striped linkbrack-table">
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
                <tbody id="linkbrack-results-body">
                    <tr class="linkbrack-empty-state">
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
<div id="linkbrack-modal" class="linkbrack-modal" style="display:none;">
    <div class="linkbrack-modal-content">
        <span class="linkbrack-close">
            <span class="material-icons">close</span>
        </span>
        <h2 id="modal-title">Link Details</h2>
        <div id="modal-body"></div>
    </div>
</div>
