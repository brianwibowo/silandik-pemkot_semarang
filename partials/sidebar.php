<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
if (!isset($conn)) {
    @include_once $_SERVER['DOCUMENT_ROOT'] . '/koneksi.php';
}

/**
 * NavbarHelper Class
 * Handles all navbar-related logic and data
 */
class NavbarHelper
{
    private $conn;
    private $userData;
    private $roleConfig;

    public function __construct($connection)
    {
        $this->conn = $connection;
        $this->initRoleConfig();
        $this->loadUserData();
    }

    /**
     * Initialize role configuration
     */
    private function initRoleConfig()
    {
        $this->roleConfig = [
            'admin' => [
                'color' => 'danger',
                'gradient' => 'linear-gradient(135deg, #ff6b6b, #ee5253)',
                'icon' => 'fa-user-shield',
                'label' => 'Admin',
                'bg' => 'bg-danger-gradient'
            ],
            'pengurus' => [
                'color' => 'success',
                'gradient' => 'linear-gradient(135deg, #20bf6b, #0bb7af)',
                'icon' => 'fa-user-tie',
                'label' => 'Pengurus',
                'bg' => 'bg-success-gradient'
            ],
            'umum' => [
                'color' => 'primary',
                'gradient' => 'linear-gradient(135deg, #4e73df, #224abe)',
                'icon' => 'fa-user',
                'label' => 'Umum',
                'bg' => 'bg-primary-gradient'
            ]
        ];
    }

    /**
     * Load user data from session and database
     */
    private function loadUserData()
    {
        $this->userData = [
            'is_logged_in' => isset($_SESSION['role']),
            'username' => $_SESSION['username'] ?? '',
            'email' => $_SESSION['email'] ?? '', // MODIFIED: Added email from session
            'role' => $_SESSION['role'] ?? null,
            'has_pending_request' => false
        ];

        if ($this->userData['is_logged_in'] && $this->userData['role'] === 'umum') {
            $this->checkPendingRequest();
        }
    }

    /**
     * Check if user has pending pengurus request
     */
    private function checkPendingRequest()
    {
        $username = $this->conn->real_escape_string($this->userData['username']);
        $query = "SELECT request_pengurus FROM users WHERE username = '$username' LIMIT 1";
        $result = $this->conn->query($query);

        if ($result && $result->num_rows === 1) {
            $data = $result->fetch_assoc();
            $this->userData['has_pending_request'] = ($data['request_pengurus'] == 1);
        }
    }

    /**
     * Get current user data
     */
    public function getUserData()
    {
        return $this->userData;
    }

    /**
     * Get role configuration
     */
    public function getRoleConfig($role = null)
    {
        if ($role === null) {
            $role = $this->userData['role'];
        }

        $config = $this->roleConfig[$role] ?? [
            'color' => 'secondary',
            'gradient' => 'linear-gradient(135deg, #6c757d, #495057)',
            'icon' => 'fa-user',
            'label' => ucfirst($role),
            'bg' => 'bg-secondary-gradient'
        ];

        // Add pending label if user has pending request
        if ($role === 'umum' && $this->userData['has_pending_request']) {
            $config['label'] .= ' (Pending)';
        }

        return $config;
    }

    /**
     * Check if current page is active
     */
    public static function isActive($path)
    {
        return strpos($_SERVER['PHP_SELF'], $path) !== false ? 'active' : '';
    }

    /**
     * Check if any path in array is active
     */
    public static function isAnyActive($paths)
    {
        foreach ($paths as $path) {
            if (self::isActive($path)) {
                return 'active';
            }
        }
        return '';
    }
}

// Initialize helper
$navbarHelper = new NavbarHelper($conn);
$userData = $navbarHelper->getUserData();
$roleConfig = $userData['is_logged_in'] ? $navbarHelper->getRoleConfig() : null;
?>

<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="/index.php">
            <img src="/assets/logo_dinas.png" alt="Logo Dinas" width="40" height="32">
            SILANDIK
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav me-auto">
                <?php echo renderMainMenu(); ?>
            </ul>

            <div class="auth-section ms-lg-3">
                <?php if ($userData['is_logged_in']) : ?>
                    <?php echo renderAuthenticatedUser($userData, $roleConfig); ?>
                <?php else : ?>
                    <?php echo renderGuestUser(); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<?php
/**
 * Render main navigation menu
 */
function renderMainMenu()
{
    $menuItems = [
        [
            'type' => 'link',
            'href' => '/index.php',
            'icon' => 'fa-home',
            'label' => 'Beranda',
            'active_path' => '/index.php'
        ],
        [
            'type' => 'dropdown',
            'id' => 'navbarDropdownRegulasi',
            'icon' => 'fa-gavel',
            'label' => 'Regulasi',
            'active_paths' => ['/features/regulasi/dasar_hukum.php'],
            'items' => [
                [
                    'href' => '/features/regulasi/dasar_hukum.php',
                    'icon' => 'fa-balance-scale',
                    'label' => 'Dasar Hukum'
                ]
            ]
        ],
        [
            'type' => 'dropdown',
            'id' => 'navbarDropdownSekolah',
            'icon' => 'fa-school',
            'label' => 'Sekolah Inklusi',
            'active_paths' => ['/features/kategori_data/sekolah_inklusi/', '/features/kategori_data/siswa/'],
            'items' => [
                [
                    'href' => '/features/kategori_data/sekolah_inklusi/data_sekolah_inklusi.php',
                    'icon' => 'fa-building',
                    'label' => 'Data Sekolah Inklusi'
                ],
                [
                    'href' => '/features/kategori_data/siswa/data_siswa.php',
                    'icon' => 'fa-users',
                    'label' => 'Data Siswa'
                ]
            ]
        ],
        [
            'type' => 'link',
            'href' => '/features/info_sekolah_inklusi/informasi_sekolah_inklusi.php',
            'icon' => 'fa-info-circle',
            'label' => 'Informasi Sekolah Inklusi',
            'active_path' => '/features/info_sekolah_inklusi/informasi_sekolah_inklusi.php'
        ],
        [
            'type' => 'link',
            'href' => '/features/dokumen_kurikulum/dokumen_kurikulum_inklusi.php',
            'icon' => 'fa-file-alt',
            'label' => 'Dokumen Kurikulum Inklusi',
            'active_path' => '/features/dokumen_kurikulum/dokumen_kurikulum_inklusi.php'
        ]
    ];

    $html = '';
    foreach ($menuItems as $item) {
        if ($item['type'] === 'link') {
            $html .= renderMenuItem($item);
        } else {
            $html .= renderDropdownMenu($item);
        }
    }

    return $html;
}

/**
 * Render single menu item
 */
function renderMenuItem($item)
{
    $active = NavbarHelper::isActive($item['active_path']);
    return sprintf(
        '<li class="nav-item">
            <a class="nav-link %s" href="%s">
                <i class="fas %s"></i> %s
            </a>
        </li>',
        $active,
        htmlspecialchars($item['href']),
        htmlspecialchars($item['icon']),
        htmlspecialchars($item['label'])
    );
}

/**
 * Render dropdown menu
 */
function renderDropdownMenu($item)
{
    $active = NavbarHelper::isAnyActive($item['active_paths']);

    $html = sprintf(
        '<li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle %s" href="#" id="%s" role="button" 
               data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas %s"></i> %s
            </a>
            <ul class="dropdown-menu" aria-labelledby="%s">',
        $active,
        htmlspecialchars($item['id']),
        htmlspecialchars($item['icon']),
        htmlspecialchars($item['label']),
        htmlspecialchars($item['id'])
    );

    foreach ($item['items'] as $subItem) {
        $subActive = NavbarHelper::isActive($subItem['href']);
        $html .= sprintf(
            '<li>
                <a class="dropdown-item %s" href="%s">
                    <i class="fas %s"></i> %s
                </a>
            </li>',
            $subActive,
            htmlspecialchars($subItem['href']),
            htmlspecialchars($subItem['icon']),
            htmlspecialchars($subItem['label'])
        );
    }

    $html .= '</ul></li>';
    return $html;
}

/**
 * Render authenticated user section
 */
function renderAuthenticatedUser($userData, $roleConfig)
{
    $html = '<div class="d-flex align-items-center gap-3">';

    // Admin Panel Button
    if ($userData['role'] === 'admin') {
        $html .= renderAdminPanelButton();
    }

    // Request Pengurus Button
    if ($userData['role'] === 'umum') {
        $html .= renderRequestPengurusButton($userData['has_pending_request']);
    }

    // User Profile Dropdown
    $html .= renderUserProfile($userData, $roleConfig);

    $html .= '</div>';
    return $html;
}

/**
 * Render admin panel button
 */
function renderAdminPanelButton()
{
    return '
    <a href="/authentification/admin.php" 
       class="nav-link-fancy fancy-button fancy-button-warning admin-panel-button"
       data-bs-toggle="tooltip" 
       data-bs-placement="bottom" 
       title="Akses Panel Admin">
        <div class="admin-icon-wrapper">
            <i class="fas fa-gear icon-floating"></i>
        </div>
        <span class="d-none d-sm-inline">Admin Panel</span>
    </a>';
}

/**
 * Render request pengurus button
 */
function renderRequestPengurusButton($hasPendingRequest)
{
    if ($hasPendingRequest) {
        return '
        <div class="status-badge status-badge-warning" 
             data-bs-toggle="tooltip" 
             data-bs-placement="bottom" 
             title="Permintaan Anda sedang menunggu persetujuan admin">
            <i class="fas fa-clock-rotate-left me-1 fa-spin-pulse"></i>
            <span class="d-none d-sm-inline">Pending</span>
        </div>';
    }

    return '
    <form action="/authentification/request_pengurus.php" method="POST" class="d-inline-block">
        <button type="submit" class="nav-link-fancy fancy-button fancy-button-success">
            <i class="fas fa-user-plus me-1"></i>
            <span class="d-none d-sm-inline">Request Pengurus</span>
        </button>
    </form>';
}

/**
 * Render user profile dropdown
 */
function renderUserProfile($userData, $roleConfig)
{
    // MODIFIED: Use email instead of username, with a fallback.
    $email = htmlspecialchars($userData['email'] ?: 'User Email');
    $role = $userData['role'];
    $hasPendingRequest = $userData['has_pending_request'];

    return sprintf('
    <div class="dropdown">
        <button class="user-profile-button" 
                type="button" 
                data-bs-toggle="dropdown" 
                data-bs-auto-close="outside"
                aria-expanded="false">
            <div class="user-avatar-wrapper">
                <div class="user-avatar %s" data-role="%s">
                    <i class="fas %s"></i>
                    %s
                </div>
            </div>
            <div class="user-info d-none d-sm-flex">
                <span class="username">%s</span>
                <span class="role-badge role-%s">
                    <i class="fas %s fa-sm me-1"></i>
                    %s
                </span>
            </div>
            <i class="fas fa-chevron-down dropdown-arrow"></i>
        </button>
        
        <div class="dropdown-menu dropdown-menu-end user-dropdown">
            <div class="dropdown-header d-flex align-items-center gap-3 d-sm-none">
                <div class="user-avatar %s" data-role="%s">
                    <i class="fas %s"></i>
                </div>
                <div>
                    <h6 class="mb-0">%s</h6>
                    <span class="role-badge role-%s mt-1">
                        <i class="fas %s fa-sm me-1"></i>
                        %s
                    </span>
                </div>
            </div>
            
            <div class="dropdown-divider d-sm-none"></div>
            
            <a class="dropdown-item-fancy" href="/authentification/logout.php">
                <i class="fas fa-sign-out-alt"></i>
                <span>Keluar</span>
            </a>
        </div>
    </div>',
        $roleConfig['bg'], $role, $roleConfig['icon'],
        $hasPendingRequest ? '<span class="notification-dot"></span>' : '',
        $email, // MODIFIED: Use email here
        $role, $roleConfig['icon'], $roleConfig['label'],
        $roleConfig['bg'], $role, $roleConfig['icon'],
        $email, // MODIFIED: And here
        $role, $roleConfig['icon'], $roleConfig['label']
    );
}


/**
 * Render guest user buttons
 */
function renderGuestUser()
{
    return '
    <div class="auth-buttons">
        <a href="/authentification/login.php" class="nav-link-fancy fancy-button fancy-button-outline">
            <i class="fas fa-sign-in-alt me-1"></i>
            <span class="d-none d-sm-inline">Masuk</span>
        </a>
        <a href="/authentification/register.php" class="nav-link-fancy fancy-button fancy-button-primary">
            <i class="fas fa-user-plus me-1"></i>
            <span class="d-none d-sm-inline">Daftar</span>
        </a>
    </div>';
}
?>

<link rel="stylesheet" href="/assets/css/navbar-styles.css">

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>

<style>

    .navbar-brand{
        margin-right: 1.5em;
    }
    /* ==========================================
   NAVBAR STYLES - SILANDIK
   ========================================== */

    /* Gradient Backgrounds */
    .bg-primary-gradient {
        background: linear-gradient(135deg, #4e73df, #224abe);
    }

    .bg-success-gradient {
        background: linear-gradient(135deg, #20bf6b, #0bb7af);
    }

    .bg-danger-gradient {
        background: linear-gradient(135deg, #ff6b6b, #ee5253);
    }

    .bg-warning-gradient {
        background: linear-gradient(135deg, #ffc107, #ff9800);
    }

    .bg-secondary-gradient {
        background: linear-gradient(135deg, #6c757d, #495057);
    }

    /* ==========================================
   FANCY BUTTONS
   ========================================== */
    .nav-link-fancy {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        border-radius: 50rem;
        transition: all 0.3s ease;
        text-decoration: none;
        position: relative;
        overflow: hidden;
        z-index: 1;
    }

    .fancy-button {
        position: relative;
        border: none;
        background: none;
        padding: 0.5rem 1.25rem;
        font-weight: 500;
        cursor: pointer;
        overflow: hidden;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .fancy-button::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0));
        transform: translateX(-100%);
        transition: transform 0.5s ease;
        z-index: -1;
    }

    .fancy-button:hover::before {
        transform: translateX(0);
    }

    .fancy-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    }

    /* Button Variants */
    .fancy-button-warning {
        background: linear-gradient(135deg, #ffd43b, #ffa502);
        color: #fff;
    }

    .fancy-button-success {
        background: linear-gradient(135deg, #20bf6b, #0bb7af);
        color: #fff;
    }

    .fancy-button-primary {
        background: linear-gradient(135deg, #4e73df, #224abe);
        color: #fff;
    }

    .fancy-button-outline {
        background: transparent;
        border: 2px solid #4e73df;
        color: #4e73df;
    }

    .fancy-button-outline:hover {
        background: #4e73df;
        color: #fff;
        border-color: #4e73df;
    }

    /* ==========================================
   STATUS BADGE
   ========================================== */
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.4rem 1rem;
        border-radius: 50rem;
        font-size: 0.875rem;
        font-weight: 500;
        background: rgba(255, 193, 7, 0.1);
        color: #997404;
        border: 1px solid rgba(255, 193, 7, 0.2);
    }

    .status-badge i {
        margin-right: 0.5rem;
    }

    /* ==========================================
   USER PROFILE SECTION
   ========================================== */
    /* MODIFIED: Improved user profile button styles */
    .user-profile-button {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.25rem 0.75rem 0.25rem 0.25rem;
        /* Adjusted padding */
        border: 1px solid #e3e6f0;
        /* Added border */
        background: #f8f9fa;
        /* Added light background */
        cursor: pointer;
        transition: all 0.3s ease;
        border-radius: 50rem;
        /* Rounded pill shape */
    }

    .user-profile-button:hover,
    .user-profile-button:focus {
        background-color: #e9ecef;
        border-color: #d1d3e2;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }

    .user-profile-button:hover .user-avatar {
        transform: scale(1.05);
    }

    .user-avatar-wrapper {
        position: relative;
        padding: 2px;
        border-radius: 50%;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.2), rgba(255, 255, 255, 0.1));
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 1rem;
        position: relative;
        transition: all 0.3s ease;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .user-avatar::after {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: 50%;
        border: 2px solid rgba(255, 255, 255, 0.3);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
            opacity: 1;
        }

        50% {
            transform: scale(1.1);
            opacity: 0.5;
        }

        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    .user-info {
        flex-direction: column;
        align-items: flex-start;
    }

    .username {
        font-weight: 600;
        color: #2d3436;
        font-size: 0.95rem;
        /* Truncate long emails */
        max-width: 150px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* ==========================================
   ROLE BADGES
   ========================================== */
    .role-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 500;
        background: rgba(0, 0, 0, 0.05);
    }

    .role-admin {
        color: #dc3545;
    }

    .role-pengurus {
        color: #198754;
    }

    .role-umum {
        color: #0d6efd;
    }

    /* ==========================================
   NOTIFICATION DOT
   ========================================== */
    .notification-dot {
        position: absolute;
        top: -2px;
        right: -2px;
        width: 10px;
        height: 10px;
        background: #ffc107;
        border-radius: 50%;
        border: 2px solid #fff;
        animation: blink 1.5s infinite;
    }

    @keyframes blink {
        0% {
            opacity: 1;
        }

        50% {
            opacity: 0.5;
        }

        100% {
            opacity: 1;
        }
    }

    /* ==========================================
   DROPDOWN ARROW
   ========================================== */
    .dropdown-arrow {
        font-size: 0.75rem;
        color: #6c757d;
        transition: transform 0.3s ease;
    }

    .dropdown.show .dropdown-arrow {
        transform: rotate(180deg);
    }

    /* ==========================================
   USER DROPDOWN MENU
   ========================================== */
    .user-dropdown {
        min-width: 240px;
        padding: 0.5rem;
        border: none;
        border-radius: 1rem;
        background: #fff;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .dropdown-header {
        padding: 1rem;
        border-radius: 0.5rem;
        background: rgba(0, 0, 0, 0.02);
    }

    .dropdown-item-fancy {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        color: #dc3545;
        text-decoration: none;
        border-radius: 0.5rem;
        transition: all 0.2s ease;
    }

    .dropdown-item-fancy:hover {
        background: rgba(220, 53, 69, 0.1);
        color: #dc3545;
    }

    .dropdown-item-fancy i {
        font-size: 1rem;
        width: 1.25rem;
        text-align: center;
    }

    /* ==========================================
   ADMIN PANEL BUTTON
   ========================================== */
    .admin-icon-wrapper {
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .icon-floating {
        animation: float 2s ease-in-out infinite;
        margin-right: 0.5em;
    }

    @keyframes float {

        0%,
        100% {
            transform: translateY(0);
        }

        50% {
            transform: translateY(-3px);
        }
    }

    /* ==========================================
   AUTH BUTTONS SECTION
   ========================================== */
    .auth-buttons {
        display: flex;
        gap: 0.75rem;
        align-items: center;
    }

    /* ==========================================
   RESPONSIVE STYLES
   ========================================== */
    @media (max-width: 991px) {
        .auth-section {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
        }
    }

    @media (max-width: 576px) {
        .user-dropdown {
            position: fixed !important;
            bottom: 0 !important;
            left: 0 !important;
            right: 0 !important;
            width: 100% !important;
            margin: 0;
            border-radius: 1rem 1rem 0 0;
            animation: slideUp 0.3s ease;
        }

        @keyframes slideUp {
            from {
                transform: translateY(100%);
            }

            to {
                transform: translateY(0);
            }
        }

        .dropdown-header {
            background: transparent;
        }

        .auth-buttons {
            justify-content: stretch;
        }

        .auth-buttons .nav-link-fancy {
            flex: 1;
            justify-content: center;
        }
    }

    /* ==========================================
   ACCESSIBILITY IMPROVEMENTS
   ========================================== */
    .nav-link-fancy:focus,
    .fancy-button:focus,
    .user-profile-button:focus {
        outline: 2px solid #4e73df;
        outline-offset: 2px;
    }

    /* ==========================================
   PRINT STYLES
   ========================================== */
    @media print {

        .auth-section,
        .navbar-toggler {
            display: none !important;
        }
    }
</style>