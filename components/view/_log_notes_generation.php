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
        case 'log notes main':
            if(isset($_POST['database_table']) && !empty($_POST['database_table']) && isset($_POST['reference_id']) && !empty($_POST['reference_id'])){
                $logNote = '';

                $databaseTable = htmlspecialchars($_POST['database_table'], ENT_QUOTES, 'UTF-8');
                $referenceID = htmlspecialchars($_POST['reference_id'], ENT_QUOTES, 'UTF-8');

                $sql = $databaseModel->getConnection()->prepare('CALL generateLogNotes(:databaseTable, :referenceID)');
                $sql->bindValue(':databaseTable', $databaseTable, PDO::PARAM_STR);
                $sql->bindValue(':referenceID', $referenceID, PDO::PARAM_INT);
                $sql->execute();
                $options = $sql->fetchAll(PDO::FETCH_ASSOC);
                $count = count($options);
                $sql->closeCursor();

                foreach ($options as $index => $row) {
                    $log = $row['log'];
                    $changedBy = $row['changed_by'];
                    $timeElapsed = $systemModel->timeElapsedString($row['changed_at']);
                
                    $userDetails = $authenticationModel->getLoginCredentials($changedBy, null);
                    $fileAs = $userDetails['file_as'];
                    $profilePicture = $systemModel->checkImage($userDetails['profile_picture'] ?? null, 'profile');
                
                    $marginClass = ($index === $count - 1) ? 'mb-0' : 'mb-3';
                
                    $logNote .= '<div class="p-4 rounded-4 text-bg-light ' . $marginClass . '">
                                    <div class="d-flex align-items-center gap-6 flex-wrap">
                                        <img src="'. $profilePicture .'" alt="user" class="rounded-circle" width="33" height="33">
                                        <h6 class="mb-0">'. $fileAs .'</h6>
                                        <span class="fs-2">
                                            <span class="p-1 text-bg-muted rounded-circle d-inline-block me-2"></span> '. $timeElapsed .'
                                        </span>
                                    </div>
                                    <p class="mt-3 mb-0">'. $log .'</p>
                                </div>';
                }

                if(empty($logNote)){
                    $logNote = '<div class="p-4 rounded-4 text-bg-light mb-0 text-center">
                                No log notes found.
                            </div>';
                }

                $response[] = [
                    'LOG_NOTE' => $logNote
                ];

                echo json_encode($response);
            }
        break;
        # -------------------------------------------------------------

        # -------------------------------------------------------------
        case 'log notes':
            if(isset($_POST['database_table']) && !empty($_POST['database_table']) && isset($_POST['reference_id']) && !empty($_POST['reference_id'])){
                $logNote = '';

                $databaseTable = htmlspecialchars($_POST['database_table'], ENT_QUOTES, 'UTF-8');
                $referenceID = htmlspecialchars($_POST['reference_id'], ENT_QUOTES, 'UTF-8');

                $sql = $databaseModel->getConnection()->prepare('CALL generateLogNotes(:databaseTable, :referenceID)');
                $sql->bindValue(':databaseTable', $databaseTable, PDO::PARAM_STR);
                $sql->bindValue(':referenceID', $referenceID, PDO::PARAM_INT);
                $sql->execute();
                $options = $sql->fetchAll(PDO::FETCH_ASSOC);
                $count = count($options);
                $sql->closeCursor();

                foreach ($options as $index => $row) {
                    $log = $row['log'];
                    $changedBy = $row['changed_by'];
                    $timeElapsed = $systemModel->timeElapsedString($row['changed_at']);
                
                    $userDetails = $authenticationModel->getLoginCredentials($changedBy, null);
                    $fileAs = $userDetails['file_as'];
                    $profilePicture = $systemModel->checkImage($userDetails['profile_picture'] ?? null, 'profile');
                
                    $marginClass = ($index === $count - 1) ? 'mb-0' : 'mb-3';
                
                    $logNote .= '<div class="p-4 rounded-4 text-bg-light ' . $marginClass . '">
                                    <div class="d-flex align-items-center gap-6 flex-wrap">
                                        <img src="'. $profilePicture .'" alt="user" class="rounded-circle" width="33" height="33">
                                        <h6 class="mb-0">'. $fileAs .'</h6>
                                        <span class="fs-2">
                                            <span class="p-1 text-bg-muted rounded-circle d-inline-block me-2"></span> '. $timeElapsed .'
                                        </span>
                                    </div>
                                    <p class="mt-3 mb-0">'. $log .'</p>
                                </div>';
                }

                if(empty($logNote)){
                    $logNote = '<div class="p-4 rounded-4 text-bg-light mb-0 text-center">
                                    No log notes found.
                                </div>';
                }

                $response[] = [
                    'LOG_NOTE' => $logNote
                ];

                echo json_encode($response);
            }
        break;
        # -------------------------------------------------------------
    }
}

?>