<?php
// modules/llm/index.php

// Determine the action for the LLM module
$llm_action = isset($_GET['action']) ? sanitize_input($_GET['action']) : 'dashboard'; // Default to 'dashboard' view for LLM module

if ($llm_action === 'settings') {
    // If action is 'settings', load the settings page for LLM
    // This assumes a naming convention like 'settings.php' within the module directory
    $settings_file = __DIR__ . '/settings.php';
    if (file_exists($settings_file)) {
        require_once $settings_file;
    } else {
        display_error("LLM settings page not found.");
    }
    return; // Stop further processing in this file if settings page is loaded
}


// For the LLM dashboard view
$page_title = "LLM Integration - Dashboard - OpenCart Manager";

// Placeholder data for LLM status
$llm_configured = false; // In a real app, check if API keys are set
$llm_active_provider = "Not Configured"; // e.g., "OpenAI", "Claude"
$llm_enabled_globally = defined('LLM_MODULE_ENABLED') && LLM_MODULE_ENABLED;

$products_missing_seo = 50; // Placeholder
$auto_generation_history = [
    ['date' => '2023-10-26 10:00', 'type' => 'SEO URLs', 'items_processed' => 25, 'status' => 'Completed'],
    ['date' => '2023-10-25 14:00', 'type' => 'Product Descriptions', 'items_processed' => 10, 'status' => 'Partial (2 errors)'],
];

?>

<div class="container-fluid">
    <!-- Page Title & Actions -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-robot me-2"></i><?php echo $page_title; ?></h1>
        <div>
            <a href="<?php echo BASE_URL; ?>index.php?module=llm&action=settings" class="btn btn-sm btn-primary"><i class="fas fa-cog me-1"></i> LLM Configuration</a>
        </div>
    </div>

    <!-- LLM Module Status -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-cogs me-1"></i>LLM Module Status</h6>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Global Status:
                            <?php if ($llm_enabled_globally): ?>
                                <span class="badge bg-success">Enabled</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Disabled (via config.php)</span>
                            <?php endif; ?>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Configuration:
                            <?php if ($llm_configured): ?>
                                <span class="badge bg-success">Configured</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">Not Configured</span>
                            <?php endif; ?>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Active Provider:
                            <span class="badge bg-info"><?php echo htmlspecialchars($llm_active_provider); ?></span>
                        </li>
                         <li class="list-group-item">
                            <small class="text-muted">To enable or disable the LLM module entirely, update `LLM_MODULE_ENABLED` in `config/config.php`.</small>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
             <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-lightbulb me-1"></i>Quick Actions</h6>
                </div>
                <div class="card-body text-center">
                    <p>Quick access to common LLM tasks.</p>
                    <button class="btn btn-outline-success mb-2" onclick="alert('Trigger LLM-based SEO URL generation for missing items - Placeholder')">
                        <i class="fas fa-link me-1"></i> Generate Missing SEO URLs (<?php echo $products_missing_seo; ?>)
                    </button>
                    <button class="btn btn-outline-primary mb-2" onclick="alert('Trigger LLM-based product description generation - Placeholder')">
                        <i class="fas fa-file-alt me-1"></i> Generate Product Descriptions
                    </button>
                     <a href="<?php echo BASE_URL; ?>index.php?module=seo" class="btn btn-outline-secondary mb-2">
                        <i class="fas fa-search-location me-1"></i> Go to SEO Management
                    </a>
                </div>
            </div>
        </div>
    </div>


    <!-- Auto-SEO Generator / Batch Processing -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-magic me-1"></i>LLM Content Generation Tasks (Placeholder)</h6>
        </div>
        <div class="card-body">
            <p>This section will allow batch processing for SEO URLs, descriptions, etc., based on LLM suggestions. Users can review and approve generated content.</p>
            
            <h6 class="mt-3">Recent Activity:</h6>
            <?php if (!empty($auto_generation_history)): ?>
            <div class="table-responsive">
                <table class="table table-sm table-striped">
                    <thead>
                        <tr><th>Date</th><th>Task Type</th><th>Items Processed</th><th>Status</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($auto_generation_history as $log): ?>
                        <tr>
                            <td><?php echo $log['date']; ?></td>
                            <td><?php echo htmlspecialchars($log['type']); ?></td>
                            <td class="text-center"><?php echo $log['items_processed']; ?></td>
                            <td>
                                <span class="badge bg-<?php echo strpos(strtolower($log['status']), 'error') !== false ? 'danger' : (strpos(strtolower($log['status']), 'partial') !== false ? 'warning text-dark' : 'success'); ?>">
                                    <?php echo htmlspecialchars($log['status']); ?>
                                </span>
                            </td>
                            <td><button class="btn btn-xs btn-outline-info" onclick="alert('View details for this task - Placeholder')"><i class="fas fa-eye"></i> View Log</button></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
                <p class="text-muted">No recent LLM generation activity.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Rollback Functionality Placeholder -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-danger"><i class="fas fa-undo me-1"></i>Rollback Functionality (Placeholder)</h6>
        </div>
        <div class="card-body">
            <p>In case of undesired LLM generations, a rollback mechanism will be available here to revert changes for specific batches or timeframes.</p>
            <button class="btn btn-sm btn-outline-danger" onclick="alert('Open rollback options - Placeholder')"><i class="fas fa-history me-1"></i> View Rollback Options</button>
        </div>
    </div>

</div>
