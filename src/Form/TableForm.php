<?php

/**
 * @file
 * Contains \Drupal\may\Form\TableForm.
 */

namespace Drupal\may\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\MessageCommand;

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
    
    // Renderable array using Form API.
    $form['messages'] = [
      '#markup' => '<div class="status-message"></div>',
      '#weight' => -100,
    ];

    // Get the number of rows (default = 1).
    $number_of_rows = $form_state->get('number_of_rows');
    if (empty($number_of_rows)) {
      $number_of_rows = 1;
      $form_state->set('number_of_rows', $number_of_rows);
    }

    // Button to add the year.
    $form['add_year'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add Year'),
      '#submit' => ['::addYear'],
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

    // Create a table.
    $form['table'] = [
      '#type' => 'table',
      '#header' => $header,
      '#empty' => t('Nothing found'),
    ];

    // Create number_of_rows according to $number_of_rows.
    for ($i=1; $i<=$number_of_rows; $i++) {
      $form['table'][$i]['year'] = [
        '#type' => 'number',
      ];
      $form['table'][$i]['jan'] = [
        '#type' => 'number',
      ];
      $form['table'][$i]['feb'] = [
        '#type' => 'number',
      ];
      $form['table'][$i]['mar'] = [
        '#type' => 'number',
      ];
      $form['table'][$i]['q1'] = [
        '#type' => 'number',
      ];
      $form['table'][$i]['apr'] = [
        '#type' => 'number',
      ];
      $form['table'][$i]['may'] = [
        '#type' => 'number',
      ];
      $form['table'][$i]['jun'] = [
        '#type' => 'number',
      ];
      $form['table'][$i]['q2'] = [
        '#type' => 'number',
      ];
      $form['table'][$i]['jul'] = [
        '#type' => 'number',
      ];
      $form['table'][$i]['aug'] = [
        '#type' => 'number',
      ];
      $form['table'][$i]['sep'] = [
        '#type' => 'number',
      ];
      $form['table'][$i]['q3'] = [
        '#type' => 'number',
      ];
      $form['table'][$i]['oct'] = [
        '#type' => 'number',
      ];
      $form['table'][$i]['nov'] = [
        '#type' => 'number',
      ];
      $form['table'][$i]['dec'] = [
        '#type' => 'number',
      ];
      $form['table'][$i]['q4'] = [
        '#type' => 'number',
      ];
      $form['table'][$i]['ytd'] = [
        '#type' => 'number',
      ];
    }
 
    // Button to sending form.
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#ajax'=> [
        'callback' => '::submitForm',
        'event' => 'click',
        'progress' => [
          'type' => 'throbber',
        ],
      ],
    ];

    // Button to add the table.
    $form['add_table'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add Table'),
      '#submit' => ['::addTable'],
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
    $response = new AjaxResponse();
    if ($form_state->hasAnyErrors()) {
      $response->addCommand(new MessageCommand($this->t('Form is not valid.'), '.status-message', ['type' => 'error']));
    }
    else {
      $response->addCommand(new MessageCommand($this->t('Form is valid.'), '.status-message', ['type' => 'status']));
    }
    return $response;
  }

  /**
   * {@inheritDoc}
   */
  public function addYear(array &$form, FormStateInterface $form_state) {

    // Increase by 1 the number of rows.
    $number_of_rows = $form_state->get('number_of_rows');
    $number_of_rows++;
    $form_state->set('number_of_rows', $number_of_rows);

    // Rebuild form with 1 extra row.
    $form_state->setRebuild();
  }

  /**
   * {@inheritDoc}
   */
  public function addTable(array &$form, FormStateInterface $form_state) {

  }

}
