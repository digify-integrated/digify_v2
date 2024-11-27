<?php
require('../../../../components/configurations/session.php');
require('../../../../components/configurations/config.php');
require('../../../../components/model/database-model.php');
require('../../../../components/model/system-model.php');
require('../../../../components/model/security-model.php');

$databaseModel = new DatabaseModel();
$systemModel = new SystemModel();
$securityModel = new SecurityModel();

if(isset($_POST['type']) && !empty($_POST['type'])){
    $type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);
    $pageID = isset($_POST['page_id']) ? $_POST['page_id'] : null;
    $pageLink = isset($_POST['page_link']) ? $_POST['page_link'] : null;
    $response = [];
    
    switch ($type) {
        # -------------------------------------------------------------
        case 'department table':
            $filterParentDepartment = isset($_POST['parent_department_filter']) && is_array($_POST['parent_department_filter']) 
            ? "'" . implode("','", array_map('trim', $_POST['parent_department_filter'])) . "'" 
            : null;

            $filterManager = isset($_POST['manager_filter']) && is_array($_POST['manager_filter']) 
            ? "'" . implode("','", array_map('trim', $_POST['manager_filter'])) . "'" 
            : null;

            $sql = $databaseModel->getConnection()->prepare('CALL generateDepartmentTable(:filterParentDepartment, :filterManager)');
            $sql->bindValue(':filterParentDepartment', $filterParentDepartment, PDO::PARAM_STR);
            $sql->bindValue(':filterManager', $filterManager, PDO::PARAM_STR);
            $sql->execute();
            $options = $sql->fetchAll(PDO::FETCH_ASSOC);
            $sql->closeCursor();

            foreach ($options as $row) {
                $departmentID = $row['department_id'];
                $departmentName = $row['department_name'];
                $parentDepartmentName = $row['parent_department_name'];
                $managerName = $row['manager_name'];

                $departmentIDEncrypted = $securityModel->encryptData($departmentID);

                $response[] = [
                    'CHECK_BOX' => '<div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                        <input class="form-check-input datatable-checkbox-children" type="checkbox" value="'. $departmentID .'">
                                    </div>',
                    'DEPARTMENT_NAME' => $departmentName,
                    'PARENT_DEPARTMENT_NAME' => $parentDepartmentName,
                    'MANAGER_NAME' => $managerName,
                    'LINK' => $pageLink .'&id='. $departmentIDEncrypted
                ];
            }

            echo json_encode($response);
        break;
        # -------------------------------------------------------------

        # -------------------------------------------------------------
        case 'parent department options':
            $departmentID = (isset($_POST['department_id'])) ? filter_input(INPUT_POST, 'department_id', FILTER_VALIDATE_INT) : null;
            $multiple = (isset($_POST['multiple'])) ? filter_input(INPUT_POST, 'multiple', FILTER_VALIDATE_INT) : false;

            $sql = $databaseModel->getConnection()->prepare('CALL generateParentDepartmentOptions(:departmentID)');
            $sql->bindValue(':departmentID', $departmentID, PDO::PARAM_INT);
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
                    'id' => $row['department_id'],
                    'text' => $row['department_name']
                ];
            }

            echo json_encode($response);
        break;
        # -------------------------------------------------------------

        # -------------------------------------------------------------
        case 'department options':
            $multiple = (isset($_POST['multiple'])) ? filter_input(INPUT_POST, 'multiple', FILTER_VALIDATE_INT) : false;

            $sql = $databaseModel->getConnection()->prepare('CALL generateDepartmentOptions()');
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
                    'id' => $row['department_id'],
                    'text' => $row['department_name']
                ];
            }

            echo json_encode($response);
        break;
        # -------------------------------------------------------------
    }
}

?>