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
   * Number of tables.
   *
   * @var int
   */
  protected $tables = 1;

  /**
   * Number of rows.
   *
   * @var array
   */
  protected $rows = [1];

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
    
    // Header for table.
    $header = [
      'year' => $this->t('Year'),
      'jan' => $this->t('Jan'),
      'feb' => $this->t('Feb'),
      'mar' => $this->t('Mar'),
      'q1' => $this->t('Q1'),
      'apr' => $this->t('Apr'),
      'may' => $this->t('May'),
      'jun' => $this->t('Jun'),
      'q2' => $this->t('Q2'),
      'jul' => $this->t('Jul'),
      'aug' => $this->t('Aug'),
      'sep' => $this->t('Sep'),
      'q3' => $this->t('Q3'),
      'oct' => $this->t('Oct'),
      'nov' => $this->t('Nov'),
      'dec' => $this->t('Dec'),
      'q4' => $this->t('Q4'),
      'ytd' => $this->t('YTD'),
    ];

    for ($t = 0; $t < $this->tables; $t++) {

      // Button to add the year.
      $form["add_year_$t"] = [
        '#type' => 'submit',
        '#value' => $this->t('Add Year'),
        '#name' => $t,
        '#submit' => ['::addYear'],
      ];

      // Create a table.
      $form["table_$t"] = [
        '#type' => 'table',
        '#header' => $header,
        '#empty' => t('Nothing found'),
      ];

      // Create rows with fields.
      for ($r = $this->rows[$t]; $r > 0; $r--) {
        foreach ($header as $header_item) {
          if ($r == 1) {
            $form["table_$t"]["rows_$r"]['Year'] = [
              '#type' => 'number',
              '#disabled' => TRUE,
              '#default_value' => date('Y'),
            ];
          }
          else {
            $form["table_$t"]["rows_$r"]['Year'] = [
              '#type' => 'number',
              '#disabled' => TRUE,
              '#default_value' => date('Y') - $r + 1,
            ];
          }
          
          $form["table_$t"]["rows_$r"]["$header_item"] = [
            '#type' => 'number',
          ];

          if (in_array("$header_item", ['Q1', 'Q2', 'Q3', 'Q4', 'YTD'])) {
            $form["table_$t"]["rows_$r"]["$header_item"] = [
              '#type' => 'number',
              '#disabled' => TRUE,
            ];
          }
        }
      }
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
    $t = $form_state->getTriggeringElement()['#name'];

    // Increase by 1 the number of rows.
    $this->rows[$t]++;

    // Rebuild form with 1 extra row.
    $form_state->setRebuild();
    return $form;
  }

  /**
   * {@inheritDoc}
   */
  public function addTable(array &$form, FormStateInterface $form_state) {

    // Increase by 1 the number of tables.
    $this->tables++;

    // Default number of rows for new table.
    $this->rows[] = 1;

    // Rebuild form with 1 extra table.
    $form_state->setRebuild();
    return $form;
  }

}
