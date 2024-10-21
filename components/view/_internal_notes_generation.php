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
                $count = count($options);
                $sql->closeCursor();

                foreach ($options as $index => $row) {
                    $attachment = '';
                    $internalNotesID = $row['internal_notes_id'];
                    $internalNote = $row['internal_note'];
                    $internalNoteBy = $row['internal_note_by'];
                    $timeElapsed = $systemModel->timeElapsedString($row['internal_note_date']);

                    $userDetails = $authenticationModel->getLoginCredentials($internalNoteBy, null);
                    $fileAs = $userDetails['file_as'];
                    $profilePicture = $systemModel->checkImage($userDetails['profile_picture'] ?? null, 'profile');

                    $internalNotesAttachments = $authenticationModel->getInternalNotesAttachment($internalNotesID);
                    $numberOfValues = count($internalNotesAttachments);

                    $marginClass = ($index === $count - 1) ? 'mb-0' : 'mb-10';

                    if($numberOfValues > 0){
                        foreach ($internalNotesAttachments as $internalNotesAttachment) {
                            $attachmentFileName = $internalNotesAttachment['attachment_file_name'];
                            $attachmentFileSize = $systemModel->getFormatBytes($internalNotesAttachment['attachment_file_size']);
                            $attachmentPathFile = $internalNotesAttachment['attachment_path_file'];

                            if(file_exists(str_replace('./components/', '../../', $attachmentPathFile))){
                                $fileExtension = pathinfo($attachmentPathFile, PATHINFO_EXTENSION);                                
                                $attachmentImage = ' <img src="'. $systemModel->getFileExtensionIcon($fileExtension) .'" alt="attachment" width="30">';
        
                                $attachment .= ' <div class="d-flex flex-aligns-center pe-10 pe-lg-20">
                                                    <img alt="" class="w-30px me-3" src="'. $systemModel->getFileExtensionIcon($fileExtension) .'" />
                                                    <div class="ms-1 fw-semibold">
                                                        <a href="'. $attachmentPathFile .'" class="fs-6 text-hover-primary fw-bold" target="_blank" >'. $attachmentFileName .'</a>
                                                            <div class="text-gray-500">
                                                               '. $attachmentFileSize .'
                                                             </div>
                                                        </div>
                                                    </div>';
                            }
                        }

                        $internalNoteList .= '<div class="timeline-item">
                                                <div class="timeline-line"></div>
                                                <div class="timeline-icon">
                                                    <i class="ki-outline ki-disconnect fs-2 text-gray-500"></i>
                                                </div>
                                                <div class="timeline-content '. $marginClass .' mt-n1">
                                                    <div class="mb-5 pe-3">
                                                        <a href="javascript:void(0);" class="fs-5 fw-semibold text-gray-800 text-hover-primary mb-2">'. $internalNote .'</a>
                                                        <div class="d-flex align-items-center mt-1 fs-6">
                                                            <div class="text-muted me-2 fs-7">
                                                                '. $timeElapsed .'
                                                            </div>
                                                            <div class="symbol symbol-circle symbol-25px" data-bs-toggle="tooltip" data-bs-boundary="window" data-bs-placement="top" title="'. $fileAs .'">
                                                                <img src="'. $profilePicture .'" alt="img" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="overflow-auto pb-5">
                                                        <div class="d-flex align-items-center border border-dashed border-gray-300 rounded min-w-700px p-5">
                                                        '. $attachment .'
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>';
                    }
                    else{
                        $internalNoteList .= '<div class="timeline-item">
                                                <div class="timeline-line"></div>
                                                <div class="timeline-icon">
                                                    <i class="ki-outline ki-message-text-2 fs-2 text-gray-500"></i>
                                                </div>
                                                <div class="timeline-content '. $marginClass .' mt-n1">
                                                    <div class="pe-3 mb-5">
                                                        <div class="fs-5 fw-semibold mb-2">
                                                            '. $internalNote .'
                                                        </div>
                                                        <div class="d-flex align-items-center mt-1 fs-6">
                                                            <div class="text-muted me-2 fs-7">
                                                                '. $timeElapsed .'
                                                            </div>
                                                            <div class="symbol symbol-circle symbol-25px" data-bs-toggle="tooltip" data-bs-boundary="window" data-bs-placement="top" title="'. $fileAs .'">
                                                                <img src="'. $profilePicture .'" alt="img" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>';
                    }
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