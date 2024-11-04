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
    $type = htmlspecialchars($_POST['type'], ENT_QUOTES, 'UTF-8');
    $pageID = isset($_POST['page_id']) ? $_POST['page_id'] : null;
    $pageLink = isset($_POST['page_link']) ? $_POST['page_link'] : null;
    $response = [];
    
    switch ($type) {
        # -------------------------------------------------------------
        case 'subscription tier table':
            $sql = $databaseModel->getConnection()->prepare('CALL generateSubscriptionTierTable()');
            $sql->execute();
            $options = $sql->fetchAll(PDO::FETCH_ASSOC);
            $sql->closeCursor();

            foreach ($options as $row) {
                $subscriptionTierID = $row['subscription_tier_id'];
                $subscriptionTierName = $row['subscription_tier_name'];
                $subscriptionTierDescription = $row['subscription_tier_description'];
                $orderSequence = $row['order_sequence'];

                $subscriptionTierIDEncrypted = $securityModel->encryptData($subscriptionTierID);

                $response[] = [
                    'CHECK_BOX' => '<div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                        <input class="form-check-input datatable-checkbox-children" type="checkbox" value="'. $subscriptionTierID .'">
                                    </div>',
                    'SUBSCRIPTION_TIER_NAME' => '<div class="d-flex align-items-center">
                                            <div class="user-meta-info">
                                                <h6 class="mb-0">'. $subscriptionTierName .'</h6>
                                                <small class="text-wrap">'. $subscriptionTierDescription .'</small>
                                            </div>
                                        </div>',
                    'ORDER_SEQUENCE' => $orderSequence,
                    'LINK' => $pageLink .'&id='. $subscriptionTierIDEncrypted
                ];
            }

            echo json_encode($response);
        break;
        # -------------------------------------------------------------

        # -------------------------------------------------------------
        case 'subscription tier options':
            $multiple = (isset($_POST['multiple'])) ? filter_input(INPUT_POST, 'multiple', FILTER_VALIDATE_INT) : false;

            $sql = $databaseModel->getConnection()->prepare('CALL generateSubscriptionTierOptions()');
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
                    'id' => $row['subscription_tier_id'],
                    'text' => $row['subscription_tier_name']
                ];
            }

            echo json_encode($response);
        break;
        # -------------------------------------------------------------
    }
}

?>