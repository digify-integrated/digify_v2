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
                
                    $marginClass = ($index === $count - 1) ? 'mb-0' : 'mb-9';
                
                    $logNote .= '<div class="timeline-item">
                                    <div class="timeline-line"></div>
                                        <div class="timeline-icon">
                                            <i class="ki-outline ki-message-text-2 fs-2 text-gray-500"></i>
                                        </div>
                                        <div class="timeline-content '. $marginClass .' mt-n1">
                                            <div class="pe-3 mb-5">
                                                <div class="fs-6 fw-semibold mb-2">
                                                    '. $log .'
                                                 </div>
                                                <div class="d-flex align-items-center mt-1 fs-6">
                                                    <div class="text-muted me-2 fs-7">
                                                        Logged: '. $timeElapsed .' by
                                                    </div>
                                                    <div class="symbol symbol-circle symbol-25px me-2">
                                                        <img src="'. $profilePicture .'" alt="img" />
                                                    </div>
                                                    <span class="text-primary fw-bold me-1 fs-7">'. $fileAs .'</span>
                                                 </div>
                                            </div>
                                        </div>
                                    </div>';
                }

                if(empty($logNote)){
                    $logNote = '<div class="mb-0">
                                    <div class="card card-bordered w-100">   
                                        <div class="card-body">    
                                            <p class="fw-normal fs-6 text-gray-700 m-0">
                                                No log notes found.
                                            </p>   
                                        </div>
                                    </div>
                                </div>';
                }

                $response[] = [
                    'LOG_NOTES' => $logNote
                ];

                echo json_encode($response);
            }
        break;
        # -------------------------------------------------------------
    }
}

?>