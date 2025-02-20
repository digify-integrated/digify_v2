<?php
require('../../../../components/configurations/session.php');
require('../../../../components/configurations/config.php');
require('../../../../components/model/database-model.php');
require('../../../../components/model/system-model.php');
require('../../../../components/model/security-model.php');
require('../../../settings/authentication/model/authentication-model.php');

$databaseModel = new DatabaseModel();
$systemModel = new SystemModel();
$securityModel = new SecurityModel();
$authenticationModel = new AuthenticationModel($databaseModel, $securityModel);

if(isset($_POST['type']) && !empty($_POST['type'])){
    $userID = $_SESSION['user_account_id'];
    $type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);
    $pageID = isset($_POST['page_id']) ? $_POST['page_id'] : null;
    $pageLink = isset($_POST['page_link']) ? $_POST['page_link'] : null;
    $response = [];
    
    switch ($type) {
        # -------------------------------------------------------------
        case 'employee cards':
            $searchValue = isset($_POST['search_value']) ? $_POST['search_value'] : null;
            $limit = isset($_POST['limit']) ? $_POST['limit'] : null;
            $offset = isset($_POST['offset']) ? $_POST['offset'] : null;

            $filterCompany = isset($_POST['filter_by_company']) && is_array($_POST['filter_by_company']) 
            ? "'" . implode("','", array_map('trim', $_POST['filter_by_company'])) . "'" 
            : null;

            $filterDepartment = isset($_POST['filter_by_department']) && is_array($_POST['filter_by_department']) 
            ? "'" . implode("','", array_map('trim', $_POST['filter_by_department'])) . "'" 
            : null;

            $filterJobPosition = isset($_POST['filter_by_job_position']) && is_array($_POST['filter_by_job_position']) 
            ? "'" . implode("','", array_map('trim', $_POST['filter_by_job_position'])) . "'" 
            : null;

            $filterEmployeeStatus = isset($_POST['filter_by_employee_status']) && is_array($_POST['filter_by_employee_status']) 
            ? "'" . implode("','", array_map('trim', $_POST['filter_by_employee_status'])) . "'" 
            : null;

            $filterWorkLocation = isset($_POST['filter_by_work_location']) && is_array($_POST['filter_by_work_location']) 
            ? "'" . implode("','", array_map('trim', $_POST['filter_by_work_location'])) . "'" 
            : null;

            $filterEmploymentType = isset($_POST['filter_by_employment_type']) && is_array($_POST['filter_by_employment_type']) 
            ? "'" . implode("','", array_map('trim', $_POST['filter_by_employment_type'])) . "'" 
            : null;

            $filterGender = isset($_POST['filter_by_gender']) && is_array($_POST['filter_by_gender']) 
            ? "'" . implode("','", array_map('trim', $_POST['filter_by_gender'])) . "'" 
            : null;

            $sql = $databaseModel->getConnection()->prepare('CALL generateEmployeeCard(:searchValue, :filterCompany, :filterDepartment, :filterJobPosition, :filterEmployeeStatus, :filterWorkLocation, :filterEmploymentType, :filterGender, :limit, :offset)');
            $sql->bindValue(':searchValue', $searchValue, PDO::PARAM_STR);
            $sql->bindValue(':filterCompany', $filterCompany, PDO::PARAM_STR);
            $sql->bindValue(':filterDepartment', $filterDepartment, PDO::PARAM_STR);
            $sql->bindValue(':filterJobPosition', $filterJobPosition, PDO::PARAM_STR);
            $sql->bindValue(':filterEmployeeStatus', $filterEmployeeStatus, PDO::PARAM_STR);
            $sql->bindValue(':filterWorkLocation', $filterWorkLocation, PDO::PARAM_STR);
            $sql->bindValue(':filterEmploymentType', $filterEmploymentType, PDO::PARAM_STR);
            $sql->bindValue(':filterGender', $filterGender, PDO::PARAM_STR);
            $sql->bindValue(':limit', $limit, PDO::PARAM_INT);
            $sql->bindValue(':offset', $offset, PDO::PARAM_INT);
            $sql->execute();
            $options = $sql->fetchAll(PDO::FETCH_ASSOC);
            $sql->closeCursor();

            foreach ($options as $row) {
                $employeeID = $row['employee_id'];
                $fullName = $row['full_name'];
                $departmentName = $row['department_name'];
                $jobPositionName = $row['job_position_name'];
                $employmentStatus = $row['employment_status'];
                $employeeImage = $systemModel->checkImage($row['employee_image'] ?? null, 'profile');

                $badgeClass = $employmentStatus == 'Active' ? 'bg-success' : 'bg-danger';
                $employmentStatusBadge = '<div class="'. $badgeClass .' position-absolute border border-4 border-body h-15px w-15px rounded-circle translate-middle start-100 top-100 ms-n3 mt-n3"></div>';

                $employeeIDEncrypted = $securityModel->encryptData($employeeID);

                $employeeCard = '<div class="col-md-3">
                                    <div class="card">
                                        <div class="card-body d-flex flex-center flex-column pt-12 p-9">
                                            <a href="'. $pageLink .'&id='. $employeeIDEncrypted .'" class="cursor-pointer">
                                                <div class="symbol symbol-65px symbol-circle mb-5">
                                                    <img src="'. $employeeImage .'" alt="image">
                                                    '. $employmentStatusBadge .'
                                                </div>
                                            </a>

                                            <a href="'. $pageLink .'&id='. $employeeIDEncrypted .'" class="fs-4 text-gray-800 text-hover-primary fw-bold mb-0">'. $fullName .'</a>

                                            <div class="fw-semibold text-gray-500">'. $jobPositionName .'</div>
                                        </div>
                                    </div>
                                </div>';

                $response[] = [
                    'EMPLOYEE_CARD' => $employeeCard
                ];
            }

            echo json_encode($response);
        break;
        # -------------------------------------------------------------

        # -------------------------------------------------------------
        case 'language list':
            if(isset($_POST['employee_id']) && !empty($_POST['employee_id'])){
                $list = '';
                $employeeID = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);

                $sql = $databaseModel->getConnection()->prepare('CALL generateEmployeeLanguageList(:employeeID)');
                $sql->bindValue(':employeeID', $employeeID, PDO::PARAM_INT);
                $sql->execute();
                $options = $sql->fetchAll(PDO::FETCH_ASSOC);
                $sql->closeCursor();

                $writeAccess = $authenticationModel->checkAccessRights($userID, $pageID, 'write');

                foreach ($options as $key => $row) {
                    $employeeLanguageID = $row['employee_language_id'];
                    $languageName = $row['language_name'];
                    $languageProficiencyName = $row['language_proficiency_name'];

                    $button = '';
                    if($writeAccess['total'] > 0){
                        $button = '<button type="button" class="btn btn-icon btn-active-light-primary w-30px h-30px ms-auto delete-employee-language" data-employee-language-id="' . $employeeLanguageID . '">
                                        <i class="ki-outline ki-trash fs-3"></i>
                                    </button>';
                    }

                    $list .= '<div class="d-flex flex-stack">
                                    <div class="d-flex flex-column">
                                        <span>'. $languageName .'</span>
                                        <span class="text-muted fs-6">'. $languageProficiencyName .'</span>
                                    </div>

                                    <div class="d-flex justify-content-end align-items-center">
                                        '. $button .'
                                    </div>
                                </div>';

                    if ($row !== end($options)) {
                        $list .= '<div class="separator separator-dashed my-5"></div>';
                    }
                }

                if(empty($list)){
                    $list = '<div class="d-flex flex-stack">
                                    <div class="d-flex align-items-center flex-row-fluid flex-wrap mb-4">
                                        <div class="flex-grow-1 me-2">
                                            <div class="text-gray-800 fs-5 fw-bold">No language found</div>
                                        </div>
                                    </div>
                                </div>';
                }

                $response[] = [
                    'LANGUAGE_SUMMARY' => $list
                ];

                echo json_encode($response);
            }
        break;
        # -------------------------------------------------------------

        # -------------------------------------------------------------
        case 'educational background list':
            if(isset($_POST['employee_id']) && !empty($_POST['employee_id'])){
                $list = '';
                $employeeID = filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT);

                $sql = $databaseModel->getConnection()->prepare('CALL generateEmployeeEducationalBackgroundList(:employeeID)');
                $sql->bindValue(':employeeID', $employeeID, PDO::PARAM_INT);
                $sql->execute();
                $options = $sql->fetchAll(PDO::FETCH_ASSOC);
                $sql->closeCursor();

                $writeAccess = $authenticationModel->checkAccessRights($userID, $pageID, 'write');

                foreach ($options as $key => $row) {
                    $employeeEducationID = $row['employee_education_id'];
                    $school = $row['school'];
                    $degree = $row['degree'];
                    $fieldOfStudy = $row['field_of_study'];
                    $startMonth = $row['start_month'];
                    $startYear = $row['start_year'];
                    $endMonth = $row['end_month'];
                    $endYear = $row['end_year'];
                    $activitiesSocieties = $row['activities_societies'];
                    $educationDescription = $row['education_description'];

                    $degreeFieldOfStudy = (!empty($degree) || !empty($fieldOfStudy)) ?
                    '<div class="fs-6 fw-semibold text-gray-600">' . trim($degree . (!empty($degree) && !empty($fieldOfStudy) ? ' · ' : '') . $fieldOfStudy) . '</div>' : '';

                    $activitiesSocieties = !empty($activitiesSocieties) ? '<div class="fs-6 fw-semibold text-gray-600">Activities and societies: ' . $activitiesSocieties . '</div>' : '';
                    $educationDescription = !empty($educationDescription) ? '<div class="fs-6 fw-semibold text-gray-600">' . $educationDescription . '</div>' : '';
            
                    $startDateFormatted = date('F', mktime(0, 0, 0, $startMonth, 1));
                    $startDate = $startDateFormatted . ' ' . $startYear;                
    
                    $endDate = (!empty($endMonth) && !empty($endYear)) ? date('F', mktime(0, 0, 0, $endMonth, 1)) . ' ' . $endYear : 'Present';

                    $button = '';
                    if($writeAccess['total'] > 0){
                        $button = '<button class="btn btn-sm btn-light btn-active-light-primary me-3" data-employee-education-id="' . $employeeEducationID . '">
                                            <span class="indicator-label"> Delete</span>
                                        </button>
                                        <button class="btn btn-sm btn-light btn-active-light-primary" data-bs-toggle="modal" data-bs-target="#employee_education_modal" data-employee-education-id="' . $employeeEducationID . '">
                                            Edit
                                        </button>';
                    }

                    $list .= '<div class="col-xl-12">
                                <div class="card card-dashed h-xl-100 flex-row flex-stack flex-wrap p-6">
                                    <div class="d-flex flex-column py-2">
                                        <div class="d-flex align-items-center fs-5 fw-bold mb-5">
                                           '. $school .'
                                        </div>
                                        '. $degreeFieldOfStudy .'
                                        <div class="fs-6 fw-semibold text-gray-600">'. $startDate .' - '. $endDate .'</div>
                                        '. $activitiesSocieties .'
                                        '. $educationDescription .'
                                    </div>
                                    
                                    <div class="d-flex align-items-center py-2">
                                        '. $button .'
                                    </div>
                                </div>
                            </div>';
                }

                if($writeAccess['total'] > 0){
                    $list .= ' <div class="col-xl-12">
                                    <div class="notice d-flex bg-light-primary rounded border-primary border border-dashed flex-stack h-xl-100 mb-10 p-6">
                                        <div class="d-flex flex-stack flex-grow-1 flex-wrap flex-md-nowrap">
                                            <div class="mb-3 mb-md-0 fw-semibold">
                                                <h4 class="text-gray-900 fw-bold">Add New Educational Background for Employee</h4>
                                                <div class="fs-6 text-gray-700 pe-7">Provide detailed information about the employee\'s educational background, including school, degree, and field of study.</div>
                                            </div>
                                            <a href="#" class="btn btn-primary px-6 align-self-center text-nowrap" data-bs-toggle="modal" data-bs-target="#employee_education_modal"> New Educational Background</a>
                                        </div>
                                    </div>
                                </div>';

                }

                $response[] = [
                    'EDUCATIONAL_BACKGROUND_SUMMARY' => $list
                ];

                echo json_encode($response);
            }
        break;
        # -------------------------------------------------------------

        # -------------------------------------------------------------
        case 'parent employee options':
            $employeeID = (isset($_POST['employee_id'])) ? filter_input(INPUT_POST, 'employee_id', FILTER_VALIDATE_INT) : null;
            $multiple = (isset($_POST['multiple'])) ? filter_input(INPUT_POST, 'multiple', FILTER_VALIDATE_INT) : false;

            $sql = $databaseModel->getConnection()->prepare('CALL generateParentEmployeeOptions(:employeeID)');
            $sql->bindValue(':employeeID', $employeeID, PDO::PARAM_INT);
            $sql->execute();
            $options = $sql->fetchAll(PDO::FETCH_ASSOC);
            $sql->closeCursor();

            if(!$multiple){
                $response[] = [
                    'id' => '',
                    'text' => '--'
                ];
            }            

            foreach ($options as $row) {
                $response[] = [
                    'id' => $row['employee_id'],
                    'text' => $row['full_name']
                ];
            }

            echo json_encode($response);
        break;
        # -------------------------------------------------------------

        # -------------------------------------------------------------
        case 'employee options':
            $multiple = (isset($_POST['multiple'])) ? filter_input(INPUT_POST, 'multiple', FILTER_VALIDATE_INT) : false;

            $sql = $databaseModel->getConnection()->prepare('CALL generateEmployeeOptions()');
            $sql->execute();
            $options = $sql->fetchAll(PDO::FETCH_ASSOC);
            $sql->closeCursor();

            if(!$multiple){
                $response[] = [
                    'id' => '',
                    'text' => '--'
                ];
            }            

            foreach ($options as $row) {
                $response[] = [
                    'id' => $row['employee_id'],
                    'text' => $row['full_name']
                ];
            }

            echo json_encode($response);
        break;
        # -------------------------------------------------------------
    }
}

?>