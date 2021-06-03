<?php

/**
 * Collection of upgrade steps.
 */
class CRM_ctrl_voucher_Upgrader extends CRM_ctrl_voucher_Upgrader_Base {

  // By convention, functions that look like "function upgrade_NNNN()" are
  // upgrade tasks. They are executed in order (like Drupal's hook_update_N).

  /**
   * Example: Run an external SQL script when the module is installed.
   */
  public function install() {
    // @todo review for better path?
    $res = CRM_Core_Resources::singleton();
    $files = glob($res->getPath('be.ctrl.voucher') . '/xml/*_install.xml');
    if (is_array($files)) {
      foreach ($files as $file) {
        $this->executeCustomDataFileByAbsPath($file);
      }
    }
  }
}
