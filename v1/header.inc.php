<?php
require_once('includes/midas.inc.php');
?>
<html lang="en">
<head>
    <base href="<?php echo SITE_WS_PATH; ?>" />
    <title><?php echo SITE_NAME; ?>:: Control Panel</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="shortcut icon" href="assets/media/logos/favicon.ico" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <link href="assets/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />
    <link href="assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
    <script src="assets/js/custom.js"></script>
    <script>
        // Frame-busting to prevent site from being loaded within a frame without permission (click-jacking) if (window.top != window.self) { window.top.location.replace(window.self.location.href); }
        
        // Get current page for active menu detection
        const currentPage = window.location.pathname.split('/').pop() || 'my-secure-index.php';
        const siteUrl = '<?php echo SITE_URL; ?>';
        
        // Dynamic Menu Data Structure
        const menuData = {
            'dashboards': [
                { title: 'Home', link: siteUrl + '/my-secure-index.php', page: 'my-secure-index.php' },
                { title: 'Quick Contact', link: siteUrl + '/quick-contact.php', page: 'quick-contact.php' },
                { title: 'Transfer & Tour', link: siteUrl + '/transfer-tour.php', page: 'transfer-tour.php' },
                { title: 'Trip Planning', link: siteUrl + '/trip-planning.php', page: 'trip-planning.php' }
            ],
            'files': [
                { title: 'File Manager', link: siteUrl + '/file-manager.php', page: 'file-manager.php' },
                { title: 'Upload Files', link: siteUrl + '/upload.php', page: 'upload.php' },
                { title: 'File Categories', link: siteUrl + '/file-categories.php', page: 'file-categories.php' }
            ],
            'users': [
                { title: 'User List', link: siteUrl + '/users/list.php', page: 'list.php' },
                { title: 'Add User', link: siteUrl + '/users/add.php', page: 'add.php' },
                { title: 'User Roles', link: siteUrl + '/users/roles.php', page: 'roles.php' },
                { title: 'Permissions', link: siteUrl + '/users/permissions.php', page: 'permissions.php' }
            ],
            'products': [
                { title: 'Product List', link: siteUrl + '/products/list.php', page: 'list.php' },
                { title: 'Add Product', link: siteUrl + '/products/add.php', page: 'add.php' },
                { title: 'Categories', link: siteUrl + '/products/categories.php', page: 'categories.php' },
                { title: 'Inventory', link: siteUrl + '/products/inventory.php', page: 'inventory.php' }
            ],
            'suppliers': [
                { title: 'Supplier List', link: siteUrl + '/suppliers/list.php', page: 'list.php' },
                { title: 'Add Supplier', link: siteUrl + '/suppliers/add.php', page: 'add.php' },
                { title: 'Supplier Orders', link: siteUrl + '/suppliers/orders.php', page: 'orders.php' }
            ]
        };
        
        // Function to check if menu item is active based on current page
        function isMenuItemActive(item) {
            return currentPage === item.page;
        }
        
        // Function to get active primary menu based on current page
        function getActivePrimaryMenu() {
            for (const [menuKey, items] of Object.entries(menuData)) {
                if (items.some(item => isMenuItemActive(item))) {
                    return menuKey;
                }
            }
            return 'dashboards'; // default
        }
        
        // Function to update secondary menu
        function updateSecondaryMenu(menuKey) {
            const secondaryMenuContainer = document.getElementById('kt_app_header_secondary_menu');
            if (!secondaryMenuContainer || !menuData[menuKey]) return;
            
            const menuItems = menuData[menuKey];
            let menuHTML = '';
            
            menuItems.forEach((item, index) => {
                const activeClass = isMenuItemActive(item) ? ' active' : '';
                menuHTML += `
                    <div class="menu-item">
                        <a class="menu-link${activeClass}" href="${item.link}">
                            <span class="menu-title">${item.title}</span>
                        </a>
                    </div>`;
                
                // Add separator between items (except last item)
                if (index < menuItems.length - 1) {
                    menuHTML += `
                    <div class="menu-item">
                        <div class="menu-content">
                            <div class="menu-separator"></div>
                        </div>
                    </div>`;
                }
            });
            
            secondaryMenuContainer.innerHTML = menuHTML;
        }
        
        // Function to handle primary menu clicks
        function handlePrimaryMenuClick(event, menuKey) {
            event.preventDefault();
            
            // Remove active class from all primary menu items
            document.querySelectorAll('.menu-item.here').forEach(item => {
                item.classList.remove('here', 'menu-here-bg');
            });
            
            // Add active class to clicked menu item
            const clickedMenuItem = event.target.closest('.menu-item');
            if (clickedMenuItem) {
                clickedMenuItem.classList.add('here', 'menu-here-bg');
            }
            
            // Update secondary menu
            updateSecondaryMenu(menuKey);
        }
        
        // Initialize menu system when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Set up click handlers for primary menu items
            const menuItems = {
                'dashboards': document.querySelector('[data-menu="dashboards"]'),
                'files': document.querySelector('[data-menu="files"]'),
                'users': document.querySelector('[data-menu="users"]'),
                'products': document.querySelector('[data-menu="products"]'),
                'suppliers': document.querySelector('[data-menu="suppliers"]')
            };
            
            Object.keys(menuItems).forEach(menuKey => {
                const menuElement = menuItems[menuKey];
                if (menuElement) {
                    menuElement.addEventListener('click', (e) => handlePrimaryMenuClick(e, menuKey));
                }
            });
            
            // Get active menu based on current page
            const activePrimaryMenu = getActivePrimaryMenu();
            
            // Set active primary menu
            Object.keys(menuItems).forEach(menuKey => {
                const menuElement = menuItems[menuKey];
                if (menuElement) {
                    if (menuKey === activePrimaryMenu) {
                        menuElement.classList.add('here', 'menu-here-bg');
                    } else {
                        menuElement.classList.remove('here', 'menu-here-bg');
                    }
                }
            });
            
            // Initialize with active menu based on current page
            updateSecondaryMenu(activePrimaryMenu);
        });
    </script>
    <style>
        .app-header {
            border: none !important;
}
    </style>
</head>
<body id="kt_app_body" data-kt-app-header-fixed="true" data-kt-app-header-fixed-mobile="true" data-kt-app-header-stacked="true" data-kt-app-header-primary-enabled="true" data-kt-app-header-secondary-enabled="true" data-kt-app-toolbar-enabled="true" class="app-default">
    <div class="d-flex flex-column flex-root app-root" id="kt_app_root">
        <div class="app-page flex-column flex-column-fluid" id="kt_app_page">
            <div id="kt_app_header" class="app-header">
                <div class="app-header-primary">
                    <div class="app-container container-fluid d-flex align-items-stretch justify-content-between" id="kt_app_header_primary_container">
                        <div class="d-flex flex-stack flex-grow-1">
                            <div class="d-flex">
                                <div class="app-header-logo d-flex flex-center gap-2 me-lg-15">
                                    <button class="btn btn-icon btn-sm btn-custom d-flex d-lg-none ms-n2" id="kt_app_header_menu_toggle">
                                        <i class="ki-outline ki-abstract-14 fs-2"></i>
                                    </button>
                                    <a href="<?php echo SITE_URL.'/my-secure-index.php'; ?>">
                                        <img alt="Logo" src="assets/media/logos/logo_w_big.png" class="mh-35px" />
                                    </a>
                                </div>
                                <div class="d-flex align-items-stretch" id="kt_app_header_menu_wrapper">
                                    <div class="app-header-menu app-header-mobile-drawer align-items-stretch" data-kt-drawer="true" data-kt-drawer-name="app-header-menu" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'200px', '300px': '250px'}" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_app_header_menu_toggle" data-kt-swapper="true" data-kt-swapper-mode="{default: 'append', lg: 'prepend'}" data-kt-swapper-parent="{default: '#kt_app_body', lg: '#kt_app_header_menu_wrapper'}">
                                        <div class="menu menu-rounded menu-column menu-lg-row menu-active-bg menu-title-gray-700 menu-state-gray-900 menu-icon-gray-500 menu-arrow-gray-500 menu-state-icon-primary menu-state-bullet-primary fw-semibold fs-6 align-items-stretch my-5 my-lg-0 px-2 px-lg-0" id="#kt_app_header_menu" data-kt-menu="true">
                                            <div data-menu="dashboards" class="menu-item here menu-here-bg menu-lg-down-accordion me-0 me-lg-2" style="cursor: pointer;">
                                                <span class="menu-link">
                                                    <span class="menu-title">Dashboards</span>
                                                    <span class="menu-arrow d-lg-none"></span>
                                                </span>
                                            </div>
                                            <div data-menu="files" class="menu-item menu-lg-down-accordion me-0 me-lg-2" style="cursor: pointer;">
                                                <span class="menu-link">
                                                    <span class="menu-title">Files</span>
                                                    <span class="menu-arrow d-lg-none"></span>
                                                </span>
                                            </div>
                                            <div data-menu="users" class="menu-item menu-lg-down-accordion menu-sub-lg-down-indention me-0 me-lg-2" style="cursor: pointer;">
                                                <span class="menu-link">
                                                    <span class="menu-title">Users</span>
                                                    <span class="menu-arrow d-lg-none"></span>
                                                </span>
                                            </div>
                                            <div data-menu="products" class="menu-item menu-lg-down-accordion menu-sub-lg-down-indention me-0 me-lg-2" style="cursor: pointer;">
                                                <span class="menu-link">
                                                    <span class="menu-title">Products</span>
                                                    <span class="menu-arrow d-lg-none"></span>
                                                </span>
                                            </div>
                                            <div data-menu="suppliers" class="menu-item menu-lg-down-accordion menu-sub-lg-down-indention me-0 me-lg-2" style="cursor: pointer;">
                                                <span class="menu-link">
                                                    <span class="menu-title">Suppliers</span>
                                                    <span class="menu-arrow d-lg-none"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="app-navbar flex-shrink-0 gap-2">
                                <div class="app-navbar-item ms-1">
                                    <div class="btn btn-sm btn-icon btn-custom h-35px w-35px" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                                        <i class="ki-outline ki-notification-status fs-3"></i>
                                    </div>
                                </div>
                                <div class="app-navbar-item ms-1">
                                    <div class="cursor-pointer symbol position-relative symbol-35px" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                                        <img src="assets/media/avatars/300-2.jpg" alt="user" />
                                        <span class="bullet bullet-dot bg-success h-6px w-6px position-absolute translate-middle mb-1 bottom-0 start-100 animation-blink"></span>
                                    </div>
                                    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px" data-kt-menu="true">
                                        <div class="menu-item px-3">
                                            <div class="menu-content d-flex align-items-center px-3">
                                                <div class="symbol symbol-50px me-5">
                                                    <img alt="Logo" src="assets/media/avatars/300-2.jpg" />
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <div class="fw-bold d-flex align-items-center fs-5"><?php echo $_SESSION['sess_agent_name']; ?>
                                                        <span class="badge badge-light-success fw-bold fs-8 px-2 py-1 ms-2"><?php echo $_SESSION['sess_agent_type']; ?></span>
                                                    </div>
                                                    <a href="#" class="fw-semibold text-muted text-hover-primary fs-7" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; "><?php echo $_SESSION['sess_agent_email']; ?></a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="separator my-2"></div>
                                        <div class="menu-item px-5" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="left-start" data-kt-menu-offset="-15px, 0">
                                            <a href="#" class="menu-link px-5">
                                                <span class="menu-title position-relative">Mode
                                                    <span class="ms-5 position-absolute translate-middle-y top-50 end-0">
                                                        <i class="ki-outline ki-night-day theme-light-show fs-2"></i>
                                                        <i class="ki-outline ki-moon theme-dark-show fs-2"></i>
                                                    </span></span>
                                            </a>
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-title-gray-700 menu-icon-gray-500 menu-active-bg menu-state-color fw-semibold py-4 fs-base w-150px" data-kt-menu="true" data-kt-element="theme-mode-menu">
                                                <div class="menu-item px-3 my-0">
                                                    <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="light">
                                                        <span class="menu-icon" data-kt-element="icon">
                                                            <i class="ki-outline ki-night-day fs-2"></i>
                                                        </span>
                                                        <span class="menu-title">Light</span>
                                                    </a>
                                                </div>
                                                <div class="menu-item px-3 my-0">
                                                    <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="dark">
                                                        <span class="menu-icon" data-kt-element="icon">
                                                            <i class="ki-outline ki-moon fs-2"></i>
                                                        </span>
                                                        <span class="menu-title">Dark</span>
                                                    </a>
                                                </div>
                                                <div class="menu-item px-3 my-0">
                                                    <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="system">
                                                        <span class="menu-icon" data-kt-element="icon">
                                                            <i class="ki-outline ki-screen fs-2"></i>
                                                        </span>
                                                        <span class="menu-title">System</span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="menu-item px-5 my-1">
                                            <a href="#" class="menu-link px-5">Account Settings</a>
                                        </div>
                                        <div class="menu-item px-5">
                                            <a href="<?php echo SITE_URL; ?>/logout.php" class="menu-link px-5">Sign Out</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="app-navbar-item d-lg-none" title="Show header menu">
                                    <button class="btn btn-sm btn-icon btn-custom h-35px w-35px" id="kt_header_secondary_mobile_toggle">
                                        <i class="ki-outline ki-element-4 fs-2"></i>
                                    </button>
                                </div>
                                <div class="app-navbar-item d-lg-none me-n3" title="Show header menu">
                                    <button class="btn btn-sm btn-icon btn-custom h-35px w-35px" id="kt_app_sidebar_mobile_toggle">
                                        <i class="ki-outline ki-setting-3 fs-2"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="app-header-secondary app-header-mobile-drawer" data-kt-drawer="true" data-kt-drawer-name="app-header-secondary" data-kt-drawer-activate="{default: true, lg: false}" data-kt-drawer-overlay="true" data-kt-drawer-width="250px" data-kt-drawer-direction="start" data-kt-drawer-toggle="#kt_header_secondary_mobile_toggle" data-kt-swapper="true" data-kt-swapper-mode="{default: 'append', lg: 'append'}" data-kt-swapper-parent="{default: '#kt_app_body', lg: '#kt_app_header'}">
                    <div class="d-flex flex-column flex-grow-1 overflow-hidden">
                        <div class="app-header-secondary-menu-main d-flex flex-grow-lg-1 align-items-end pt-3 pt-lg-2 px-3 px-lg-0 w-auto overflow-auto flex-nowrap">
                            <div class="app-container container-fluid">
                                <div class="menu menu-rounded menu-nowrap flex-shrink-0 menu-row menu-active-bg menu-title-gray-700 menu-state-gray-900 menu-icon-gray-500 menu-arrow-gray-500 menu-state-icon-primary menu-state-bullet-primary fw-semibold fs-base align-items-stretch" id="kt_app_header_secondary_menu" data-kt-menu="true">
                                    <!-- Secondary menu items will be dynamically populated by JavaScript -->
                                </div>
                            </div>
                        </div>
                        <div class="app-header-secondary-menu-sub d-flex align-items-stretch flex-grow-1">
                            <div class="app-container d-flex flex-column flex-lg-row align-items-stretch justify-content-lg-between container-fluid">
                            </div>
                        </div>
                    </div>
                </div>
            </div>