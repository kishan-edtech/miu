<?php

trait ExportTrait
{
    protected $db;
    protected $uniId;
    
    public function setDBConnection($pdo)
    {
        $this->db = $pdo;
    }
    
    public function setUniversityId($universityId)
    {
        $this->uniId = $universityId;
    }
    
    public function studentExport($uniId, $payload, $filters = null)
    {
        try {
            // Set university ID
            $this->uniId = $uniId;
            
            // Build header array
            $header = $this->buildHeader();
            
            // Build search query
            $searchValue = $filters['search']['value'] ?? '';
            $searchQuery = $this->buildSearchQuery($searchValue);
            
            // Build main query
            $query = $this->buildMainQuery($searchQuery);
            
            // Prepare and execute main query
            $stmt = $this->db->prepare($query);
            
            // Bind parameters
            $params = [':university_id' => $this->uniId];
            
            // Add search parameters if needed
            if (!empty($searchValue)) {
                if (strpos($searchValue, "=") !== false) {
                    $params = $this->bindSearchParams($searchValue, $params);
                } elseif (strcasecmp($searchValue, 'completed') != 0) {
                    $params[':search'] = '%' . $searchValue . '%';
                }
            }
            
            $stmt->execute($params);
            $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $data = [];
            $data[] = $header;
            
            foreach ($students as $student) {
                $row = $this->processStudentRow($student);
                // Verify row count matches header count
                if (count($row) !== count($header)) {
                    error_log("Row count mismatch: Header=" . count($header) . ", Row=" . count($row));
                    // Pad or trim row to match header count
                    $row = $this->fixRowLength($row, count($header));
                }
                $data[] = $row;
            }
            
            return $data;
            
        } catch (PDOException $e) {
            error_log("Export Error: " . $e->getMessage());
            throw new Exception("Database error during export: " . $e->getMessage());
        }
    }
    
    /**
     * Fix row length to match header count
     */
    private function fixRowLength(array $row, int $targetLength): array
    {
        $currentLength = count($row);
        
        if ($currentLength < $targetLength) {
            // Pad with empty values
            return array_pad($row, $targetLength, '');
        } elseif ($currentLength > $targetLength) {
            // Trim excess values
            return array_slice($row, 0, $targetLength);
        }
        
        return $row;
    }
    
    /**
     * Build the header array
     */
    private function buildHeader(): array
    {
        $header = [
            'Student_ID', 'Enrollment_No', 'Roll_Number', 'Step', 'Added On', 
            'Processed By Center', 'Processed To University', 'Student Name', 
            'Father Name', 'Mother Name', 'Adm Type', 'Session', 'Duration', 
            'Mode', 'Course', 'Sub Course', 'Short Name', 'Email', 'Contact', 
            'Alternate Email', 'Alternate Contact', 'Aadhar Number', 'DOB', 
            'Employement Status', 'Gender', 'Category', 'Address', 'City', 
            'District', 'State', 'Pincode', 'Nationality', 'High School', 
            '10th Subject', '10th Year', '10th Board/Institute', '10th Marks Obtained', 
            '10th Maximum Marks', '10th Total Marks', 'Intermediate', '12th Subject', '12th Year', 
            '12th Board/Institute', '12th Marks Obtained', '12th Maximum Marks', '12th Total Marks', 
            'UG', 'ug Subject', 'ug Year', 'ug Board/Institute', 'ug Marks Obtained', 
            'ug Maximum Marks', 'ug Total Marks', 'PG', 'pg Subject', 'pg Year', 
            'pg Board/Institute', 'pg Marks Obtained', 'pg Maximum Marks', 'pg Total Marks', 
            'Other', 'other Subject', 'other Year', 'other Board/Institute', 'other Marks Obtained', 
            'other Maximum Marks', 'other Total Marks', 'Code', 'Center Name', 'RM', 
            'Export Documents'
        ];
        
        // Add fee structures to header
        $header = $this->addFeeStructuresToHeader($header);
        
        // Add semester headers for university ID 20
        if ($this->uniId == 20 || $this->uniId == 21) {
            $header = $this->addSemesterHeaders($header);
        }
        
        // Add total fee received for university ID 41
        if ($this->uniId == 41) {
            $header[] = 'Total Fee Received';
        }
        
        // Add course duration/name and transfer ID
        $header[] = ($this->uniId == 20 || $this->uniId == 21) ? 'Course Name' : 'Course Duration';
        $header[] = 'Transfer ID';
        
        return $header;
    }
    
    /**
     * Add fee structures to header
     */
    private function addFeeStructuresToHeader(array $header): array
    {
        $stmt = $this->db->prepare(
            "SELECT ID, Name, Sharing FROM Fee_Structures 
             WHERE University_ID = ? ORDER BY Fee_Applicable_ID"
        );
        $stmt->execute([$this->uniId]);
        $feeStructures = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($feeStructures as $feeStructure) {
            $header[] = $feeStructure['Name'];
            $header[] = $feeStructure['Name'] . " %";
        }
        
        $header[] = "Total";
        
        return $header;
    }
    
    /**
     * Add semester headers for university ID 20
     */
    private function addSemesterHeaders(array $header): array
    {
        $semester = $this->getMaxSemester();
        
        for ($i = 1; $i <= $semester; $i++) {
            $header[] = 'Semester-' . $i;
        }
        
        $header[] = "Total (Paid Fee)";
        
        return $header;
    }
    
    /**
     * Build search query based on search value
     */
    private function buildSearchQuery(string $searchValue): string
    {
        if (empty($searchValue)) {
            return " ";
        }
        
        if (strpos($searchValue, "=") !== false) {
            return $this->buildAdvancedSearchQuery($searchValue);
        }
        
        if (strcasecmp($searchValue, 'completed') == 0) {
            return " AND Step = 4 ";
        }
        
        return " AND (Students.ID LIKE :search OR Students.Unique_ID LIKE :search 
                 OR Students.First_Name LIKE :search OR Students.Middle_Name LIKE :search 
                 OR Students.Last_Name LIKE :search OR Admission_Sessions.Name LIKE :search 
                 OR Admission_Types.Name LIKE :search OR Students.Step LIKE :search 
                 OR Students.Father_Name LIKE :search OR Students.Email LIKE :search 
                 OR Students.Contact LIKE :search OR Sub_Courses.Short_Name LIKE :search)";
    }
    
    /**
     * Build advanced search query for specific columns
     */
    private function buildAdvancedSearchQuery(string $searchValue): string
    {
        $search = explode("=", $searchValue);
        $searchBy = trim($search[0]);
        $values = isset($search[1]) && !empty($search[1]) 
            ? array_filter(explode(" ", $search[1])) 
            : [];
        
        if (empty($values)) {
            return " ";
        }
        
        // Note: student_id logic might need to be passed or configured
        $studentIdColumn = 'Students.ID'; // Default to ID, can be configured elsewhere
        
        $column = match (strtolower($searchBy)) {
            'student id' => $studentIdColumn,
            'enrollment' => 'Students.Enrollment_No',
            'oa number' => 'OA_Number',
            default => ''
        };
        
        if (empty($column)) {
            return " ";
        }
        
        // Create placeholders for IN clause
        $placeholders = implode(',', array_fill(0, count($values), '?'));
        return " AND $column IN ($placeholders)";
    }
    
    /**
     * Bind search parameters for advanced search
     */
    private function bindSearchParams(string $searchValue, array $params): array
    {
        $search = explode("=", $searchValue);
        $values = isset($search[1]) && !empty($search[1]) 
            ? array_filter(explode(" ", $search[1])) 
            : [];
        
        foreach ($values as $index => $value) {
            $params[":search_value_$index"] = $value;
        }
        
        return $params;
    }
    
    /**
     * Build main query
     */
    private function buildMainQuery(string $searchQuery): string
    {
        return "SELECT 
            Students.ID, 
            Students.Unique_ID, 
            Students.Enrollment_No, 
            Students.OA_Number, 
            Students.Step, 
            Students.Created_At, 
            Students.Process_By_Center, 
            Students.Processed_To_University, 
            CONCAT(
                Students.First_Name, 
                IF(Students.Middle_Name != '', CONCAT(' ', Students.Middle_Name), ''), 
                ' ', 
                Students.Last_Name
            ) as Name, 
            Students.Father_Name, 
            Students.Mother_Name, 
            Admission_Types.Name as Adm_Type, 
            Admission_Sessions.Name as Session, 
            Students.Duration, 
            Modes.Name as Mode, 
            Courses.Name as Course, 
            Sub_Courses.Name AS Sub_Course, 
            Sub_Courses.Short_Name as Short_Name, 
            Students.Email, 
            Students.Contact, 
            Students.Alternate_Email, 
            Students.Alternate_Contact, 
            Students.Aadhar_Number, 
            Students.DOB, 
            Students.Employement_Status, 
            Students.Gender, 
            Students.Category, 
            REPLACE(JSON_EXTRACT(Students.Address, '$.present_address'), '\"', '') as Address, 
            REPLACE(JSON_EXTRACT(Students.Address, '$.present_city'), '\"', '') as City, 
            REPLACE(JSON_EXTRACT(Students.Address, '$.present_district'), '\"', '') as District, 
            REPLACE(JSON_EXTRACT(Students.Address, '$.present_state'), '\"', '') as State, 
            REPLACE(JSON_EXTRACT(Students.Address, '$.present_pincode'), '\"', '') as Pincode, 
            Students.Nationality, 
            Students.Added_For,
            Students.Is_Transferred 
        FROM Students 
        LEFT JOIN Admission_Types ON Students.Admission_Type_ID = Admission_Types.ID 
        LEFT JOIN Admission_Sessions ON Students.Admission_Session_ID = Admission_Sessions.ID 
        LEFT JOIN Modes ON Students.Mode_ID = Modes.ID 
        LEFT JOIN Courses ON Students.Course_ID = Courses.ID 
        LEFT JOIN Sub_Courses ON Students.Sub_Course_ID = Sub_Courses.ID 
        WHERE Students.University_ID = :university_id 
        $searchQuery 
        ORDER BY ID DESC";
    }
    
    /**
     * Process individual student row
     */
    private function processStudentRow(array $student): array
    {
        // Initialize row with student data
        $row = [];
        
        // Add student basic data in correct order
        $row[] = $student['Unique_ID'] ?? '';
        $row[] = $student['Enrollment_No'] ?? '';
        $row[] = $student['OA_Number'] ?? '';
        $row[] = $student['Step'] ?? '';
        $row[] = $student['Created_At'] ?? '';
        $row[] = $student['Process_By_Center'] ?? '';
        $row[] = $student['Processed_To_University'] ?? '';
        $row[] = $student['Name'] ?? '';
        $row[] = $student['Father_Name'] ?? '';
        $row[] = $student['Mother_Name'] ?? '';
        $row[] = $student['Adm_Type'] ?? '';
        $row[] = $student['Session'] ?? '';
        $row[] = $student['Duration'] ?? '';
        $row[] = $student['Mode'] ?? '';
        $row[] = $student['Course'] ?? '';
        $row[] = $student['Sub_Course'] ?? '';
        $row[] = $student['Short_Name'] ?? '';
        $row[] = $student['Email'] ?? '';
        $row[] = $student['Contact'] ?? '';
        $row[] = $student['Alternate_Email'] ?? '';
        $row[] = $student['Alternate_Contact'] ?? '';
        $row[] = $student['Aadhar_Number'] ?? '';
        $row[] = $student['DOB'] ?? '';
        $row[] = $student['Employement_Status'] ?? '';
        $row[] = $student['Gender'] ?? '';
        $row[] = $student['Category'] ?? '';
        $row[] = $student['Address'] ?? '';
        $row[] = $student['City'] ?? '';
        $row[] = $student['District'] ?? '';
        $row[] = $student['State'] ?? '';
        $row[] = $student['Pincode'] ?? '';
        $row[] = $student['Nationality'] ?? '';
        
        // Get user information
        $user = $this->getUserInfo($student['Added_For'] ?? 0);
        
        // Get RM information
        $rm = $this->getRMInfo($user['ID'] ?? 0);
        
        // Add academic information (5 courses * 7 fields = 35 fields)
        $academicRows = $this->getAcademicInfo($student['ID']);
        foreach ($academicRows as $academicRow) {
            $row = array_merge($row, $academicRow);
        }
        
        // Add user and RM info (3 fields)
        $row[] = $user['Code'] ?? '';
        $row[] = $user['Name'] ?? '';
        $row[] = $rm['Name'] ?? '';
        
        // Format dates
        $row[4] = !empty($row[4]) ? date("d-m-Y H:i A", strtotime($row[4])) : ""; // Added On
        $row[5] = !empty($row[5]) ? date("d-m-Y H:i A", strtotime($row[5])) : ""; // Processed By Center
        $row[6] = !empty($row[6]) ? date("d-m-Y H:i A", strtotime($row[6])) : ""; // Processed To University
        
        // Add export link
        $encode = base64_encode($student['ID'] . "W1Ebt1IhGN3ZOLplom9I");
        $row[] = '<i><a href="https://' . $_SERVER['HTTP_HOST'] . '/ams/app/applications/zip?id=' . $encode . '">Click Here</a></i>';
        
        // Add fee information
        $feeInfo = $this->getFeeInfo($student['ID'], $user['ID'] ?? 0);
        $row = array_merge($row, $feeInfo);
        
        // Add course information (semester/duration)
        $courseInfo = $this->getCourseInfo($student);
        $row = array_merge($row, $courseInfo);
        
        // Reindex the array to ensure sequential keys
        $row = array_values($row);
        
        return $row;
    }
    
    /**
     * Get user information
     */
    private function getUserInfo(int $userId): array
    {
        if (empty($userId)) {
            return ['Name' => '', 'Code' => '', 'ID' => 0];
        }
        
        try {
            $stmt = $this->db->prepare(
                "SELECT ID, Code, Name FROM Users WHERE ID = ?"
            );
            $stmt->execute([$userId]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: ['Name' => '', 'Code' => '', 'ID' => 0];
            
        } catch (PDOException $e) {
            error_log("Error in getUserInfo: " . $e->getMessage());
            return ['Name' => '', 'Code' => '', 'ID' => 0];
        }
    }
    
    /**
     * Get RM information
     */
    private function getRMInfo(int $userId): array
    {
        if (empty($userId)) {
            return ['Name' => ""];
        }
        
        try {
            $stmt = $this->db->prepare(
                "SELECT CONCAT(Users.Name, ' (', Users.Code, ')') as Name 
                 FROM University_User 
                 LEFT JOIN Users ON University_User.Reporting = Users.ID 
                 WHERE University_User.User_ID = ? 
                 AND University_User.University_ID = ?"
            );
            $stmt->execute([$userId, $this->uniId]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: ['Name' => ""];
            
        } catch (PDOException $e) {
            error_log("Error in getRMInfo: " . $e->getMessage());
            return ['Name' => ""];
        }
    }
    
    /**
     * Get academic information
     */
    private function getAcademicInfo(int $studentId): array
    {
        $courses = ['High School', 'Intermediate', 'UG', 'PG', 'Other'];
        $academicRows = [];
        
        try {
            // Fetch all academics for this student in one query
            $stmt = $this->db->prepare(
                "SELECT Type, Subject, Year, `Board/Institute`, Marks_Obtained, Max_Marks, Total_Marks 
                 FROM Student_Academics 
                 WHERE Student_ID = ?"
            );
            $stmt->execute([$studentId]);
            $academics = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Create lookup array by course type
            $academicLookup = [];
            foreach ($academics as $academic) {
                $academicLookup[$academic['Type']] = $academic;
            }
            
            // Build rows for each course type
            foreach ($courses as $course) {
                if (isset($academicLookup[$course])) {
                    $acad = $academicLookup[$course];
                    $academicRows[] = [
                        $acad['Type'] ?? $course,
                        $acad['Subject'] ?? '',
                        $acad['Year'] ?? '',
                        $acad['Board/Institute'] ?? '',
                        $acad['Marks_Obtained'] ?? '',
                        $acad['Max_Marks'] ?? '',
                        $acad['Total_Marks'] ?? ''
                    ];
                } else {
                    $academicRows[] = [$course, '', '', '', '', '', ''];
                }
            }
        } catch (PDOException $e) {
            error_log("Error in getAcademicInfo: " . $e->getMessage());
            // Return empty rows for all courses
            foreach ($courses as $course) {
                $academicRows[] = [$course, '', '', '', '', '', ''];
            }
        }
        
        // Flatten the array
        $flattened = [];
        foreach ($academicRows as $row) {
            $flattened = array_merge($flattened, $row);
        }
        
        return [$flattened]; // Return as array of flattened rows
    }
    
    /**
     * Get fee information
     */
    private function getFeeInfo(int $studentId, int $userId): array
    {
        $feeInfo = [];
        
        try {
            // Get fee structures
            $stmt = $this->db->prepare(
                "SELECT ID, Sharing FROM Fee_Structures 
                 WHERE University_ID = ? ORDER BY Fee_Applicable_ID"
            );
            $stmt->execute([$this->uniId]);
            $feeStructures = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get student fee
            $stmt = $this->db->prepare(
                "SELECT Fee FROM Student_Ledgers WHERE Student_ID = ? LIMIT 1"
            );
            $stmt->execute([$studentId]);
            $studentFee = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $feeJson = $studentFee ? (json_decode($studentFee['Fee'], true) ?: []) : [];
            
            // Add fee for each structure
            foreach ($feeStructures as $feeStructure) {
                $feeInfo[] = $feeJson[$feeStructure['ID']] ?? "";
                
                if ($feeStructure['Sharing'] == 1) {
                    $sharingStmt = $this->db->prepare(
                        "SELECT Fee FROM Fee_Variables 
                         WHERE Code = ? AND University_ID = ?"
                    );
                    $sharingStmt->execute([$userId, $this->uniId]);
                    $sharing = $sharingStmt->fetch(PDO::FETCH_ASSOC);
                    $feeInfo[] = $sharing ? $sharing['Fee'] : 0;
                } else {
                    $feeInfo[] = 0;
                }
            }
            
            // Add total
            $feeInfo[] = array_sum($feeJson);
            
        } catch (PDOException $e) {
            error_log("Error in getFeeInfo: " . $e->getMessage());
            // Add minimal defaults
            $feeInfo = [0, 0, 0];
        }
        
        return $feeInfo;
    }
    
    /**
     * Get course information (semester/duration)
     */
    private function getCourseInfo(array $student): array
    {
        $courseInfo = [];
        
        // Add semester fees for university ID 20
        if ($this->uniId == 20 || $this->uniId == 21) {
            $semesterFees = $this->getSemesterFees($student['ID']);
            $courseInfo = array_merge($courseInfo, $semesterFees);
        }
        
        // Add total fee received for university ID 41
        if ($this->uniId == 41) {
            $totalFee = $this->getTotalFeeReceived($student['ID']);
            $courseInfo[] = $totalFee;
        }
        
        // Add course name/duration
        $courseName = $student['Sub_Course'] ?? '';
        if ($this->uniId == 20 || $this->uniId == 21) {
            $courseInfo[] = $courseName;
        } else {
            $courseInfo[] = $this->getSkillDuration($courseName);
        }
        
        // Add transfer ID
        $courseInfo[] = $student['Is_Transferred'] ?? '';
        
        return $courseInfo;
    }
    
    /**
     * Get semester fees for university ID 20
     */
    private function getSemesterFees(int $studentId): array
    {
        $semester = $this->getMaxSemester();
        $semFees = [];
        
        for ($i = 1; $i <= $semester; $i++) {
            $semFees[] = $this->getSemesterFee($studentId, $i);
        }
        
        $semFees[] = array_sum($semFees);
        
        return $semFees;
    }
    
    /**
     * Get maximum semester count
     */
    private function getMaxSemester(): int
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT MAX(Min_Duration) AS semester FROM Sub_Courses 
                 WHERE Status = 1 AND University_ID = ?"
            );
            $stmt->execute([$this->uniId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result && $result['semester']) {
                $decoded = json_decode($result['semester'], true);
                return is_numeric($decoded) ? (int)$decoded : 6;
            }
        } catch (PDOException $e) {
            error_log("Error in getMaxSemester: " . $e->getMessage());
        }
        
        return 6;
    }
    
    /**
     * Get semester fee for a student
     */
    private function getSemesterFee(int $studentId, int $duration): float
    {
        try {
            $query = "SELECT Student_Ledgers.Fee FROM Student_Ledgers 
                      WHERE Student_Ledgers.Student_ID = ? AND Duration = ? 
                      AND Student_Ledgers.Transaction_ID IS NOT NULL 
                      AND Student_Ledgers.Status = 1 
                      ORDER BY Student_Ledgers.Type, Student_Ledgers.Created_AT 
                      LIMIT 1";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([$studentId, $duration]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (empty($result['Fee'])) {
                return 0;
            }
            
            if (is_numeric($result['Fee'])) {
                return (float)$result['Fee'];
            }
            
            $fee = json_decode($result['Fee'], true);
            return isset($fee['Paid']) ? abs((float)$fee['Paid']) : 0;
            
        } catch (PDOException $e) {
            error_log("Error in getSemesterFee: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get total fee received for university ID 41
     */
    private function getTotalFeeReceived(int $studentId): float
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT SUM(JSON_UNQUOTE(JSON_EXTRACT(Fee, '$.Paid'))) AS total_fee 
                 FROM Student_Ledgers 
                 WHERE Student_Ledgers.Student_ID = ? AND Student_Ledgers.Status = 1"
            );
            $stmt->execute([$studentId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return abs((float)($result['total_fee'] ?? 0));
        } catch (PDOException $e) {
            error_log("Error in getTotalFeeReceived: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get skill duration based on course name
     */
    private function getSkillDuration(string $courseName): string
    {
        $courseLower = strtolower($courseName);
        
        if (strpos($courseLower, '11') !== false && strpos($courseLower, 'adv') !== false) {
            return "11/Certified";
        } elseif (strpos($courseLower, '11') !== false) {
            return "11/Advanced-Certified";
        } elseif (strpos($courseLower, '6') !== false) {
            return "6/Certified";
        } elseif (strpos($courseLower, '3') !== false) {
            return "3/Certification";
        }
        
        return '';
    }
}