<?php

use CRM_ctrl_voucher_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://docs.civicrm.org/dev/en/latest/framework/quickform/
 */
class CRM_ctrl_voucher_Form_VoucherConfig extends CRM_Core_Form {

  public function buildQuickForm() {
    // Set default values.
    $defaults = CRM_Core_BAO_Setting::getItem('ctrl_voucher', 'voucher-settings');
    $this->setDefaults(['settings' => $defaults]);
    // Fields.
    $attributes = ['rows' => '5', 'cols' => '75'];
    $this->add(
      'textarea',
      'settings',
      'Object',
      $attributes
    );
    // Buttons.
    $this->addButtons([
      [
        'type' => 'submit',
        'name' => E::ts('Submit'),
        'isDefault' => TRUE,
      ],
    ]);
    // Export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  /**
   * {@inheritdoc}
   */
  public function postProcess() {
    $values = $this->controller->exportValues($this->_name);
    $config = $values['settings'];
    CRM_Core_BAO_Setting::setItem($config, 'ctrl_voucher', 'voucher-settings');
    parent::postProcess();
  }

  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  public function getRenderableElementNames() {
    // The _elements list includes some items which should not be
    // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
    // items don't have labels.  We'll identify renderable by filtering on
    // the 'label'.
    $elementNames = [];
    foreach ($this->_elements as $element) {
      /** @var HTML_QuickForm_Element $element */
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }
}
