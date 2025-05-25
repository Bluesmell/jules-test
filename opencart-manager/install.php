<?php
// install.php - OpenCart Manager Installation Wizard (Placeholder)
// This wizard will guide users through the setup process.

session_start();
require_once 'config/config.php'; // For base constants, if needed early
// Potentially, don't include full app config until DB is set up

$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$error_message = '';
$success_message = '';

// Placeholder for handling form submissions from different steps
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF check would be here
    // if (!validate_csrf_token($_POST['csrf_token'])) { /* error */ }
    
    if (isset($_POST['submit_step1'])) { // Database Configuration
        // Validate and attempt to save DB config (e.g., to a temporary session or try to write config/database.php)
        // For now, just simulate success and move to next step
        $success_message = "Database details (simulated) saved.";
        $_SESSION['install_db_host'] = $_POST['db_host'] ?? 'localhost';
        // ... store other params ...
        $step = 2;
    } elseif (isset($_POST['submit_step2'])) { // OpenCart Compatibility & Admin Setup
        // Perform checks, create admin user for the manager app
        $success_message = "Compatibility checks (simulated) passed. Admin user (simulated) created.";
        $step = 3;
    }
    // ... more steps
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OpenCart Manager - Installation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .installer-container { max-width: 700px; margin-top: 50px; }
    </style>
</head>
<body>
    <div class="container installer-container">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h3 class="text-center mb-0"><i class="fas fa-cogs"></i> OpenCart Manager Installation</h3>
            </div>
            <div class="card-body p-4">
                <?php if ($error_message): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
                <?php endif; ?>
                <?php if ($success_message): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
                <?php endif; ?>

                <?php if ($step === 1): ?>
                    <h4>Step 1: Welcome & Requirements</h4>
                    <p>Welcome to OpenCart Manager! This wizard will guide you through the installation.</p>
                    <h5>Server Requirements (Placeholder)</h5>
                    <ul>
                        <li>PHP 7.4+ (Current: <?php echo phpversion(); ?>) - <i class="fas <?php echo version_compare(phpversion(), '7.4.0', '>=') ? 'fa-check-circle text-success' : 'fa-times-circle text-danger'; ?>"></i></li>
                        <li>MySQLi or PDO_MySQL Extension - <i class="fas fa-check-circle text-success"></i> (Assuming available)</li>
                        <li>Write Permissions for `config/` directory - <i class="fas fa-question-circle text-warning"></i> (Needs check)</li>
                        <li>OpenCart Installation Detected - <i class="fas fa-question-circle text-warning"></i> (Needs check)</li>
                    </ul>
                    <p class="text-center">
                        <a href="?step=2" class="btn btn-primary mt-3">Start Setup <i class="fas fa-arrow-right"></i></a>
                    </p>
                    <hr>
                    <p class="text-muted small">If you encounter issues, ensure your server meets the requirements and the `config/` directory is writable during installation.</p>


                <?php elseif ($step === 2): ?>
                    <h4>Step 2: Database Configuration</h4>
                    <p>Please provide your OpenCart database connection details. These will be used to read OpenCart data and store manager-specific data.</p>
                    <form method="POST" action="?step=2">
                        <?php // csrf_input_field(); ?>
                        <div class="mb-3">
                            <label for="db_host" class="form-label">Database Host</label>
                            <input type="text" class="form-control" id="db_host" name="db_host" value="localhost" required>
                        </div>
                        <div class="mb-3">
                            <label for="db_name" class="form-label">Database Name</label>
                            <input type="text" class="form-control" id="db_name" name="db_name" placeholder="e.g., opencart_db" required>
                        </div>
                        <div class="mb-3">
                            <label for="db_user" class="form-label">Database Username</label>
                            <input type="text" class="form-control" id="db_user" name="db_user" placeholder="e.g., db_user" required>
                        </div>
                        <div class="mb-3">
                            <label for="db_pass" class="form-label">Database Password</label>
                            <input type="password" class="form-control" id="db_pass" name="db_pass">
                        </div>
                        <div class="mb-3">
                            <label for="db_prefix" class="form-label">OpenCart Table Prefix</label>
                            <input type="text" class="form-control" id="db_prefix" name="db_prefix" value="oc_" placeholder="Default: oc_">
                        </div>
                        <button type="submit" name="submit_step1" class="btn btn-primary">Test Connection & Save</button>
                         <a href="?step=1" class="btn btn-outline-secondary">Back</a>
                    </form>

                <?php elseif ($step === 3): ?>
                    <h4>Step 3: OpenCart Integration & Admin User</h4>
                    <p>Verifying OpenCart compatibility and setting up your admin account for OpenCart Manager.</p>
                    <!-- Placeholder for compatibility check results -->
                    <div class="alert alert-info">OpenCart Version Detected: 3.x (Simulated) - Compatible!</div>
                    
                    <h5>Create Admin User for OpenCart Manager</h5>
                     <form method="POST" action="?step=3">
                        <?php // csrf_input_field(); ?>
                        <div class="mb-3">
                            <label for="admin_user" class="form-label">Admin Username</label>
                            <input type="text" class="form-control" id="admin_user" name="admin_user" required>
                        </div>
                         <div class="mb-3">
                            <label for="admin_email" class="form-label">Admin Email</label>
                            <input type="email" class="form-control" id="admin_email" name="admin_email" required>
                        </div>
                        <div class="mb-3">
                            <label for="admin_pass" class="form-label">Admin Password</label>
                            <input type="password" class="form-control" id="admin_pass" name="admin_pass" required>
                        </div>
                        <button type="submit" name="submit_step2" class="btn btn-primary">Create Admin & Proceed</button>
                        <a href="?step=2" class="btn btn-outline-secondary">Back</a>
                    </form>

                <?php elseif ($step === 4): // Final Step ?>
                    <h4>Step 4: Installation Complete!</h4>
                    <div class="alert alert-success">
                        <strong>Congratulations!</strong> OpenCart Manager has been installed successfully.
                    </div>
                    <p><strong>Important Security Note:</strong> For security reasons, please delete or rename the `install.php` file now.</p>
                    <p>You can now log in to your OpenCart Manager:</p>
                    <a href="index.php" class="btn btn-success btn-lg"><i class="fas fa-sign-in-alt"></i> Go to Login Page</a>
                    <hr>
                    <h5>Next Steps:</h5>
                    <ul>
                        <li>Review the <a href="USER_MANUAL.md" target="_blank">User Manual</a> for guidance on using the application.</li>
                        <li>Configure LLM settings if you plan to use AI-powered features.</li>
                    </ul>
                <?php endif; ?>
            </div>
            <div class="card-footer text-center text-muted small">
                OpenCart Manager &copy; <?php echo date("Y"); ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
