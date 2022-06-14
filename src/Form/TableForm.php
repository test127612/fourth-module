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
    return 'table_form';
  }

  /**
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['add_year'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add Year'),
    ];

    $header = [
      'year' => t('Year'),
      'jan' => t('Jan'),
      'feb' => t('Feb'),
      'mar' => t('Mar'),
      'q1' => t('Q1'),
      'apr' => t('Apr'),
      'may' => t('May'),
      'jun' => t('Jun'),
      'q2' => t('Q2'),
      'jul' => t('Jul'),
      'aug' => t('Aug'),
      'sep' => t('Sep'),
      'q3' => t('Q3'),
      'oct' => t('Oct'),
      'nov' => t('Nov'),
      'dec' => t('Dec'),
      'q4' => t('Q4'),
      'ytd' => t('YTD'),
    ];

    $form['table'] = [
      '#type' => 'tableselect',
      '#header' => $header,
      '#empty' => t('Nothing found'),
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    $form['add_table'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add Table'),
    ];

    $form['#attached']['library'][] = 'may/global';

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