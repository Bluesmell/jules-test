<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Ensure session is started
}
require_once dirname(__DIR__) . '/config/config.php'; // Adjust path to config
require_once dirname(__DIR__) . '/includes/functions.php'; // For utility functions like get_supported_languages
require_once dirname(__DIR__) . '/includes/auth.php';     // For is_logged_in()

$current_language_code = get_current_language(); // Assuming get_current_language() is defined
$supported_languages = get_supported_languages(); // Assuming get_supported_languages() is defined

$page_title = isset($page_title) ? htmlspecialchars($page_title) : "OpenCart Manager";
$current_module = isset($_GET['module']) ? $_GET['module'] : 'dashboard';
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($current_language_code); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons (optional) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <script>
        // JavaScript to pass PHP defines to JS
        const BASE_URL = "<?php echo BASE_URL; ?>";
    </script>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?php echo BASE_URL; ?>index.php?module=dashboard">
            <i class="fas fa-chart-line"></i> OpenCart Manager
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_module === 'dashboard') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>index.php?module=dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_module === 'sales') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>index.php?module=sales"><i class="fas fa-dollar-sign"></i> Sales</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_module === 'stock') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>index.php?module=stock"><i class="fas fa-boxes"></i> Stock</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_module === 'customers') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>index.php?module=customers"><i class="fas fa-users"></i> Customers</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_module === 'seo') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>index.php?module=seo"><i class="fas fa-search-location"></i> SEO Links</a>
                </li>
                <?php if (defined('LLM_MODULE_ENABLED') && LLM_MODULE_ENABLED): ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($current_module === 'llm') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>index.php?module=llm"><i class="fas fa-robot"></i> LLM Tools</a>
                </li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="languageDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-globe"></i> <?php echo isset($supported_languages[$current_language_code]['name']) ? $supported_languages[$current_language_code]['name'] : 'Language'; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
                        <?php foreach ($supported_languages as $lang_code => $lang_details): ?>
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>index.php?module=<?php echo $current_module; ?>&lang=<?php echo $lang_code; ?>"><?php echo htmlspecialchars($lang_details['name']); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </li>
                <?php if (is_logged_in()): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user"></i> <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'User'; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>index.php?module=settings&action=profile"><i class="fas fa-user-cog"></i> Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>index.php?module=auth&action=logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                         <a class="nav-link <?php echo ($current_module === 'auth') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>index.php?module=auth&action=login"><i class="fas fa-sign-in-alt"></i> Login</a>
                    </li>
                <?php endif; ?>
                 <li class="nav-item">
                    <button class="nav-link" id="theme-toggle-btn">
                        <i class="fas fa-moon" id="theme-icon"></i> <!-- Icon changes with theme -->
                    </button>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <!-- Breadcrumbs placeholder -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>index.php?module=dashboard">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo ucfirst(htmlspecialchars($current_module)); ?></li>
                </ol>
            </nav>
        </div>
    </div>

    <main role="main" class="pb-3">
        <!-- Content will be loaded here by individual module files -->
