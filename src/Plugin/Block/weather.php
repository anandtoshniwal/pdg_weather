<?php

namespace Drupal\pdg_weather\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\pdg_weather\WeatherInfo;

/**
 * Provides a 'weather_info' block.
 *
 * @Block(
 *   id = "weather_info",
 *   admin_label = @Translation("Weather info"),
 *   category = @Translation("Info block")
 * )
 */
class weather extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The weather info service.
   *
   * @var \Drupal\pdg_weather\WeatherInfo
   */
  protected $weatherInfo;

  use StringTranslationTrait;

  /**
   * Constructs a new SwitchUserBlock object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param $weatherInfo
   *   The weather info service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, WeatherInfo $weatherInfo) {
    // If you're extending a core plugin class, call its constructor.
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->weatherInfo = $weatherInfo;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('pdg_weather.weather_info')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {

    $form['city_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Enter City name'),
      '#size' => 60,
      '#description' => $this->t('City name to fetch weather information.'),
      '#default_value' => $this->configuration['city_name'],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {

    $this->configuration['weatherInfo_data'] = $form_state->getValue('city_name');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    $response = $this->weatherInfo->weatherData($this->configuration['weatherInfo_data']);
    if (!empty($response)) {
      return [
        '#type' => 'markup',
        '#markup' => '<div class="block-weather-ifo">
                        <h2 class="weather-info-city">City:' . $response['city'] . '</h2>
                        <ul style="list-style: none;">
                            <li class="weather-temp">Temprature:' . $response['temp'] .'</li>
                            <li class="weather-pressure">Pressure:' . $response['pressure'] . '</li>
                            <li class="weather-humidity">Humidity:' . $response['humidity'] . '</li>
                            <li class="weather-temp-min">Temp min:' . $response['temp_min'] . '</li>
                            <li class="weather-temp-max">Temp max:' . $response['temp_max'] . '</li>
                            <li class="weather-wind-speed">Wind speed:' . $response['wind_speed'] . '</li>
                        </ul>
                    </div>',
      ];
    }
  }

}
