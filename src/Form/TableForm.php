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
   * Primary number of tables.
   *
   * @var int
   */
  protected $tables = 1;

  /**
   * Primary number of rows.
   *
   * @var array
   */
  protected $rows = [1];

  /**
   * Titles for header.
   */
  protected $titles;

  /**
   * Inactive titles for header.
   */
  protected $inactive_titles;

  /**
   * {@inheritDoc}
   */
  public function getFormId() {
    return 'table_form';
  }

  /**
   * Function to build a header.
   */
  protected function buildHeader(): void {
    $this->titles = [
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
    $this->active_titles = [
      'Jan', 'Feb', 'Mar',
      'Apr', 'May', 'Jun',
      'Jul', 'Aug', 'Sep',
      'Oct', 'Nov', 'Dec',
    ];
    $this->inactive_titles = [
      'Year', 'Q1', 'Q2',
      'Q3', 'Q4', 'YTD',
    ];
  }

  /**
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['#prefix'] = '<div id="wrapper">';
    $form['#suffix'] = '</div>';

    $this->buildTable($form, $form_state);

     // Button to add the table.
     $form['add_table'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add table'),
      '#submit' => ['::addTable'],
      '#ajax' => [
        'event' => 'click',
        'callback' => '::ajaxReload',
        'wrapper' => 'wrapper',
        'progress' => [
          'type' => 'none',
        ],
      ],
    ];

    // Button to sending form.
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#name' => 'send',
      '#ajax' => [
        'event' => 'click',
        'callback' => '::ajaxReload',
        'wrapper' => 'wrapper',
        'progress' => [
          'type' => 'none',
        ],
      ],
    ];

    $form['#attached']['library'][] = 'may/global';
    return $form;
  }

  /**
   * Function to build tables.
   */
  public function buildTable(array &$form, FormStateInterface $form_state) {
    $this->buildHeader($form, $form_state);
    for ($t = 0; $t < $this->tables; $t++) {
      // Button to add the year.
      $form["add_year_$t"] = [
        '#type' => 'submit',
        '#value' => $this->t('Add year'),
        '#name' => $t,
        '#submit' => ['::addYear'],
        '#ajax' => [
          'event' => 'click',
          'callback' => '::ajaxReload',
          'wrapper' => 'wrapper',
          'progress' => [
            'type' => 'none',
          ],
        ],
      ];
      // Create a table.
      $form["table_$t"] = [
        '#type' => 'table',
        '#header' => $this->titles,
        '#empty' => t('Nothing found'),
      ];
      // Create rows with fields.
      for ($r = $this->rows[$t]; $r > 0; $r--) {
        // Build titles.
        foreach ($this->titles as $title) {
          $form["table_$t"]["rows_$r"]["$title"] = [
            '#type' => 'number',
          ];
        }
        // Build calculated titles.
        foreach ($this->inactive_titles as $inactive_title) {
          $form["table_$t"]["rows_$r"]["$inactive_title"] = [
            '#type' => 'number',
            '#disabled' => TRUE,
          ];
        }
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
      }
    }
    return $form;
  }

  /**
   * {@inheritDoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    // Validation applies only for submit button.
    if ($form_state->getTriggeringElement()['#name'] !== 'send') {
      return;
    }

    // Checking tables for similarity.
    for ($t = 0; $t < $this->tables; $t++) {
      for ($r = $this->rows[$t]; $r > 0; $r--) {
        $value = $form_state->getValue(["table_$t", "rows_$r"]);
        foreach ($this->titles as $title) {
          if (empty($form_state->getValue(["table_0", "rows_$r"])["$title"]) !== empty($value["$title"])) {
            $form_state->setErrorByName('error', $this->t('Invalid.'));
          }
        }
      }
    }

    // Checking rows for gaps.
    $primary_row = array_search(min($this->rows), $this->rows);
    for ($t = 0; $t < $this->tables; $t++) {
      $isset_value = NULL;
      $isset_empty = NULL;
      for ($r = $this->rows[$t]; $r > 0; $r--) {
        foreach ($form_state->getValue(["table_$t", "rows_$r"]) as $key => $value) {
          if (in_array("$key", $this->active_titles)) {
            if ($r <= $this->rows[$primary_row]) {
              if (!$isset_value && !$isset_empty && !empty($value)) {
                $isset_value = 1;
              }
              if ($isset_value && !$isset_empty && empty($value)) {
                $isset_empty = 1;
              }
              if ($isset_value && $isset_empty && !empty($value)) {
                $form_state->setErrorByName('invalid-table', $this->t('Invalid.'));
              }
            }
          }
        }
      }
    }

  }

  /**
   * {@inheritDoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->hasAnyErrors()) {
      $this->messenger()->addStatus($this->t('Invalid.'));
    }
    else {
      $this->messenger()->addStatus($this->t('Valid.'));
    }
  }

  /**
   * {@inheritDoc}
   *
   * Update page.
   */
  public function ajaxReload(array &$form, FormStateInterface $form_state) {
    return $form;
  }

  /**
   * {@inheritDoc}
   */
  public function addYear(array &$form, FormStateInterface $form_state) {
    // Getting name of button for concrete table.
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
