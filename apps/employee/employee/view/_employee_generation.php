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
        case 'employee table':
            $filterParentEmployee = isset($_POST['parent_employee_filter']) && is_array($_POST['parent_employee_filter']) 
            ? "'" . implode("','", array_map('trim', $_POST['parent_employee_filter'])) . "'" 
            : null;

            $filterManager = isset($_POST['manager_filter']) && is_array($_POST['manager_filter']) 
            ? "'" . implode("','", array_map('trim', $_POST['manager_filter'])) . "'" 
            : null;

            $sql = $databaseModel->getConnection()->prepare('CALL generateEmployeeTable(:filterParentEmployee, :filterManager)');
            $sql->bindValue(':filterParentEmployee', $filterParentEmployee, PDO::PARAM_STR);
            $sql->bindValue(':filterManager', $filterManager, PDO::PARAM_STR);
            $sql->execute();
            $options = $sql->fetchAll(PDO::FETCH_ASSOC);
            $sql->closeCursor();

            foreach ($options as $row) {
                $employeeID = $row['employee_id'];
                $employeeName = $row['employee_name'];
                $parentEmployeeName = $row['parent_employee_name'];
                $managerName = $row['manager_name'];

                $employeeIDEncrypted = $securityModel->encryptData($employeeID);

                $response[] = [
                    'CHECK_BOX' => '<div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                        <input class="form-check-input datatable-checkbox-children" type="checkbox" value="'. $employeeID .'">
                                    </div>',
                    'DEPARTMENT_NAME' => $employeeName,
                    'PARENT_DEPARTMENT_NAME' => $parentEmployeeName,
                    'MANAGER_NAME' => $managerName,
                    'LINK' => $pageLink .'&id='. $employeeIDEncrypted
                ];
            }

            echo json_encode($response);
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
                    'text' => $row['employee_name']
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
                    'text' => $row['employee_name']
                ];
            }

            echo json_encode($response);
        break;
        # -------------------------------------------------------------
    }
}

?>