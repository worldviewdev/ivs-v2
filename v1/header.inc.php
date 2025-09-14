<?php
require_once('includes/midas.inc.php');
?>
<html lang="en">
<head>
    <base href="<?php echo SITE_WS_PATH; ?>" />
    <title><?php echo SITE_NAME; ?>:: Control Panel</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="shortcut icon" href="<?php echo SITE_WS_PATH; ?>/assets/media/logos/favicon.ico" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    <link href="<?php echo SITE_WS_PATH; ?>/assets/css/custom.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo SITE_WS_PATH; ?>/assets/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo SITE_WS_PATH; ?>/assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo SITE_WS_PATH; ?>/assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
    <script src="<?php echo SITE_WS_PATH; ?>/assets/plugins/global/plugins.bundle.js"></script>
    <script>
        const currentPage = window.location.pathname.split('/').pop() || 'my-secure-index.php';
        const siteUrl = '<?php echo SITE_URL; ?>';
        const menuData = {
            'dashboards': [{
                    title: 'Home',
                    link: siteUrl + '/my-secure-index.php',
                    page: 'my-secure-index.php'
                }<?php if ($_SESSION['sess_super_admin'] == 'SuperAdmin') { ?>,
                    {
                        title: 'Quick Contact',
                        link: siteUrl + '/quick-contact.php',
                        page: 'quick-contact.php'
                    },
                    {
                        title: 'Transfer & Tour',
                        link: siteUrl + '/transfer-tour.php',
                        page: 'transfer-tour.php'
                    },
                    {
                        title: 'Trip Planning',
                        link: siteUrl + '/trip-planning.php',
                        page: 'trip-planning.php'
                    }
                <?php } ?>
            ],
            'files': [{
                    title: 'My Files',
                    link: siteUrl + '/files/my-files.php',
                    page: 'my-files.php'
                },
                {
                    title: 'Files',
                    link: siteUrl + '/files/files.php',
                    page: 'files.php'
                },
                {
                    title: 'Motivation Files',
                    link: siteUrl + '/files/motivation-files.php',
                    page: 'motivation-files.php'
                },
                {
                    title: 'File Year Current',
                    link: siteUrl + '/files/file-year-current.php',
                    page: 'file-year-current.php'
                },
                {
                    title: 'Abandoned Files',
                    link: siteUrl + '/files/abandoned-files.php',
                    page: 'abandoned-files.php'
                },
                {
                    title: 'Recently Modified',
                    link: siteUrl + '/files/recently-modified-files.php',
                    page: 'recently-modified-files.php'
                },
                {
                    title: 'Archived Files',
                    link: siteUrl + '/files/archived-files.php',
                    page: 'archived-files.php'
                },
                {
                    title: 'File Paid in Full By CC',
                    link: siteUrl + '/files/file-paid-in-full-list.php',
                    page: 'file-paid-in-full-list.php'
                }
            ],
            'users': [{
                    title: 'Clients',
                    link: siteUrl + '/users/clients.php',
                    page: 'clients.php'
                },
                {
                    title: 'Agents',
                    link: siteUrl + '/users/agents.php',
                    page: 'agents.php'
                },
                {
                    title: 'Agencies',
                    link: siteUrl + '/users/agencies.php',
                    page: 'agencies.php'
                },
                {
                    title: 'Consortiums',
                    link: siteUrl + '/users/users.php',
                    page: 'users.php'
                }
            ],
            'products': [{
                    title: 'Product List',
                    link: siteUrl + '/products/list.php',
                    page: 'list.php'
                },
                {
                    title: 'Add Product',
                    link: siteUrl + '/products/add.php',
                    page: 'add.php'
                },
                {
                    title: 'Categories',
                    link: siteUrl + '/products/categories.php',
                    page: 'categories.php'
                },
                {
                    title: 'Inventory',
                    link: siteUrl + '/products/inventory.php',
                    page: 'inventory.php'
                }
            ],
            'suppliers': [{
                    title: 'Supplier List',
                    link: siteUrl + '/suppliers/list.php',
                    page: 'list.php'
                },
                {
                    title: 'Add Supplier',
                    link: siteUrl + '/suppliers/add.php',
                    page: 'add.php'
                },
                {
                    title: 'Supplier Orders',
                    link: siteUrl + '/suppliers/orders.php',
                    page: 'orders.php'
                }
            ]
        };
        function isMenuItemActive(item) {
            return item && item.page && currentPage === item.page;
        }
        function getActivePrimaryMenu() {
            for (const [menuKey, items] of Object.entries(menuData)) {
                if (items.some(item => item && item.page && isMenuItemActive(item))) {
                    return menuKey;
                }
            }
            return 'dashboards'; // default
        }
        function getPageTitle() {
            // Cari di semua menu untuk mendapatkan title berdasarkan currentPage
            for (const [menuKey, items] of Object.entries(menuData)) {
                for (const item of items) {
                    if (item && item.page && item.page === currentPage) {
                        return item.title;
                    }
                }
            }
            // Jika tidak ditemukan, gunakan default berdasarkan currentPage
            const pageTitleMap = {
                'my-secure-index.php': 'Dashboard',
                'quick-contact.php': 'Quick Contact',
                'transfer-tour.php': 'Transfer & Tour',
                'trip-planning.php': 'Trip Planning',
                'my-files.php': 'My Files',
                'files.php': 'Files',
                'motivation-files.php': 'Motivation Files',
                'file-year-current.php': 'File Year Current',
                'abandoned-files.php': 'Abandoned Files',
                'recently-modified-files.php': 'Recently Modified',
                'archived-files.php': 'Archived Files',
                'file-paid-in-full-list.php': 'File Paid in Full By CC',
                'clients.php': 'Clients',
                'agents.php': 'Agents',
                'agencies.php': 'Agencies',
                'users.php': 'Consortiums',
                'list.php': 'Product List',
                'add.php': 'Add Product',
                'categories.php': 'Categories',
                'inventory.php': 'Inventory',
                'orders.php': 'Supplier Orders'
            };
            return pageTitleMap[currentPage] || 'Home';
        }
        function updatePageHeading() {
            const pageHeading = document.querySelector('.page-heading');
            if (pageHeading) {
                pageHeading.textContent = getPageTitle();
            }
        }
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
        function handlePrimaryMenuClick(event, menuKey) {
            event.preventDefault();
            document.querySelectorAll('.menu-item.here').forEach(item => {
                item.classList.remove('here', 'menu-here-bg');
            });
            const clickedMenuItem = event.target.closest('.menu-item');
            if (clickedMenuItem) {
                clickedMenuItem.classList.add('here', 'menu-here-bg');
            }
            updateSecondaryMenu(menuKey);
        }
        document.addEventListener('DOMContentLoaded', function() {
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
            const activePrimaryMenu = getActivePrimaryMenu();
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
            updateSecondaryMenu(activePrimaryMenu);
            updatePageHeading(); // Tambahkan ini untuk mengupdate page heading
        });
    </script>
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
                                    <a href="<?php echo SITE_URL . '/my-secure-index.php'; ?>">
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
            <div class="app-wrapper flex-column flex-row-fluid" id="kt_app_wrapper">
                <div class="app-main flex-column flex-row-fluid" id="kt_app_main">
                    <div class="d-flex flex-column flex-column-fluid">
                        <div id="kt_app_toolbar" class="app-toolbar pt-10 mb-0">
                            <div id="kt_app_toolbar_container" class="app-container container-fluid d-flex align-items-stretch">
                                <div class="app-toolbar-wrapper d-flex flex-stack flex-wrap gap-4 w-100">
                                    <div class="page-title d-flex flex-column justify-content-center gap-1 me-3">
                                        <h1 class="page-heading d-flex flex-column justify-content-center text-gray-900 fw-bold fs-3 m-0" id="dynamic-page-heading">Loading...</h1>
                                        <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0">
                                            <li class="breadcrumb-item text-gray-600">
                                                <a href="index.html" class="text-gray-600 text-hover-primary">Home</a>
                                            </li>
                                            <li class="breadcrumb-item">
                                                <span class="bullet bg-gray-500 w-5px h-2px"></span>
                                            </li>
                                            <li class="breadcrumb-item text-gray-600">Quick Contact</li>
                                        </ul>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 gap-lg-3">
                                        <a href="#" class="btn btn-sm btn-flex btn-transparent btn-hover-outline" data-bs-toggle="modal" data-bs-target="#kt_modal_create_campaign">Save</a>
                                        <a href="" class="btn btn-sm btn-flex btn-outline btn-active-primary bg-body" data-bs-toggle="modal" data-bs-target="#kt_modal_view_users">
                                            <i class="ki-outline ki-eye fs-4"></i>Preview</a>
                                        <a href="" class="btn btn-sm btn-flex btn-primary" data-bs-toggle="modal" data-bs-target="#kt_modal_create_campaign">
                                            <i class="ki-outline ki-exit-up fs-4"></i>Push</a>
                                    </div>
                                </div>
                            </div>
                        </div>