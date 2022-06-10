<?php

/**
 * @file
 * Contains \Drupal\may\Form\TableForm.
 */

namespace Drupal\may\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides form for the may module.
 */
class TableForm extends FormBase {

  /**
   * {@inheritDoc}
   */
  public function getFormId() {
    return 'table';
  }

  /**
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form["#attached"]["library"][] = "may/global";
    return $form;
  }

  /**
   * {@inheritDoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

  }

    /**
   * {@inheritDoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  }

}