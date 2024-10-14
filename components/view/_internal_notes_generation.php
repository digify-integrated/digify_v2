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
        case 'internal notes':
            if(isset($_POST['database_table']) && !empty($_POST['database_table']) && isset($_POST['reference_id']) && !empty($_POST['reference_id'])){
                $internalNoteList = '';

                $databaseTable = htmlspecialchars($_POST['database_table'], ENT_QUOTES, 'UTF-8');
                $referenceID = htmlspecialchars($_POST['reference_id'], ENT_QUOTES, 'UTF-8');

                $sql = $databaseModel->getConnection()->prepare('CALL generateInternalNotes(:databaseTable, :referenceID)');
                $sql->bindValue(':databaseTable', $databaseTable, PDO::PARAM_STR);
                $sql->bindValue(':referenceID', $referenceID, PDO::PARAM_INT);
                $sql->execute();
                $options = $sql->fetchAll(PDO::FETCH_ASSOC);
                $sql->closeCursor();

                foreach ($options as $row) {
                    $attachment = '';
                    $internalNotesID = $row['internal_notes_id'];
                    $internalNote = $row['internal_note'];
                    $internalNoteBy = $row['internal_note_by'];
                    $timeElapsed = $systemModel->timeElapsedString($row['internal_note_date']);

                    $userDetails = $authenticationModel->getLoginCredentials($internalNoteBy, null);
                    $fileAs = $userDetails['file_as'];
                    $profilePicture = $systemModel->checkImage($userDetails['profile_picture'] ?? null, 'profile');

                    if(!empty($internalNote)){
                        $internalNote = '<p class="my-3">'. $internalNote .'</p>';
                    }

                    $internalNotesAttachments = $globalModel->getInternalNotesAttachment($internalNotesID);
                    $numberOfValues = count($internalNotesAttachments);

                    if($numberOfValues > 0){
                        foreach ($internalNotesAttachments as $internalNotesAttachment) {
                            $attachmentFileName = $internalNotesAttachment['attachment_file_name'];
                            $attachmentFileSize = $systemModel->getFormatBytes($internalNotesAttachment['attachment_file_size']);
                            $attachmentPathFile = $internalNotesAttachment['attachment_path_file'];

                            if(file_exists(str_replace('./components/', '../../', $attachmentPathFile))){
                                $fileExtension = pathinfo($attachmentPathFile, PATHINFO_EXTENSION);                                
                                $attachmentImage = ' <img src="'. $systemModel->getFileExtensionIcon($fileExtension) .'" alt="attachment" width="30">';
        
                                $attachment .= '<a href="'. $attachmentPathFile .'" target="_blank">
                                                    <div class="mt-2 mb-0 card p-3 rounded shadow-none border">
                                                        <div class="d-flex align-items-center">
                                                            <div class="position-relative">
                                                                '. $attachmentImage .'
                                                            </div>
                                                            <div class="ms-3">
                                                                <h6 class="mb-0 fs-5">'. $attachmentFileName .'</h6>
                                                                <small>'. $attachmentFileSize .'</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </a>';
                            }
                        }
                    }                    
                    
                    $internalNoteList .= '<div class="p-4 rounded-4 text-bg-light mb-3">
                                    <div class="d-flex align-items-center gap-6 flex-wrap">
                                        <img src="'. $profilePicture .'" alt="user" class="rounded-circle" width="33" height="33">
                                        <h6 class="mb-0">'. $fileAs .'</h6>
                                        <span class="fs-2">
                                            <span class="p-1 text-bg-muted rounded-circle d-inline-block me-2"></span> '. $timeElapsed .'
                                        </span>
                                    </div>
                                    '. $internalNote .'
                                    '. $attachment .'
                                </div>';
                }

                if(empty($internalNoteList)){
                    $internalNoteList = '<div class="p-4 rounded-4 text-bg-light mb-0 text-center">
                                No internal notes found.
                            </div>';
                }

                $response[] = [
                    'INTERNAL_NOTES' => $internalNoteList
                ];

                echo json_encode($response);
            }
        break;
        # -------------------------------------------------------------
    }
}

?>