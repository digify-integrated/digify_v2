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
        case 'subscriber table':
            $filterSubscriptionTier = isset($_POST['subscription_tier_filter']) && is_array($_POST['subscription_tier_filter']) 
            ? "'" . implode("','", array_map('trim', $_POST['subscription_tier_filter'])) . "'" 
            : null;
            $filterBillingCycle = isset($_POST['billing_cycle_filter']) && is_array($_POST['billing_cycle_filter']) 
            ? "'" . implode("','", array_map('trim', $_POST['billing_cycle_filter'])) . "'" 
            : null;

            $sql = $databaseModel->getConnection()->prepare('CALL generateSubscriberTable(:filterSubscriptionTier, :filterBillingCycle)');
            $sql->bindValue(':filterSubscriptionTier', $filterSubscriptionTier, PDO::PARAM_STR);
            $sql->bindValue(':filterBillingCycle', $filterBillingCycle, PDO::PARAM_STR);
            $sql->execute();
            $options = $sql->fetchAll(PDO::FETCH_ASSOC);
            $sql->closeCursor();

            foreach ($options as $row) {
                $subscriberID = $row['subscriber_id'];
                $subscriberName = $row['subscriber_name'];
                $companyName = $row['company_name'];
                $phone = $row['phone'];
                $email = $row['email'];
                $subscriberStatus = $row['subscriber_status'];
                $subscriptionTierName = $row['subscription_tier_name'];
                $billingCycleName = $row['billing_cycle_name'];

                $badgeClass = $subscriberStatus == 'Active' ? 'badge-light-success' : 'badge-light-danger';
                $subscriberStatusBadge = '<div class="badge ' . $badgeClass . ' fw-bold">' . $subscriberStatus . '</div>';

                $subscriberIDEncrypted = $securityModel->encryptData($subscriberID);

                $response[] = [
                    'CHECK_BOX' => '<div class="form-check form-check-sm form-check-custom form-check-solid me-3">
                                        <input class="form-check-input datatable-checkbox-children" type="checkbox" value="'. $subscriberID .'">
                                    </div>',
                    'SUBSCRIBER' => '<div class="d-flex align-items-center">
                                        <div class="d-flex flex-column">
                                            <span class="text-gray-800 fw-bold mb-1">'. $subscriberName .'</span>
                                            <small class="text-gray-600">'. $companyName .'</small>
                                        </div>
                                    </div>',
                    'PHONE' => $phone,
                    'EMAIL' => $email,
                    'SUBSCRIPTION_TIER' => $subscriptionTierName,
                    'BILLING_CYCLE' => $billingCycleName,
                    'SUBSCRIBER_STATUS' => $subscriberStatusBadge,
                    'LINK' => $pageLink .'&id='. $subscriberIDEncrypted
                ];
            }

            echo json_encode($response);
        break;
        # -------------------------------------------------------------

        # -------------------------------------------------------------
        case 'subscriber options':
            $multiple = (isset($_POST['multiple'])) ? filter_input(INPUT_POST, 'multiple', FILTER_VALIDATE_INT) : false;

            $sql = $databaseModel->getConnection()->prepare('CALL generateSubscriberOptions()');
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
                    'id' => $row['subscriber_id'],
                    'text' => $row['subscriber_name']
                ];
            }

            echo json_encode($response);
        break;
        # -------------------------------------------------------------
    }
}

?>