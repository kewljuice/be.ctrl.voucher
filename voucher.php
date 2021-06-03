<?php

require_once 'voucher.civix.php';

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function voucher_civicrm_config(&$config) {
  _voucher_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_xmlMenu
 */
function voucher_civicrm_xmlMenu(&$files) {
  _voucher_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function voucher_civicrm_install() {
  _voucher_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postInstall
 */
function voucher_civicrm_postInstall() {
  _voucher_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_uninstall
 */
function voucher_civicrm_uninstall() {
  _voucher_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function voucher_civicrm_enable() {
  // Get 'Voucher' financial type.
  try {
    $fin_get = civicrm_api3('FinancialType', 'get', [
      'sequential' => 1,
      'name' => "Voucher",
    ]);
  } catch (\CiviCRM_API3_Exception $e) {
    \Civi::log()
      ->debug("voucher_civicrm_install() get: " . $e->getMessage());
  }
  // Create 'Voucher' financial type.
  if ($fin_get['count'] == 0) {
    try {
      $fin_create = civicrm_api3('FinancialType', 'create', [
        'name' => "Voucher",
        'is_active' => 1,
      ]);
      $fin_id = $fin_create['id'];
    } catch (\CiviCRM_API3_Exception $e) {
      \Civi::log()
        ->debug("voucher_civicrm_install() create: " . $e->getMessage());
    }
  }
  else {
    $fin_id = $fin_get['id'];
  }
  // Assign 'Voucher' financial type id.
  $json['financial_id'] = $fin_id;
  // Find 'Custom field' id.
  try {
    $find = civicrm_api3('CustomField', 'get', [
      'sequential' => 1,
      'return' => ["column_name", "id"],
      'column_name' => "discount_id_58",
    ]);
  } catch (\CiviCRM_API3_Exception $e) {
    \Civi::log()
      ->debug("voucher_civicrm_install() find: " . $e->getMessage());
  }
  // Assign 'Custom field' id.
  $json['discount_id'] = $find['id'];
  // Assign 'template' id.
  $json['template_id'] = NULL;
  // Save config.
  $encode = json_encode($json);
  CRM_Core_BAO_Setting::setItem($encode, 'ctrl_voucher', 'voucher-settings');
  // Continue.
  _voucher_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_disable
 */
function voucher_civicrm_disable() {
  _voucher_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_upgrade
 */
function voucher_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _voucher_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
 */
function voucher_civicrm_managed(&$entities) {
  _voucher_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_caseTypes
 */
function voucher_civicrm_caseTypes(&$caseTypes) {
  _voucher_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_angularModules
 */
function voucher_civicrm_angularModules(&$angularModules) {
  _voucher_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterSettingsFolders
 */
function voucher_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _voucher_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_post().
 *
 * @param $op
 * @param $objectName
 * @param $objectId
 * @param $objectRef
 *
 * @throws \CiviCRM_API3_Exception
 */
function voucher_civicrm_post($op, $objectName, $objectId, &$objectRef) {
  // Custom hook for contributions.
  if ($objectName == "Contribution") {
    // Operation: edit.
    if ($op == "edit") {
      // Fetch contribution data.
      $settings = json_decode(CRM_Core_BAO_Setting::getItem('ctrl_voucher', 'voucher-settings'), TRUE);
      $custom_key = 'custom_' . $settings['discount_id'];
      try {
        $contribution = civicrm_api3('Contribution', 'getsingle', [
          'return' => [
            "financial_type_id",
            "contribution_status_id",
            "total_amount",
            "contact_id",
            $custom_key,

          ],
          'id' => $objectId,
        ]);
      } catch (\CiviCRM_API3_Exception $e) {
        \Civi::log()
          ->debug("voucher_civicrm_post() contribution: " . $e->getMessage());
      }
      // Alter only 'Voucher' payments.
      if (isset($contribution['financial_type_id']) && $contribution['financial_type_id'] == $settings['financial_id']) {
        // Alter only completed contributions.
        if (isset($contribution['contribution_status_id']) && $contribution['contribution_status_id'] == 1) {
          // Generate absolute unique 'Voucher' code.
          $key = preg_replace("/[^0-9\s]/", "", constant('CIVICRM_SITE_KEY'));
          $voucher_code = generateVoucherCode($objectId, $key);
          // Check if 'Voucher' exists, if so skip creation.
          try {
            $check = civicrm_api3('DiscountCode', 'get', [
              'sequential' => 1,
              'is_active' => 1,
              'count_use' => ['<' => 1],
              'code' => $voucher_code,
            ]);
          } catch (\CiviCRM_API3_Exception $e) {
            \Civi::log()
              ->debug("voucher_civicrm_post() check: " . $e->getMessage());
          }
          if ($check['count'] == 0) {
            // Create 'Voucher'.
            try {
              $voucher = civicrm_api3('DiscountCode', 'create', [
                'amount' => $contribution['total_amount'],
                'code' => $voucher_code,
                'is_active' => 1,
                'count_max' => 1,
                'description' => "Voucher",
                'amount_type' => 2,
                'memberships' => [1],
                // @todo create settings form or move to HOOK!
                // 'filters' => ['event' => ['event_type_id' => ['IN' => ["8", "9"]]]],
              ]);
            } catch (\CiviCRM_API3_Exception $e) {
              \Civi::log()
                ->debug("voucher_civicrm_post() voucher: " . $e->getMessage());
            }
            if (!$voucher['is_error'] && $voucher['count'] == 1) {
              // Transaction callback function to update custom fields.
              CRM_Core_Transaction::addCallback(CRM_Core_Transaction::PHASE_POST_COMMIT,
                'updateContributionCustomFields', [
                  $objectId,
                  $voucher_code,
                  $custom_key,
                ]);
              // Set voucher variables to session.
              $_SESSION["voucher"]["code"] = $voucher_code;
              // Send Email!
              try {
                $template = $settings['template_id'];
                $email = civicrm_api3('Email', 'send', [
                  'contact_id' => $contribution['contact_id'],
                  'template_id' => $template,
                ]);
              } catch (\CiviCRM_API3_Exception $e) {
                \Civi::log()
                  ->debug("voucher_civicrm_post() email: " . $e->getMessage());
              }
              // Unset session.
              unset($_SESSION["voucher"]);
              // @todo remove logging for production.
              CRM_Core_Session::setStatus($voucher_code, 'Voucher created!', 'success');
            }
          }
        }
      }
    }
  }
}

/**
 * Transaction callback function updateContributionCustomFields().
 */
function updateContributionCustomFields($objectId, $voucher_code, $key) {
  // Update contribution with discount code.
  try {
    $update = civicrm_api3('Contribution', 'create', [
      'id' => $objectId,
      $key => $voucher_code,
    ]);
  } catch (\CiviCRM_API3_Exception $e) {
    \Civi::log()
      ->debug("updateContributionCustomFields() " . $e->getMessage());
  }
}

/**
 * Generate 'Voucher' code.
 *
 * @param integer $id
 * @param integer $key
 *
 * @return string $code
 */
function generateVoucherCode($id, $key) {
  $ten = substr(1000000 + $id, -4) . substr(100 + $id % 97, -2) . substr(100 + $key % 97, -2) . substr(100 + $key % 97, -2);
  $check = substr(100 + $ten % 97, -2);
  if ($check == "00") {
    $check = 97;
  }
  $input = str_split(substr($ten, 3, 4));
  $letters = '';
  foreach ($input as $num) {
    $letters .= chr(64 + $num + 1);
  }
  $code = substr($ten, 0, 3) . $letters . substr($ten, 7, 3) . $check;
  return $code;
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
 */
function voucher_civicrm_entityTypes(&$entityTypes) {
  _voucher_civix_civicrm_entityTypes($entityTypes);
}

/**
 * Implements hook_civicrm_tokens().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_tokens
 */
function voucher_civicrm_tokens(&$tokens) {
  $tokens['voucher'] = [
    'voucher.code' => ts("My voucher code"),
  ];
}

/**
 * Implements hook_civicrm_tokenValues().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_tokenValues
 */
function voucher_civicrm_tokenValues(&$values, $cids, $job = NULL, $tokens = [], $context = NULL) {
  // Voucher tokens.
  if (!empty($tokens['voucher'])) {
    if (isset($_SESSION["voucher"]["code"])) {
      foreach ($cids as $cid) {
        $values[$cid]['voucher.code'] = $_SESSION["voucher"]["code"];
      }
    }
  }
}
