<?php
// modules/llm/settings.php
// This file is included by modules/llm/index.php when action=settings

$page_title = "LLM Configuration - OpenCart Manager";

// Placeholder data for settings
$llm_config = [
    'openai_api_key' => 'sk-xxxxxxxxxxxxxxxxx_placeholder', // Example, DO NOT COMMIT REAL KEYS
    'claude_api_key' => 'claude-xxxxxxxxxxxx_placeholder',
    'active_provider' => 'openai', // 'openai', 'claude', etc.
    'seo_url_prompt' => "Generate a concise, SEO-friendly URL slug for a product titled '{product_title}' with description: '{product_description}'. The slug should be lowercase, hyphenated, and under 70 characters.",
    'seo_description_prompt' => "Generate a compelling, SEO-friendly meta description (max 160 characters) for a product titled '{product_title}' with features: '{product_features}'. Highlight key benefits.",
];

$supported_providers = [
    'none' => 'None (Disable LLM Features)',
    'openai' => 'OpenAI (GPT Models)',
    'claude' => 'Anthropic (Claude Models)',
    // Add other providers as needed
];

$language_specific_prompts = [
    'en' => [
        'seo_url_prompt' => $llm_config['seo_url_prompt'], // Default to global
        'seo_description_prompt' => $llm_config['seo_description_prompt'],
    ],
    'es' => [
        'seo_url_prompt' => "Genera un slug de URL conciso y optimizado para SEO para un producto titulado '{product_title}' con descripción: '{product_description}'. El slug debe estar en minúsculas, con guiones y menos de 70 caracteres.",
        'seo_description_prompt' => "Genera una meta descripción atractiva y optimizada para SEO (máx. 160 caracteres) para un producto titulado '{product_title}' con características: '{product_features}'. Destaca los beneficios clave.",
    ]
];

$supported_languages = get_supported_languages(); // from includes/functions.php

// Handle form submission (placeholder)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_llm_settings'])) {
    // CSRF token validation would go here
    // sanitize_input($_POST) would be used
    echo '<div class="alert alert-success">LLM settings saved (Placeholder - no actual save yet).</div>';
    // In a real app, you would:
    // 1. Validate inputs
    // 2. Encrypt API keys before saving to database or llm-config.php
    // 3. Update the configuration
    $llm_config['active_provider'] = sanitize_input($_POST['active_provider'] ?? 'none');
    $llm_config['openai_api_key'] = sanitize_input($_POST['openai_api_key'] ?? '');
    $llm_config['claude_api_key'] = sanitize_input($_POST['claude_api_key'] ?? '');
    $llm_config['seo_url_prompt'] = sanitize_input($_POST['seo_url_prompt'] ?? '');
    // ... and so on for other fields
}

?>

<div class="container-fluid">
    <!-- Page Title -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-cogs me-2"></i><?php echo $page_title; ?></h1>
        <a href="<?php echo BASE_URL; ?>index.php?module=llm" class="btn btn-sm btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Back to LLM Dashboard</a>
    </div>

    <form method="POST" action="<?php echo BASE_URL; ?>index.php?module=llm&action=settings">
        <?php csrf_input_field(); ?>

        <!-- API Key Management -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-key me-1"></i>API Key Management</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="active_provider" class="form-label">Active LLM Provider</label>
                    <select class="form-select" id="active_provider" name="active_provider">
                        <?php foreach($supported_providers as $code => $name): ?>
                            <option value="<?php echo $code; ?>" <?php echo ($llm_config['active_provider'] === $code) ? 'selected' : ''; ?>><?php echo htmlspecialchars($name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="openai_api_key" class="form-label">OpenAI API Key</label>
                    <input type="password" class="form-control" id="openai_api_key" name="openai_api_key" value="<?php echo htmlspecialchars($llm_config['openai_api_key']); ?>" placeholder="Enter your OpenAI API Key">
                    <small class="form-text text-muted">API keys are stored securely (placeholder - actual encryption to be implemented).</small>
                </div>

                <div class="mb-3">
                    <label for="claude_api_key" class="form-label">Anthropic (Claude) API Key</label>
                    <input type="password" class="form-control" id="claude_api_key" name="claude_api_key" value="<?php echo htmlspecialchars($llm_config['claude_api_key']); ?>" placeholder="Enter your Claude API Key">
                </div>
                <!-- Add more provider key inputs as needed -->
            </div>
        </div>

        <!-- Prompt Templates -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-file-alt me-1"></i>Global Prompt Templates</h6>
            </div>
            <div class="card-body">
                <p class="text-muted"><small>Use placeholders like <code>{product_title}</code>, <code>{product_description}</code>, <code>{product_features}</code>, <code>{target_language}</code> where applicable. These can be overridden by language-specific prompts.</small></p>
                <div class="mb-3">
                    <label for="seo_url_prompt" class="form-label">Default SEO URL Generation Prompt</label>
                    <textarea class="form-control" id="seo_url_prompt" name="seo_url_prompt" rows="3"><?php echo htmlspecialchars($llm_config['seo_url_prompt']); ?></textarea>
                </div>
                <div class="mb-3">
                    <label for="seo_description_prompt" class="form-label">Default SEO Meta Description Prompt</label>
                    <textarea class="form-control" id="seo_description_prompt" name="seo_description_prompt" rows="3"><?php echo htmlspecialchars($llm_config['seo_description_prompt']); ?></textarea>
                </div>
                <!-- Add more global prompt template fields as needed -->
            </div>
        </div>
        
        <!-- Language-Specific Prompt Configurations -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-language me-1"></i>Language-Specific Prompt Configurations</h6>
            </div>
            <div class="card-body">
                <div class="accordion" id="languagePromptsAccordion">
                    <?php foreach ($supported_languages as $lang_code => $lang_details): ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading-<?php echo $lang_code; ?>">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?php echo $lang_code; ?>" aria-expanded="false" aria-controls="collapse-<?php echo $lang_code; ?>">
                                    <?php echo htmlspecialchars($lang_details['name']); ?> Prompts
                                </button>
                            </h2>
                            <div id="collapse-<?php echo $lang_code; ?>" class="accordion-collapse collapse" aria-labelledby="heading-<?php echo $lang_code; ?>" data-bs-parent="#languagePromptsAccordion">
                                <div class="accordion-body">
                                    <div class="mb-3">
                                        <label for="lang_seo_url_prompt_<?php echo $lang_code; ?>" class="form-label">SEO URL Prompt (<?php echo htmlspecialchars($lang_details['name']); ?>)</label>
                                        <textarea class="form-control" id="lang_seo_url_prompt_<?php echo $lang_code; ?>" name="lang_prompts[<?php echo $lang_code; ?>][seo_url_prompt]" rows="3" placeholder="Leave blank to use global default."><?php echo htmlspecialchars($language_specific_prompts[$lang_code]['seo_url_prompt'] ?? ''); ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="lang_seo_description_prompt_<?php echo $lang_code; ?>" class="form-label">SEO Meta Description Prompt (<?php echo htmlspecialchars($lang_details['name']); ?>)</label>
                                        <textarea class="form-control" id="lang_seo_description_prompt_<?php echo $lang_code; ?>" name="lang_prompts[<?php echo $lang_code; ?>][seo_description_prompt]" rows="3" placeholder="Leave blank to use global default."><?php echo htmlspecialchars($language_specific_prompts[$lang_code]['seo_description_prompt'] ?? ''); ?></textarea>
                                    </div>
                                    <!-- Add more language-specific prompt fields as needed -->
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <button type="submit" name="save_llm_settings" class="btn btn-success"><i class="fas fa-save me-1"></i> Save LLM Settings</button>
        </div>
    </form>
</div>

<script>
// Any specific JS for LLM settings page
document.addEventListener('DOMContentLoaded', function() {
    // Example: Show/hide API key fields based on selected provider
    const providerSelect = document.getElementById('active_provider');
    const openaiKeyDiv = document.getElementById('openai_api_key').closest('.mb-3');
    const claudeKeyDiv = document.getElementById('claude_api_key').closest('.mb-3');

    function toggleProviderKeys() {
        const selectedProvider = providerSelect.value;
        if (openaiKeyDiv) openaiKeyDiv.style.display = (selectedProvider === 'openai' || selectedProvider === 'none') ? 'block' : 'none'; // Show for none too, or decide UX
        if (claudeKeyDiv) claudeKeyDiv.style.display = (selectedProvider === 'claude' || selectedProvider === 'none') ? 'block' : 'none';

        // For a 'none' provider, you might want to hide all key fields
        if (selectedProvider === 'none') {
             if (openaiKeyDiv) openaiKeyDiv.style.display = 'none';
             if (claudeKeyDiv) claudeKeyDiv.style.display = 'none';
        } else if (selectedProvider === 'openai') {
             if (claudeKeyDiv) claudeKeyDiv.style.display = 'none';
        } else if (selectedProvider === 'claude') {
             if (openaiKeyDiv) openaiKeyDiv.style.display = 'none';
        }


    }

    if (providerSelect) {
        providerSelect.addEventListener('change', toggleProviderKeys);
        toggleProviderKeys(); // Initial call
    }
});
</script>
