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
    $this->inactive_titles = [
      'Year',
      'Q1',
      'Q2',
      'Q3',
      'Q4',
      'YTD',
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
          if (empty($form_state->getValue(['table_0', "rows_$r"])["$title"]) !== empty($value["$title"])) {
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
          if (!in_array("$key", $this->inactive_titles)) {
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
    $this->messenger()->addStatus($this->t('Valid.'));
    for ($t = 0; $t < $this->tables; $t++) {
      for ($r = 1; $r <= $this->rows[$t]; $r++) {
        $value = $form_state->getValue(["table_$t", "rows_$r"]);

        // Calculation values for quarters.
        if (empty($value['Jan']) && empty($value['Feb']) && empty($value['Mar'])) {
          $q1 = 0;
        }
        else {
          $q1 = round(($value['Jan'] + $value['Feb'] + $value['Mar'] + 1) / 3, 2);
        }
        if (empty($value['Apr']) && empty($value['May']) && empty($value['Jun'])) {
          $q2 = 0;
        }
        else {
          $q2 = round(($value['Apr'] + $value['May'] + $value['Jun'] + 1) / 3, 2);
        }
        if (empty($value['Jul']) && empty($value['Aug']) && empty($value['Sep'])) {
          $q3= 0;
        }
        else {
          $q3 = round(($value['Jul'] + $value['Aug'] + $value['Sep'] + 1) / 3, 2);
        }
        if (empty($value['Oct']) && empty($value['Nov']) && empty($value['Dec'])) {
          $q4 = 0;
        }
        else {
          $q4 = round(($value['Oct'] + $value['Nov'] + $value['Dec'] + 1) / 3, 2);
        }
        $form["table_$t"]["rows_$r"]['Q1']['#value'] = $q1;
        $form["table_$t"]["rows_$r"]['Q2']['#value'] = $q2;
        $form["table_$t"]["rows_$r"]['Q3']['#value'] = $q3;
        $form["table_$t"]["rows_$r"]['Q4']['#value'] = $q4;

        // Calculation value for ytd.
        $ytd = round(($q1 + $q2 + $q3 +$q4 + 1) / 4, 2);
        $form["table_$t"]["rows_$r"]['YTD']['#value'] = $ytd;
      }
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
