<?php
// modules/seo/index.php
$page_title = "SEO Link Management - OpenCart Manager";

// Placeholder data for Phase 1
// 'has_seo_url' => true if seo_url is not empty and not default like index.php?route=product/product&product_id=X
// 'is_default_url' => true if current_url is like index.php?route=...
$products_seo_status = [
    ['id' => 1, 'product_name' => 'Awesome T-Shirt - Red', 'current_url' => 'index.php?route=product/product&product_id=1', 'seo_url' => '', 'is_default_url' => true, 'has_seo_url' => false, 'language' => 'English'],
    ['id' => 2, 'product_name' => 'Cool Gadget Pro', 'current_url' => 'cool-gadget-pro', 'seo_url' => 'cool-gadget-pro', 'is_default_url' => false, 'has_seo_url' => true, 'language' => 'English'],
    ['id' => 3, 'product_name' => 'Elegant Watch Model X', 'current_url' => 'index.php?route=product/product&product_id=3', 'seo_url' => '', 'is_default_url' => true, 'has_seo_url' => false, 'language' => 'English'],
    ['id' => 1, 'product_name' => 'Camiseta Increíble - Rojo', 'current_url' => 'index.php?route=product/product&product_id=1', 'seo_url' => '', 'is_default_url' => true, 'has_seo_url' => false, 'language' => 'Español'],
    ['id' => 4, 'product_name' => 'Placeholder Product D', 'current_url' => 'placeholder-product-d', 'seo_url' => 'placeholder-product-d', 'is_default_url' => false, 'has_seo_url' => true, 'language' => 'English'],
];

$supported_languages = get_supported_languages(); // from includes/functions.php

?>

<div class="container-fluid">
    <!-- Page Title & Actions -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-search-location me-2"></i><?php echo $page_title; ?></h1>
        <div>
            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#batchGenerateModal"><i class="fas fa-magic me-1"></i> Batch Generate SEO URLs</button>
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#bulkEditModal"><i class="fas fa-edit me-1"></i> Bulk Edit URLs</button>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter me-1"></i>Filter Products</h6>
        </div>
        <div class="card-body">
            <form class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="productSearch" class="form-label">Search Product</label>
                    <input type="text" class="form-control form-control-sm" id="productSearch" placeholder="Product name or ID...">
                </div>
                <div class="col-md-3">
                    <label for="seoStatusFilter" class="form-label">SEO Status</label>
                    <select id="seoStatusFilter" class="form-select form-select-sm">
                        <option selected value="">All Statuses</option>
                        <option value="missing">Missing SEO URL</option>
                        <option value="has_seo">Has SEO URL</option>
                        <option value="default_url">Using Default URL</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="languageFilter" class="form-label">Language</label>
                    <select id="languageFilter" class="form-select form-select-sm">
                        <option selected value="">All Languages</option>
                        <?php foreach ($supported_languages as $code => $lang): ?>
                            <option value="<?php echo $code; ?>"><?php echo htmlspecialchars($lang['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-sm btn-info w-100"><i class="fas fa-search me-1"></i>Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Product SEO URL List -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-link me-1"></i>Product SEO URL Status</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover" id="seoUrlTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Product Name</th>
                            <th>Language</th>
                            <th>Current URL / SEO Alias</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products_seo_status as $product): ?>
                            <tr>
                                <td><?php echo $product['id']; ?></td>
                                <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                                <td><?php echo htmlspecialchars($product['language']); ?></td>
                                <td>
                                    <input type="text" class="form-control form-control-sm" value="<?php echo htmlspecialchars($product['seo_url'] ?: $product['current_url']); ?>" 
                                           <?php echo $product['has_seo_url'] ? '' : 'disabled'; ?> 
                                           placeholder="Enter SEO URL alias">
                                    <?php if ($product['is_default_url'] && !$product['has_seo_url']): ?>
                                        <small class="text-muted d-block">Original: <?php echo htmlspecialchars($product['current_url']); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($product['has_seo_url']): ?>
                                        <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>SEO URL Active</span>
                                    <?php elseif ($product['is_default_url']): ?>
                                        <span class="badge bg-warning text-dark"><i class="fas fa-exclamation-triangle me-1"></i>Missing SEO URL</span>
                                    <?php else: ?>
                                         <span class="badge bg-secondary"><i class="fas fa-times-circle me-1"></i>Not Set / Default</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-success" title="Save SEO URL" onclick="alert('Save SEO URL for product ID <?php echo $product['id']; ?> in <?php echo $product['language']; ?> - Placeholder');"><i class="fas fa-save"></i></button>
                                    <button class="btn btn-sm btn-info" title="Preview URL" onclick="alert('Preview URL for product ID <?php echo $product['id']; ?> - Placeholder');"><i class="fas fa-eye"></i></button>
                                    <button class="btn btn-sm btn-warning" title="Generate Suggestion (LLM)" onclick="alert('Generate SEO URL suggestion for product ID <?php echo $product['id']; ?> - Placeholder');"><i class="fas fa-robot"></i></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <!-- Pagination Placeholder -->
            <nav aria-label="SEO URL table navigation">
              <ul class="pagination justify-content-center mt-3">
                <li class="page-item disabled"><a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a></li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">Next</a></li>
              </ul>
            </nav>
        </div>
    </div>

    <!-- Additional SEO Tools Placeholders -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-search-plus me-1"></i>Duplicate URL Detection</h6>
                </div>
                <div class="card-body text-center">
                    <p class="text-muted">Functionality to detect and manage duplicate SEO URLs will be here.</p>
                    <button class="btn btn-sm btn-secondary" onclick="alert('Run duplicate URL check - Placeholder');"><i class="fas fa-search-plus"></i> Check for Duplicates</button>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-globe-americas me-1"></i>Multi-language URL Management Overview</h6>
                </div>
                <div class="card-body text-center">
                    <p class="text-muted">Overview and tools for managing multi-language SEO URLs consistency.</p>
                     <div id="langSeoStatusChartPlaceholder" style="height: 150px; background-color: #f8f9fc; display:flex; align-items:center; justify-content:center;">
                        <small>Chart/Data Placeholder</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Batch Generate SEO URLs Modal (Placeholder) -->
<div class="modal fade" id="batchGenerateModal" tabindex="-1" aria-labelledby="batchGenerateModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="batchGenerateModalLabel"><i class="fas fa-magic me-1"></i>Batch Generate SEO URLs (Placeholder)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Options for batch generating SEO URLs based on product titles, categories, etc. (Potentially using LLM).</p>
        <div class="mb-3">
            <label for="generationStrategy" class="form-label">Generation Strategy</label>
            <select id="generationStrategy" class="form-select">
                <option selected>Product Title</option>
                <option>Product Title + Category</option>
                <option>LLM Based (if configured)</option>
            </select>
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="overwriteExisting">
            <label class="form-check-label" for="overwriteExisting">Overwrite existing custom SEO URLs</label>
        </div>
         <div class="mb-3">
            <label for="targetLanguage" class="form-label">Target Language for Generation</label>
            <select id="targetLanguage" class="form-select">
                <option value="all">All Languages</option>
                 <?php foreach ($supported_languages as $code => $lang): ?>
                    <option value="<?php echo $code; ?>"><?php echo htmlspecialchars($lang['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div id="generateProgress" style="display:none;">
            <div class="progress">
              <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <p class="text-center mt-1"><small id="generateProgressText">Starting...</small></p>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-success" onclick="startBatchGeneration()">Start Generation</button>
      </div>
    </div>
  </div>
</div>

<!-- Bulk Edit URLs Modal (Placeholder) -->
<div class="modal fade" id="bulkEditModal" tabindex="-1" aria-labelledby="bulkEditModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl"> <!-- xl for wider modal -->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="bulkEditModalLabel"><i class="fas fa-edit me-1"></i>Bulk Edit SEO URLs (Placeholder)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>Interface for bulk editing SEO URLs, similar to a spreadsheet. Will list products and their SEO URL fields for quick editing.</p>
        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
            <table class="table table-sm table-striped">
                <thead>
                    <tr><th>ID</th><th>Product Name</th><th>Language</th><th>SEO URL Alias</th></tr>
                </thead>
                <tbody>
                    <?php foreach(array_slice($products_seo_status, 0, 5) as $product): // Show a few examples ?>
                    <tr>
                        <td><?php echo $product['id']; ?></td>
                        <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($product['language']); ?></td>
                        <td><input type="text" class="form-control form-control-sm" value="<?php echo htmlspecialchars($product['seo_url']); ?>"></td>
                    </tr>
                    <?php endforeach; ?>
                     <tr><td colspan="4" class="text-center">... more products ...</td></tr>
                </tbody>
            </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="alert('Bulk save action - Placeholder')">Save All Changes</button>
      </div>
    </div>
  </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterForm = document.querySelector('.card-body form');
    if(filterForm) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Product SEO status filtering would be applied here. (Placeholder)');
        });
    }
});

function startBatchGeneration() {
    // Placeholder for batch generation process
    const progressDiv = document.getElementById('generateProgress');
    const progressBar = progressDiv.querySelector('.progress-bar');
    const progressText = document.getElementById('generateProgressText');
    
    progressDiv.style.display = 'block';
    progressBar.style.width = '0%';
    progressText.textContent = 'Starting generation...';

    let progress = 0;
    const interval = setInterval(function() {
        progress += 10;
        progressBar.style.width = progress + '%';
        progressBar.setAttribute('aria-valuenow', progress);
        progressText.textContent = 'Processing ' + progress + '%...';
        if (progress >= 100) {
            clearInterval(interval);
            progressText.textContent = 'Batch generation complete! (Placeholder)';
            // setTimeout(() => { progressDiv.style.display = 'none'; }, 2000); // Hide after a delay
        }
    }, 200);
    // alert('Batch SEO URL generation started! (Placeholder)');
}
</script>
