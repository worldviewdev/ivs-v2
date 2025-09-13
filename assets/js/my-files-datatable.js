$(document).ready(function() {
    // Cek apakah DataTable sudah diinisialisasi
    if ($.fn.DataTable.isDataTable('#kt_datatable_vertical_scroll')) {
        $('#kt_datatable_vertical_scroll').DataTable().destroy();
    }
    
    // Inisialisasi DataTable
    var table = $('#kt_datatable_vertical_scroll').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '../api/my-files.php',
            type: 'GET',
            data: function(d) {
                // Tambahkan filter tambahan jika diperlukan
                d.status_filter = $('[data-kt-ecommerce-order-filter="status"]').val();
                d.date_filter = $('#kt_ecommerce_sales_flatpickr').val();
            },
            error: function(xhr, error, thrown) {
                console.error('DataTable AJAX Error:', error);
                console.error('Response:', xhr.responseText);
            }
        },
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
        ],
        createdRow: function(row, data, dataIndex) {
            // Terapkan styling berdasarkan status (sama dengan yang ada di my-files.php)
            $(row).addClass(data.row_class);
            $(row).css('background-color', data.row_bg_color + ' !important');
            $(row).find('td').css('background-color', data.row_bg_color + ' !important');
        },
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        order: [[0, 'desc']],
        language: {
            processing: "Memproses...",
            lengthMenu: "_MENU_",
            zeroRecords: "Tidak ada data yang ditemukan",
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
        }
    });

    // Search functionality
    $('[data-kt-ecommerce-order-filter="search"]').on('keyup', function() {
        table.search(this.value).draw();
    });

    // Status filter
    $('[data-kt-ecommerce-order-filter="status"]').on('change', function() {
        table.draw();
    });

    // Date filter
    $('#kt_ecommerce_sales_flatpickr').on('change', function() {
        table.draw();
    });
});