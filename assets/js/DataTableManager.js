/**
 * Reusable DataTable Manager
 * Manages DataTable initialization and configuration dynamically
 */
class DataTableManager {
    constructor(config) {
        this.config = this.mergeConfig(config);
        this.table = null;
        this.init();
    }

    /**
     * Merge default configuration with custom configuration
     */
    mergeConfig(customConfig) {
        const defaultConfig = {
            tableId: '#kt_datatable_vertical_scroll',
            ajax: {
                url: '',
                type: 'GET',
                data: function(d) {
                    return d;
                },
                error: function(xhr, error, thrown) {
                    console.error('DataTable AJAX Error:', error);
                    console.error('Response:', xhr.responseText);
                }
            },
            columns: [],
            processing: true,
            serverSide: true,
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            order: [[0, 'desc']],
            language: {
                processing: "Processing...",
                lengthMenu: "_MENU_",
                zeroRecords: "No data found",
                info: "_START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "Showing 0 to 0 of 0 entries",
                infoFiltered: "(filtered from _MAX_ total entries)",
                search: "Search:",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: ">",
                    previous: "<"
                }
            },
            filters: {
                search: '[data-kt-ecommerce-order-filter="search"]',
                status: '[data-kt-ecommerce-order-filter="status"]',
                date: '#kt_ecommerce_sales_flatpickr'
            },
            rowStyling: {
                enabled: true,
                classField: 'row_class',
                bgColorField: 'row_bg_color'
            }
        };

        return this.deepMerge(defaultConfig, customConfig);
    }

    /**
     * Deep merge objects
     */
    deepMerge(target, source) {
        const result = { ...target };
        
        for (const key in source) {
            if (source[key] && typeof source[key] === 'object' && !Array.isArray(source[key])) {
                result[key] = this.deepMerge(target[key] || {}, source[key]);
            } else {
                result[key] = source[key];
            }
        }
        
        return result;
    }

    /**
     * Initialize DataTable
     */
    init() {
        // Destroy existing table if exists
        if ($.fn.DataTable.isDataTable(this.config.tableId)) {
            $(this.config.tableId).DataTable().destroy();
        }

        // Prepare AJAX data function
        const ajaxConfig = {
            ...this.config.ajax,
            data: (d) => {
                const baseData = this.config.ajax.data(d);
                
        // Add filter data
        if (this.config.filters.status) {
            baseData.status_filter = $(this.config.filters.status).val();
        }
        if (this.config.filters.date) {
            const dateValue = $(this.config.filters.date).val();
            // Handle date range - if it contains " to ", split it
            if (dateValue && dateValue.includes(' to ')) {
                const dates = dateValue.split(' to ');
                baseData.date_from = dates[0];
                baseData.date_to = dates[1];
            } else {
                baseData.date_filter = dateValue;
            }
        }
                
                return baseData;
            }
        };

        // Prepare table configuration
        const tableConfig = {
            processing: this.config.processing,
            serverSide: this.config.serverSide,
            ajax: ajaxConfig,
            columns: this.config.columns,
            pageLength: this.config.pageLength,
            lengthMenu: this.config.lengthMenu,
            order: this.config.order,
            language: this.config.language
        };

        // Add row styling if enabled
        if (this.config.rowStyling.enabled) {
            tableConfig.createdRow = (row, data, dataIndex) => {
                if (data[this.config.rowStyling.classField]) {
                    $(row).addClass(data[this.config.rowStyling.classField]);
                }
                if (data[this.config.rowStyling.bgColorField]) {
                    $(row).css('background-color', data[this.config.rowStyling.bgColorField] + ' !important');
                    $(row).find('td').css('background-color', data[this.config.rowStyling.bgColorField] + ' !important');
                }
            };
        }

        // Initialize DataTable
        this.table = $(this.config.tableId).DataTable(tableConfig);

        // Bind filter events
        this.bindFilters();
    }

    /**
     * Bind filter events
     */
    bindFilters() {
        // Search filter
        if (this.config.filters.search) {
            const searchElement = $(this.config.filters.search);
            searchElement.on('keyup', (e) => {
                this.table.search(e.target.value).draw();
            });
        }

        // Status filter
        if (this.config.filters.status) {
            const statusElement = $(this.config.filters.status);
            statusElement.on('change', () => {
                this.table.draw();
            });
        }

        // Date filter
        if (this.config.filters.date) {
            const dateElement = $(this.config.filters.date);
            dateElement.on('change', () => {
                this.table.draw();
            });
        }
    }

    /**
     * Get DataTable instance
     */
    getTable() {
        return this.table;
    }

    /**
     * Refresh table data
     */
    refresh() {
        if (this.table) {
            this.table.ajax.reload();
        }
    }

    /**
     * Destroy table
     */
    destroy() {
        if (this.table) {
            this.table.destroy();
            this.table = null;
        }
    }
}

/**
 * Predefined configurations for various DataTable types
 */
const DataTableConfigs = {
    // File DataTable configuration
    files: {
        columns: [
            { data: 'file_code' },
            { data: 'file_arrival_date' },
            { data: 'client_name' },
            { data: 'agent_name' },
            { data: 'active_staff_name' },
            { 
                data: 'status',
                render: function(data, type, row) {
                    return '<span class="status-indicator"></span><span class="status-badge">' + data.text + '</span>';
                }
            },
            { data: 'file_type' },
            { data: 'file_type_desc' }
        ]
    },

    // Sales Paid DataTable configuration - same structure as files
    sales_paid: {
        columns: [
            { data: 'file_code' },
            { data: 'file_departure_date' },
            { data: 'client_name' },
            { data: 'agent_name' },
            { data: 'active_staff_name' },
            { 
                data: 'status',
                render: function(data, type, row) {
                    return '<span class="status-indicator"></span><span class="status-badge">' + data.text + '</span>';
                }
            },
            { data: 'file_type_text' },
            { data: 'file_type_desc' }
        ]
    },

    // User DataTable configuration (contoh untuk future use)
    users: {
        columns: [
            { data: 'username' },
            { data: 'email' },
            { data: 'role' },
            { data: 'created_at' },
            { 
                data: 'status',
                render: function(data, type, row) {
                    return '<span class="badge badge-' + data.class + '">' + data.text + '</span>';
                }
            }
        ]
    }
};

/**
 * Factory function to create DataTable easily
 */
function createDataTable(type, customConfig = {}) {
    const baseConfig = DataTableConfigs[type] || {};
    const config = { ...baseConfig, ...customConfig };
    
    return new DataTableManager(config);
}
