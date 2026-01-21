jQuery(document).ready(function ($) {

    const ui = {
        startBtn: $('#linkbrack-start-scan'),
        pauseBtn: $('#linkbrack-pause-scan'),
        statusText: $('#linkbrack-status-text'),
        progressBar: $('.linkbrack-progress-fill'),
        progressWrapper: $('.linkbrack-progress-wrapper'),
        scannedCount: $('#scanned-count'),
        totalCount: $('#total-count'),
        resultsBody: $('#linkbrack-results-body'),
        statWorking: $('#stat-working'),
        statBroken: $('#stat-broken-header'),
        statTotal: $('#stat-total'),
        filterStatus: $('#linkbrack-filter-status')
    };

    let isPaused = false;
    let scanStats = {
        working: 0,
        broken: 0,
        total: 0,
        scanned: 0
    };

    // --- Event Listeners ---

    ui.startBtn.on('click', function () {
        startScan();
    });

    ui.pauseBtn.on('click', function () {
        if (isPaused) {
            resumeScan();
        } else {
            pauseScan();
        }
    });

    ui.filterStatus.on('change', function () {
        const val = $(this).val();
        filterResults(val);
    });

    // Copy Button Handler
    $(document).on('click', '.copy-btn', function (e) {
        e.preventDefault();
        const btn = $(this);
        const row = btn.closest('tr');
        const urlToCheck = row.find('.column-url a').attr('href');

        if (urlToCheck) {
            navigator.clipboard.writeText(urlToCheck).then(function () {
                const originalText = btn.text();
                btn.text('Copied!');
                setTimeout(() => {
                    btn.text(originalText);
                }, 2000);
            }, function (err) {
                console.error('Could not copy text: ', err);
                alert('Failed to copy to clipboard');
            });
        }
    });

    // Export Excel Handler
    $('#linkbrack-export-excel').on('click', function () {
        const btn = $(this);
        btn.prop('disabled', true).text('Exporting...');

        const form = $('<form>', {
            method: 'POST',
            action: linkbrack_ajax.ajax_url
        });

        form.append($('<input>', { type: 'hidden', name: 'action', value: 'linkbrack_export_csv' }));
        form.append($('<input>', { type: 'hidden', name: 'nonce', value: linkbrack_ajax.nonce }));

        $('body').append(form);
        form.submit();
        form.remove();

        setTimeout(() => {
            btn.prop('disabled', false).html('<span class="material-icons">table_chart</span> Export Excel');
        }, 2000);
    });

    // Export PDF Handler
    $('#linkbrack-export-pdf').on('click', function () {
        const btn = $(this);
        btn.prop('disabled', true).text('Generating...');

        const url = linkbrack_ajax.ajax_url + '?action=linkbrack_export_pdf&nonce=' + linkbrack_ajax.nonce;
        window.open(url, '_blank');

        setTimeout(() => {
            btn.prop('disabled', false).html('<span class="material-icons">picture_as_pdf</span> Export PDF');
        }, 2000);
    });

    // --- Logic ---

    function startScan() {
        resetUI();
        ui.startBtn.prop('disabled', true);
        ui.pauseBtn.prop('disabled', false).text('Pause');
        ui.statusText.text('Initializing scan...');
        ui.progressWrapper.show();

        $.ajax({
            url: linkbrack_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'linkbrack_start_scan',
                nonce: linkbrack_ajax.nonce
            },
            success: function (response) {
                if (response.success) {
                    scanStats.total = response.data.total_items;
                    ui.totalCount.text(scanStats.total);
                    ui.statusText.text('Scanning...');
                    processBatch();
                } else {
                    alert('Error starting scan: ' + response.data.message);
                    resetUIState();
                }
            },
            error: function () {
                alert('Connection error starting scan.');
                resetUIState();
            }
        });
    }

    function processBatch() {
        if (isPaused) return;

        $.ajax({
            url: linkbrack_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'linkbrack_scan_batch',
                nonce: linkbrack_ajax.nonce,
                scanned_count: scanStats.scanned // Pass offset or cursor if needed
            },
            success: function (response) {
                if (response.success) {
                    const data = response.data;

                    // Update Stats
                    updateStats(data.results);

                    // Append Results
                    data.results.forEach(item => addResultRow(item));

                    // Update Progress
                    scanStats.scanned += data.count; // Assuming batch count
                    updateProgress();

                    // Continue or Finish
                    if (!data.is_complete && scanStats.scanned < scanStats.total) { // Safety check
                        processBatch();
                    } else {
                        finishScan();
                    }
                } else {
                    ui.statusText.text('Error during batch: ' + response.data.message);
                }
            },
            error: function () {
                ui.statusText.text('Connection error. Retrying...');
                setTimeout(processBatch, 3000);
            }
        });
    }

    function pauseScan() {
        isPaused = true;
        ui.pauseBtn.text('Resume');
        ui.statusText.text('Paused');
    }

    function resumeScan() {
        isPaused = false;
        ui.pauseBtn.text('Pause');
        ui.statusText.text('Scanning...');
        processBatch();
    }

    function finishScan() {
        ui.startBtn.prop('disabled', false).text('Start New Scan');
        ui.pauseBtn.prop('disabled', true);
        ui.statusText.text('Scan Complete!');
        ui.progressBar.css('width', '100%');
    }

    function updateStats(results) {
        results.forEach(item => {
            if (item.status_label === 'working') scanStats.working++;
            if (item.status_label === 'broken') scanStats.broken++;
            if (item.status_label === 'timeout' || item.status_label === 'error') scanStats.broken++;
        });

        ui.statWorking.text(scanStats.working);
        ui.statBroken.text(scanStats.broken);
        ui.statTotal.text(scanStats.working + scanStats.broken);
        ui.scannedCount.text(scanStats.scanned + results.length);
    }

    function updateProgress() {
        const percent = Math.min(100, Math.round((scanStats.scanned / scanStats.total) * 100));
        ui.progressBar.css('width', percent + '%');
        ui.scannedCount.text(scanStats.scanned);
    }

    function addResultRow(item) {
        const row = `
            <tr class="linkbrack-row status-${item.status_label}">
                <td class="column-url">
                    <a href="${item.url}" target="_blank">${item.url}</a>
                </td>
                <td class="column-status">
                    <span class="status-badge ${item.status_label}">${item.status_code || item.status_label}</span>
                </td>
                <td class="column-source">
                    ${item.source_type}: <a href="${item.source_url}" target="_blank">${item.source_name}</a>
                </td>
                <td class="column-context">
                    <code>${item.context_snippet || '...'}</code>
                </td>
                <td class="column-actions">
                    <button class="button button-small copy-btn">
                        <span class="material-icons" style="font-size: 16px; margin-right: 4px;">content_copy</span>
                        Copy
                    </button>
                </td>
            </tr>
        `;

        // Remove empty state if it exists
        $('.linkbrack-empty-state').remove();

        ui.resultsBody.append(row);
    }

    function filterResults(status) {
        if (status === 'all') {
            $('.linkbrack-row').show();
        } else {
            $('.linkbrack-row').hide();
            $('.linkbrack-row.status-' + status).show();
        }
    }

    function resetUI() {
        scanStats = { working: 0, broken: 0, total: 0, scanned: 0 };
        ui.resultsBody.empty();
        ui.progressBar.css('width', '0%');
        ui.statWorking.text('0');
        ui.statBroken.text('0');
        ui.statTotal.text('0');
        ui.startBtn.text('Scanning...');
        isPaused = false;
    }

    function resetUIState() {
        ui.startBtn.prop('disabled', false).text('Start New Scan');
        ui.pauseBtn.prop('disabled', true);
    }

});
