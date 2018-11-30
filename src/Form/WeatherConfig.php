<?php

namespace Drupal\pdg_weather\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form for storing app details.
 */
class WeatherConfig extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'pdg_weather.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'weather_config';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('pdg_weather.settings');

    $form['appid'] = [
      '#type' => 'textfield',
      '#title' => $this->t('App ID'),
      '#description' => $this->t('Please enter the app id'),
      '#default_value' => $config->get('appid'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Set the submitted configuration setting.
    $this->config('pdg_weather.settings')
      ->set('appid', $form_state->getValue('appid'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
