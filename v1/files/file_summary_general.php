<?php
require_once('../includes/midas.inc.php');

$id = isset($_GET['id']) ? $_GET['id'] : '';
$result = db_query("SELECT * FROM mv_files WHERE file_id = '$id' AND file_status != 'Delete'");
$file = mysqli_fetch_assoc($result);

if (!$file) {
    echo "File not found";
    exit;
}

require_once('../header.inc.php');
?>

<div id="kt_app_content" class="app-content flex-column-fluid">
    <div id="kt_app_content_container" class="app-container container-fluid">
        <!-- File Header -->
        <div class="card mb-6">
            <div class="card-header border-0 pt-6">
                <div class="card-title">
                    <div class="d-flex align-items-center">
                        <div class="symbol symbol-50px me-4">
                            <div class="symbol-label bg-primary bg-opacity-10">
                                <i class="ki-outline ki-folder fs-2x text-primary"></i>
                            </div>
                        </div>
                        <div>
                            <h2 class="fw-bold text-gray-800 mb-1"><?php echo htmlspecialchars($file['file_code']); ?></h2>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge badge-danger fs-7 fw-bold">Live</span>
                                <span class="badge badge-primary fs-7 fw-bold">Archive File</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-toolbar">
                    <div class="d-flex gap-2">
                        <a href="#" class="btn btn-sm btn-light-primary">Save File As Template</a>
                        <a href="#" class="btn btn-sm btn-light-info">Duplicate File</a>
                        <a href="#" class="btn btn-sm btn-light-warning">Convert To Agent File</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="row g-6">
            <!-- Left Panel - Details -->
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header border-0 pt-6">
                        <div class="card-title">
                            <h4 class="fw-bold text-gray-800">Details</h4>
                        </div>
                        <div class="card-toolbar">
                            <button class="btn btn-sm btn-primary">Edit Details</button>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <!-- File Identification -->
                        <div class="mb-6">
                            <h6 class="fw-bold text-gray-800 mb-4">File Identification</h6>
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="d-flex flex-column">
                                        <span class="text-muted fs-7 fw-semibold mb-1">File #</span>
                                        <span class="fs-6 fw-bold text-gray-800"><?php echo htmlspecialchars($file['file_code']); ?></span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex flex-column">
                                        <span class="text-muted fs-7 fw-semibold mb-1">FATTURA #</span>
                                        <span class="fs-6 fw-bold text-gray-800"><?php echo htmlspecialchars($file['fattura_number'] ?? ''); ?></span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex flex-column">
                                        <span class="text-muted fs-7 fw-semibold mb-1">Status</span>
                                        <span class="fs-6 fw-bold text-gray-800"><?php echo htmlspecialchars($file['file_current_status'] ?? 'Need to Follow Up'); ?></span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex flex-column">
                                        <span class="text-muted fs-7 fw-semibold mb-1">File Type</span>
                                        <span class="fs-6 fw-bold text-gray-800"><?php echo htmlspecialchars($file['file_type'] ?? 'Transfers'); ?></span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex flex-column">
                                        <span class="text-muted fs-7 fw-semibold mb-1">File Type Description</span>
                                        <span class="fs-6 fw-bold text-gray-800"><?php echo htmlspecialchars($file['file_type_desc'] ?? ''); ?></span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex flex-column">
                                        <span class="text-muted fs-7 fw-semibold mb-1">Display File</span>
                                        <span class="fs-6 fw-bold text-gray-800"><?php echo ($file['display_file'] ?? 0) ? 'Yes' : 'No'; ?></span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex flex-column">
                                        <span class="text-muted fs-7 fw-semibold mb-1">Destination</span>
                                        <span class="fs-6 fw-bold text-gray-800"><?php echo htmlspecialchars($file['destination'] ?? 'Italy'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Client Information -->
                        <div class="mb-6">
                            <h6 class="fw-bold text-gray-800 mb-4">Client Information</h6>
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="d-flex flex-column">
                                        <span class="text-muted fs-7 fw-semibold mb-1">Client Name</span>
                                        <div class="d-flex align-items-center">
                                            <span class="fs-6 fw-bold text-gray-800 me-2"><?php echo htmlspecialchars($file['client_name'] ?? 'Jill LaMadeleine'); ?></span>
                                            <a href="#" class="btn btn-sm btn-light-primary">Change Client</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex flex-column">
                                        <span class="text-muted fs-7 fw-semibold mb-1">Client Email</span>
                                        <span class="fs-6 fw-bold text-gray-800"><?php echo htmlspecialchars($file['client_email'] ?? 'jilamadeleine5@gmail.com'); ?></span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex flex-column">
                                        <span class="text-muted fs-7 fw-semibold mb-1">Client Phone</span>
                                        <span class="fs-6 fw-bold text-gray-800"><?php echo htmlspecialchars($file['client_phone'] ?? '203-768-1342'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Administrative Details -->
                        <div class="mb-6">
                            <h6 class="fw-bold text-gray-800 mb-4">Administrative Details</h6>
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="d-flex flex-column">
                                        <span class="text-muted fs-7 fw-semibold mb-1">Added By</span>
                                        <span class="fs-6 fw-bold text-gray-800"><?php echo htmlspecialchars($file['added_by'] ?? 'Jessie Andrews'); ?></span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex flex-column">
                                        <span class="text-muted fs-7 fw-semibold mb-1">Date Added</span>
                                        <span class="fs-6 fw-bold text-gray-800"><?php echo date('d F Y h:i A', strtotime($file['date_added'] ?? '2025-09-16 02:04:00')); ?></span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex flex-column">
                                        <span class="text-muted fs-7 fw-semibold mb-1">Date Modified</span>
                                        <span class="fs-6 fw-bold text-gray-800"><?php echo date('d F Y h:i A', strtotime($file['date_modified'] ?? '2025-09-16 02:04:00')); ?></span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex flex-column">
                                        <span class="text-muted fs-7 fw-semibold mb-1">Received by</span>
                                        <span class="fs-6 fw-bold text-gray-800"><?php echo htmlspecialchars($file['received_by'] ?? 'Email'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Staff & Request -->
                        <div class="mb-6">
                            <h6 class="fw-bold text-gray-800 mb-4">Staff & Request</h6>
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="d-flex flex-column">
                                        <span class="text-muted fs-7 fw-semibold mb-1">Primary Staff</span>
                                        <span class="fs-6 fw-bold text-gray-800"><?php echo htmlspecialchars($file['primary_staff'] ?? 'Adam Beaven'); ?></span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex flex-column">
                                        <span class="text-muted fs-7 fw-semibold mb-1">Active Staff</span>
                                        <span class="fs-6 fw-bold text-gray-800"><?php echo htmlspecialchars($file['active_staff'] ?? 'Adam Beaven'); ?></span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex flex-column">
                                        <span class="text-muted fs-7 fw-semibold mb-1">Request</span>
                                        <span class="fs-6 fw-bold text-gray-800"><?php echo htmlspecialchars($file['request'] ?? 'Full Itinerary'); ?></span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex flex-column">
                                        <span class="text-muted fs-7 fw-semibold mb-1">Selling Currency</span>
                                        <span class="fs-6 fw-bold text-gray-800"><?php echo htmlspecialchars($file['selling_currency'] ?? 'EUR'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Travel Dates -->
                        <div class="mb-6">
                            <h6 class="fw-bold text-gray-800 mb-4">Travel Dates</h6>
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="d-flex flex-column">
                                        <span class="text-muted fs-7 fw-semibold mb-1">Departure</span>
                                        <span class="fs-6 fw-bold text-gray-800"><?php echo date('d F Y', strtotime($file['file_departure_date'] ?? '2025-10-01')); ?></span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex flex-column">
                                        <span class="text-muted fs-7 fw-semibold mb-1">Departure Location</span>
                                        <span class="fs-6 fw-bold text-gray-800"><?php echo htmlspecialchars($file['departure_location'] ?? ''); ?></span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex flex-column">
                                        <span class="text-muted fs-7 fw-semibold mb-1">Arrival</span>
                                        <span class="fs-6 fw-bold text-gray-800"><?php echo date('d F Y', strtotime($file['file_arrival_date'] ?? '2025-10-01')); ?></span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex flex-column">
                                        <span class="text-muted fs-7 fw-semibold mb-1">Return</span>
                                        <span class="fs-6 fw-bold text-gray-800"><?php echo date('d F Y', strtotime($file['file_return_date'] ?? '2025-09-16')); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pax Details -->
                        <div class="mb-6">
                            <h6 class="fw-bold text-gray-800 mb-4">Pax Details</h6>
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="d-flex flex-column">
                                        <span class="text-muted fs-7 fw-semibold mb-1">Pax</span>
                                        <div class="d-flex gap-4">
                                            <span class="fs-6 fw-bold text-gray-800">Adults - <?php echo $file['adults'] ?? 6; ?></span>
                                            <span class="fs-6 fw-bold text-gray-800">Infants (0-2) - <?php echo $file['infants'] ?? 0; ?></span>
                                            <span class="fs-6 fw-bold text-gray-800">Children (2-12) - <?php echo $file['children'] ?? 0; ?></span>
                                            <span class="fs-6 fw-bold text-gray-800">Teens (12-18) - <?php echo $file['teens'] ?? 0; ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex flex-column">
                                        <span class="text-muted fs-7 fw-semibold mb-1">Lead Pax</span>
                                        <span class="fs-6 fw-bold text-gray-800"><?php echo htmlspecialchars($file['lead_pax'] ?? 'Jill LaMadeleine'); ?></span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex flex-column">
                                        <span class="text-muted fs-7 fw-semibold mb-1">Additional Guests</span>
                                        <span class="fs-6 fw-bold text-gray-800"><?php echo htmlspecialchars($file['additional_guests'] ?? ''); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Comments -->
                        <div class="mb-0">
                            <h6 class="fw-bold text-gray-800 mb-4">Comments</h6>
                            <div class="bg-light-primary p-4 rounded">
                                <textarea class="form-control form-control-solid" rows="6" placeholder="Enter comments..."><?php echo htmlspecialchars($file['comments'] ?? 'How far are you from Chiusure? We will be staying there 9/28 - 10/2. Do you have availability that week? We are looking for day trips ideally with a private driver to pick us up at the villa and bring us to the activity but we do have a rental car.

Good Morning, We will be in Chiusure from 9/28 - 10/2. We are looking to do some day trips to Siena, etc and are looking for a private driver to and from. Is this a service that you provide and can you please let me know what the pricing is for round trip to Siena for 6 adults would be? Thank you. Jill LaMadeleine'); ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Middle Panel - File Actions -->
            <div class="col-lg-4">

                <!-- File Includes -->
                <div class="card">
                    <div class="card-header border-0 pt-6">
                        <div class="card-title">
                            <h4 class="fw-bold text-gray-800">File Includes</h4>
                        </div>
                        <div class="card-toolbar">
                            <button class="btn btn-sm btn-primary">Edit</button>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="text-center py-8">
                            <div class="text-muted fs-6">No file includes added yet</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Panel - Contracts & Attachments -->
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header border-0 pt-6">
                        <div class="card-title">
                            <h4 class="fw-bold text-gray-800">Contracts & Attachments</h4>
                        </div>
                        <div class="card-toolbar">
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-primary">Add File</button>
                                <button class="btn btn-sm btn-light-primary">Add Link</button>
                                <button class="btn btn-sm btn-light">Edit</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="text-center py-8">
                            <div class="text-muted fs-6">File Does Not Include</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
require_once('../footer.inc.php');
?>
