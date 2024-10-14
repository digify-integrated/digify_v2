<?php
require('../../components/configurations/session.php');
require('../../components/configurations/config.php');
require('../../components/model/database-model.php');
require('../../components/model/system-model.php');
require('../../components/model/security-model.php');
require('../../apps/security/authentication/model/authentication-model.php');

$databaseModel = new DatabaseModel();
$systemModel = new SystemModel();
$securityModel = new SecurityModel();
$authenticationModel = new AuthenticationModel($databaseModel, $securityModel);

if(isset($_POST['type']) && !empty($_POST['type'])){
    $type = htmlspecialchars($_POST['type'], ENT_QUOTES, 'UTF-8');
    $response = [];
    
    switch ($type) {
        # -------------------------------------------------------------
        case 'export options':
            if(isset($_POST['table_name']) && !empty($_POST['table_name'])){
                $tableName = filter_input(INPUT_POST, 'table_name', FILTER_SANITIZE_STRING);

                $sql = $databaseModel->getConnection()->prepare('CALL generateExportOption(:databaseName, :tableName)');
                $sql->bindValue(':databaseName', DB_NAME, PDO::PARAM_STR);
                $sql->bindValue(':tableName', $tableName, PDO::PARAM_STR);
                $sql->execute();
                $options = $sql->fetchAll(PDO::FETCH_ASSOC);
                $sql->closeCursor();

                foreach ($options as $row) {
                    $response[] = [
                        'id' => $row['column_name'],
                        'text' => $row['column_name']
                    ];
                }

                echo json_encode($response);
            }
        break;
        # -------------------------------------------------------------
    }
}

?>