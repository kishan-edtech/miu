<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Max-Age: 86400');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../../includes/db-config.php';
require_once 'mailer.php'; // optional

// Escape helper
function esc($val, $conn) {
    return mysqli_real_escape_string($conn, trim($val ?? ''));
}

// File upload helper
function uploadFile($name, $dir = 'uploads/') {
    if (!isset($_FILES[$name]) || $_FILES[$name]['error'] !== 0) return null;
    if (!is_dir($dir)) mkdir($dir, 0777, true);

    $file = time() . '_' . basename($_FILES[$name]['name']);
    return move_uploaded_file($_FILES[$name]['tmp_name'], $dir.$file) ? $file : null;
}

// Multiple file upload helper
function uploadMultipleFiles($name, $dir = 'uploads/') {
    if (!isset($_FILES[$name]) || !is_array($_FILES[$name]['name'])) return null;
    if (!is_dir($dir)) mkdir($dir, 0777, true);
    
    $uploadedFiles = [];
    $fileCount = count($_FILES[$name]['name']);
    
    for ($i = 0; $i < $fileCount; $i++) {
        if ($_FILES[$name]['error'][$i] === 0) {
            $filename = time() . '_' . $i . '_' . basename($_FILES[$name]['name'][$i]);
            if (move_uploaded_file($_FILES[$name]['tmp_name'][$i], $dir.$filename)) {
                $uploadedFiles[] = $filename;
            }
        }
    }
    
    return !empty($uploadedFiles) ? json_encode($uploadedFiles) : null;
}

// ========================
// Upload Files
// ========================
$approach_road  = uploadFile('approach_road');
$front_view     = uploadFile('front_view');
$back_view      = uploadFile('back_view');
$reception_area = uploadFile('reception_area');
$domain_lab     = uploadFile('domain_lab');
$classroom      = uploadFile('classroom');
$washrooms      = uploadFile('washrooms');
$it_lab         = uploadFile('it_lab');

// ADD THESE TWO LINES - Signature and Rubber Stamp uploads
$signature      = uploadFile('signature');
$rubber_stamp   = uploadFile('rubber_stamp');

// Enclosure files
$enclosure_1_file = uploadFile('enclosure_1_file');
$enclosure_2_file = uploadFile('enclosure_2_file');
$enclosure_3_file = uploadFile('enclosure_3_file');
$enclosure_4_file = uploadFile('enclosure_4_file');
$enclosure_5_file = uploadFile('enclosure_5_file');
$enclosure_6_file = uploadFile('enclosure_6_file');
$enclosure_7_file = uploadMultipleFiles('enclosure_7_file');
$enclosure_8_file = uploadFile('enclosure_8_file');
$enclosure_9_file = uploadFile('enclosure_9_file');

// ========================
// Process Form Data
// ========================

// A. General Information About the Institute
$institution_name     = esc($_POST['institution_name'], $conn);
$postal_address       = esc($_POST['postal_address'], $conn);
$city_place           = esc($_POST['city_place'], $conn);
$block_tehsil         = esc($_POST['block_tehsil'], $conn);
$pin_code             = esc($_POST['pin_code'], $conn);
$state                = esc($_POST['state'], $conn);
$district             = esc($_POST['district'], $conn);
$mobile_no_stdcode    = esc($_POST['mobile_no_stdcode'], $conn);
$email                = esc($_POST['email'], $conn);
$principal_name       = esc($_POST['principal_name'], $conn);
$qualification_princ  = esc($_POST['qualification_principal'], $conn);
$administrative_exp   = esc($_POST['administrative_exp'], $conn);
$teaching_exp         = esc($_POST['teaching_exp'], $conn);

// B. Society / Trust / Management
$name_address             = esc($_POST['name_address'], $conn);
$registered               = esc($_POST['registered'] ?? '', $conn);
$registered_under_act     = esc($_POST['registered_under_act'], $conn);
$registration_year        = esc($_POST['registration_year'], $conn);
$registration_no          = esc($_POST['registration_no'], $conn);
$whether_non_proprietary  = esc($_POST['whether_non_proprietary'] ?? '', $conn);

$manager_name         = esc($_POST['name'], $conn);
$manager_designation  = esc($_POST['designation'], $conn);
$manager_address      = esc($_POST['address'], $conn);
$manager_phone        = esc($_POST['phone'], $conn);

// C. Infrastructural and Academic Facilities
$institution_located  = esc($_POST['institution_located'] ?? '', $conn);
$area_acres           = esc($_POST['area_acres'], $conn);
$area_sq              = esc($_POST['area_sq'], $conn);
$built_area           = esc($_POST['built_area'], $conn);

// Rooms / Labs
$classrooms_total_rooms       = esc($_POST['classrooms_total_rooms'], $conn);
$classrooms_size_sqft         = esc($_POST['classrooms_size_sqft'], $conn);
$classrooms_area_sqft         = esc($_POST['classrooms_area_sqft'], $conn);

$science_lab_total_rooms      = esc($_POST['science_lab_total_rooms'] ?? '', $conn);
$science_lab_size_sqft        = esc($_POST['science_lab_size_sqft'] ?? '', $conn);
$science_lab_area_sqft        = esc($_POST['science_lab_area_sqft'] ?? '', $conn);

$physics_lab_total_rooms      = esc($_POST['physics_lab_total_rooms'] ?? '', $conn);
$physics_lab_size_sqft        = esc($_POST['physics_lab_size_sqft'] ?? '', $conn);
$physics_lab_area_sqft        = esc($_POST['physics_lab_area_sqft'] ?? '', $conn);

$chemistry_lab_total_rooms    = esc($_POST['chemistry_lab_total_rooms'] ?? '', $conn);
$chemistry_lab_size_sqft      = esc($_POST['chemistry_lab_size_sqft'] ?? '', $conn);
$chemistry_lab_area_sqft      = esc($_POST['chemistry_lab_area_sqft'] ?? '', $conn);

$biology_lab_total_rooms      = esc($_POST['biology_lab_total_rooms'] ?? '', $conn);
$biology_lab_size_sqft        = esc($_POST['biology_lab_size_sqft'] ?? '', $conn);
$biology_lab_area_sqft        = esc($_POST['biology_lab_area_sqft'] ?? '', $conn);

$maths_lab_total_rooms        = esc($_POST['maths_lab_total_rooms'] ?? '', $conn);
$maths_lab_size_sqft          = esc($_POST['maths_lab_size_sqft'] ?? '', $conn);
$maths_lab_area_sqft          = esc($_POST['maths_lab_area_sqft'] ?? '', $conn);

$computer_lab_total_rooms     = esc($_POST['computer_lab_total_rooms'] ?? '', $conn);
$computer_lab_size_sqft       = esc($_POST['computer_lab_size_sqft'] ?? '', $conn);
$computer_lab_area_sqft       = esc($_POST['computer_lab_area_sqft'] ?? '', $conn);

$library_total_rooms          = esc($_POST['library_total_rooms'] ?? '', $conn);
$library_size_sqft            = esc($_POST['library_size_sqft'] ?? '', $conn);
$library_area_sqft            = esc($_POST['library_area_sqft'] ?? '', $conn);

$other_total_rooms            = esc($_POST['other_total_rooms'] ?? '', $conn);
$other_size_sqft              = esc($_POST['other_size_sqft'] ?? '', $conn);
$other_area_sqft              = esc($_POST['other_area_sqft'] ?? '', $conn);

$special_workshop_total_rooms = esc($_POST['special_workshop_total_rooms'] ?? '', $conn);
$special_workshop_size_sqft   = esc($_POST['special_workshop_size_sqft'] ?? '', $conn);
$special_workshop_area_sqft   = esc($_POST['special_workshop_area_sqft'] ?? '', $conn);

function intOrNull($key) {
    return isset($_POST[$key]) && $_POST[$key] !== ''
        ? (int) $_POST[$key]
        : NULL;
}


// Teaching staff
// $tgts_permanent       = esc($_POST['tgts_permanent'], $conn);
// $tgts_part_time       = esc($_POST['tgts_part_time'], $conn);
// $total_tgts           = esc($_POST['total_tgts'], $conn);

// $pgts_permanent       = esc($_POST['pgts_permanent'], $conn);
// $pgts_part_time       = esc($_POST['pgts_part_time'], $conn);
// $total_pgts           = esc($_POST['total_pgts'], $conn);

// $librarian_permanent  = esc($_POST['librarian_permanent'], $conn);
// $librarian_part_time  = esc($_POST['librarian_part_time'], $conn);
// $total_librarian      = esc($_POST['total_librarian'], $conn);

// $vice_principal_permanent  = esc($_POST['vice_principal_permanent'], $conn);
// $vice_principal_part_time  = esc($_POST['vice_principal_part_time'], $conn);
// $total_vice_principal      = esc($_POST['total_vice_principal'], $conn);

$tgts_permanent = intOrNull('tgts_permanent');
$tgts_part_time = intOrNull('tgts_part_time');
$total_tgts     = intOrNull('total_tgts');

$pgts_permanent = intOrNull('pgts_permanent');
$pgts_part_time = intOrNull('pgts_part_time');
$total_pgts     = intOrNull('total_pgts');

$librarian_permanent = intOrNull('librarian_permanent');
$librarian_part_time = intOrNull('librarian_part_time');
$total_librarian     = intOrNull('total_librarian');

$vice_principal_permanent = intOrNull('vice_principal_permanent');
$vice_principal_part_time = intOrNull('vice_principal_part_time');
$total_vice_principal     = intOrNull('total_vice_principal');


// Administrative support staff
$clerks_permanent          = esc($_POST['clerks_permanent'] ?? '', $conn);
$clerks_nt_permanent       = esc($_POST['clerks_nt_permanent'] ?? '', $conn);
$total_clerks              = esc($_POST['total_clerks'] ?? '', $conn);

$lab_attendants_permanent  = esc($_POST['lab_attendants_permanent'] ?? '', $conn);
$lab_attendants_nt_perm    = esc($_POST['lab_attendants_nt_permanent'] ?? '', $conn);
$total_lab_attendants      = esc($_POST['total_lab_attendants'] ?? '', $conn);

$accountants_permanent     = esc($_POST['accountants_permanent'] ?? '', $conn);
$accountants_nt_permanent  = esc($_POST['accountants_nt_permanent'] ?? '', $conn);
$total_accountants         = esc($_POST['total_accountants'] ?? '', $conn);

$peons_permanent           = esc($_POST['peons_permanent'] ?? '', $conn);
$peons_nt_permanent        = esc($_POST['peons_nt_permanent'] ?? '', $conn);
$total_peons               = esc($_POST['total_peons'] ?? '', $conn);

// Other facilities
$other_facilities                = esc($_POST['other_facilities'] ?? '', $conn);
$other_facilities_drinking_water = esc($_POST['other_facilities_drinking_water'] ?? '', $conn);

$library_facilities_ttl_books    = esc($_POST['library_facilities_ttl_books'] ?? '', $conn);
$library_facilities_newspapers   = esc($_POST['library_facilities_newspapers'] ?? '', $conn);
$library_facilities_magazines    = esc($_POST['library_facilities_magazines'] ?? '', $conn);

// Checkboxes
$sports_games   = isset($_POST['sports_games']) ? 'yes' : 'no';
$dance_room     = isset($_POST['dance_room']) ? 'yes' : 'no';
$gymnasium      = isset($_POST['gymnasium']) ? 'yes' : 'no';
$music_room     = isset($_POST['music_room']) ? 'yes' : 'no';
$hostel         = isset($_POST['hostel']) ? 'yes' : 'no';
$medical_checkup= isset($_POST['medical_checkup']) ? 'yes' : 'no';

// Other relevant information
$relevant_info1 = esc($_POST['relevant_info1'], $conn);
$relevant_info2 = esc($_POST['relevant_info2'], $conn);
$place          = esc($_POST['place'], $conn);
$declaration_date = esc($_POST['declaration_date'], $conn);
// ========================
// Process Staff Details as JSON
// ========================
$staff_details = [];
if (isset($_POST['staff_name']) && is_array($_POST['staff_name'])) {
    for ($i = 0; $i < count($_POST['staff_name']); $i++) {
        if (!empty($_POST['staff_name'][$i])) {
            $staff_details[] = [
                'name' => esc($_POST['staff_name'][$i], $conn),
                'qualification' => esc($_POST['staff_qualification'][$i] ?? '', $conn)
            ];
        }
    }
}

// Convert to JSON
$staff_details_json = !empty($staff_details) ? json_encode($staff_details, JSON_UNESCAPED_UNICODE) : null;

// ========================
// Process Enclosures as JSON
// ========================
$enclosures = [];

// Process each enclosure with status and file
$enclosure_fields = [
    'enclosure_1' => [
        'status' => esc($_POST['enclosure_1'] ?? '', $conn),
        'file' => $enclosure_1_file,
        'description' => 'Processing fee of Rs. 20,000/- (Twenty thousand only)',
        'submitted_date' => date('Y-m-d H:i:s')
    ],
    'enclosure_2' => [
        'status' => esc($_POST['enclosure_2'] ?? '', $conn),
        'file' => $enclosure_2_file,
        'description' => 'Copy of the Certificate of Registration of the Society/Trust/Etc.',
        'submitted_date' => date('Y-m-d H:i:s')
    ],
    'enclosure_3' => [
        'status' => esc($_POST['enclosure_3'] ?? '', $conn),
        'file' => $enclosure_3_file,
        'description' => 'Copy of the Memorandum of Association and / Trust Rules and Regulations/Bylaws Deed/Partnership Deed.',
        'submitted_date' => date('Y-m-d H:i:s')
    ],
    'enclosure_4' => [
        'status' => esc($_POST['enclosure_4'] ?? '', $conn),
        'file' => $enclosure_4_file,
        'description' => 'List of members of the Governing Body of the Society Trust/Etc with their occupations and addresses.',
        'submitted_date' => date('Y-m-d H:i:s')
    ],
    'enclosure_5' => [
        'status' => esc($_POST['enclosure_5'] ?? '', $conn),
        'file' => $enclosure_5_file,
        'description' => 'Resolution of the Management for taking R&D center and Training Partnership',
        'submitted_date' => date('Y-m-d H:i:s')
    ],
    'enclosure_6' => [
        'status' => esc($_POST['enclosure_6'] ?? '', $conn),
        'file' => $enclosure_6_file,
        'description' => 'List of teachers indicating their qualifications, designations, experience, length of service in the institution',
        'submitted_date' => date('Y-m-d H:i:s')
    ],
    'enclosure_7' => [
        'status' => esc($_POST['enclosure_7'] ?? '', $conn),
        'file' => $enclosure_7_file ? json_decode($enclosure_7_file, true) : [],
        'description' => 'Four photographs of the building of the Institution',
        'submitted_date' => date('Y-m-d H:i:s')
    ],
    'enclosure_8' => [
        'status' => esc($_POST['enclosure_8'] ?? '', $conn),
        'file' => $enclosure_8_file,
        'description' => 'Documents of Land (Lease/Ownership)',
        'submitted_date' => date('Y-m-d H:i:s')
    ],
    'enclosure_9' => [
        'status' => esc($_POST['enclosure_9'] ?? '', $conn),
        'file' => $enclosure_9_file,
        'description' => 'Layout plan of the building of the Institute',
        'submitted_date' => date('Y-m-d H:i:s')
    ]
];

// Add each enclosure to the JSON array
foreach ($enclosure_fields as $key => $data) {
    $enclosures[$key] = $data;
}

// Convert to JSON
$enclosures_json = !empty($enclosures) ? json_encode($enclosures, JSON_UNESCAPED_UNICODE) : null;

// ========================
// Build INSERT Query with JSON fields
// ========================
$sql = "INSERT INTO center_verfiy1 (
    form_type,
    institution_name, postal_address, city_place, block_tehsil, dir_pincode, dir_state, dir_district,
    dir_mob_number, dir_email, dir_name, qualification_principal, administrative_exp, teaching_exp,
    
    name_address, registered, registered_under_act, registration_year, registration_no, whether_non_proprietary,
    cmgr_name, manager_designation, cmgr_address, cmgr_mob_number,
    
    institution_located, area_acres, area_sq, built_area,
    
    classrooms_total_rooms, classrooms_size_sqft, classrooms_area_sqft,
    science_lab_total_rooms, science_lab_size_sqft, science_lab_area_sqft,
    physics_lab_total_rooms, physics_lab_size_sqft, physics_lab_area_sqft,
    chemistry_lab_total_rooms, chemistry_lab_size_sqft, chemistry_lab_area_sqft,
    biology_lab_total_rooms, biology_lab_size_sqft, biology_lab_area_sqft,
    maths_lab_total_rooms, maths_lab_size_sqft, maths_lab_area_sqft,
    computer_lab_total_rooms, computer_lab_size_sqft, computer_lab_area_sqft,
    library_total_rooms, library_size_sqft, library_area_sqft,
    other_total_rooms, other_size_sqft, other_area_sqft,
    special_workshop_total_rooms, special_workshop_size_sqft, special_workshop_area_sqft,
    
  
    
    
    other_facilities, other_facilities_drinking_water,
    library_facilities_ttl_books, library_facilities_newspapers, library_facilities_magazines,
    sports_games, dance_room, gymnasium, music_room, hostel, medical_checkup,
    
    -- NEW: JSON data fields
    staff_details_json,
    enclosures_json,
    
    -- File uploads
    approach_road, front_view, back_view, reception_area,
    domain_lab, classroom, washrooms, it_lab,
    -- ADD THESE TWO FIELDS: Signature and Rubber Stamp
    signature, rubber_stamp,
    
    relevant_info1, relevant_info2, place, declaration_date
) VALUES (
    'vocational',
    '$institution_name', '$postal_address', '$city_place', '$block_tehsil', '$pin_code', '$state', '$district',
    '$mobile_no_stdcode', '$email', '$principal_name', '$qualification_princ', '$administrative_exp', '$teaching_exp',
    
    '$name_address', '$registered', '$registered_under_act', '$registration_year', '$registration_no', '$whether_non_proprietary',
    '$manager_name', '$manager_designation', '$manager_address', '$manager_phone',
    
    '$institution_located', '$area_acres', '$area_sq', '$built_area',
    
    '$classrooms_total_rooms', '$classrooms_size_sqft', '$classrooms_area_sqft',
    '$science_lab_total_rooms', '$science_lab_size_sqft', '$science_lab_area_sqft',
    '$physics_lab_total_rooms', '$physics_lab_size_sqft', '$physics_lab_area_sqft',
    '$chemistry_lab_total_rooms', '$chemistry_lab_size_sqft', '$chemistry_lab_area_sqft',
    '$biology_lab_total_rooms', '$biology_lab_size_sqft', '$biology_lab_area_sqft',
    '$maths_lab_total_rooms', '$maths_lab_size_sqft', '$maths_lab_area_sqft',
    '$computer_lab_total_rooms', '$computer_lab_size_sqft', '$computer_lab_area_sqft',
    '$library_total_rooms', '$library_size_sqft', '$library_area_sqft',
    '$other_total_rooms', '$other_size_sqft', '$other_area_sqft',
    '$special_workshop_total_rooms', '$special_workshop_size_sqft', '$special_workshop_area_sqft',
   
   
    
    '$other_facilities', '$other_facilities_drinking_water',
    '$library_facilities_ttl_books', '$library_facilities_newspapers', '$library_facilities_magazines',
    '$sports_games', '$dance_room', '$gymnasium', '$music_room', '$hostel', '$medical_checkup',
    
    -- NEW: JSON data
    " . ($staff_details_json ? "'$staff_details_json'" : "NULL") . ",
    " . ($enclosures_json ? "'$enclosures_json'" : "NULL") . ",
    
    -- File uploads
    '$approach_road', '$front_view', '$back_view', '$reception_area',
    '$domain_lab', '$classroom', '$washrooms', '$it_lab',
    -- ADD THESE TWO 🔥
'$signature', '$rubber_stamp',
    
    '$relevant_info1', '$relevant_info2', '$place', '$declaration_date'
)";

// ========================
// Execute Query & Response
// ========================
if (mysqli_query($conn, $sql)) {
    $voc_form_id = mysqli_insert_id($conn);

    echo json_encode([
        'status'      => 200,
        'voc_form_id' => $voc_form_id,
        'message'     => 'Information saved successfully',
        'staff_count' => count($staff_details),
        'enclosures_count' => count($enclosures),
        'data_stored' => [
            'staff_details' => 'JSON format',
            'enclosures' => 'JSON format'
        ]
    ]);
} else {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Database error: ' . mysqli_error($conn)
    ]);
}

mysqli_close($conn);
?>