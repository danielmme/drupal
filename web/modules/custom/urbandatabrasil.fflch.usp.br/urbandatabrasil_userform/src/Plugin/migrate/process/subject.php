<?php

namespace Drupal\urbandatabrasil_userform\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
use Drupal\taxonomy\Entity\Term;

/**
 * Provides a 'subject' migrate process plugin.
 *
 * @MigrateProcessPlugin(
 *  id = "trans_subject"
 * )
 */
class subject extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {

    $vocabulary = $this->configuration['vocabulary'];
  
    $pattern = '/\^a /';
    $replacement = '';
    $tid = null;

    $term_name = preg_replace($pattern, $replacement, $value);
   
    if($term_name) {
      if (!$this->getTidByName($term_name, $vocabulary)) {
        $term = Term::create([
          'name' => $term_name, 
          'vid'  => $vocabulary,
        ])->save();
      }
      $tid =  $this->getTidByName($term_name, $vocabulary);
      $result = ['target_id' => $tid];
    }

  return $result;
  }

  /**
   * Load term by name.
   */
  protected function getTidByName($name = NULL, $vocabulary = NULL) {
    $tid = null;
    $properties = [];
    if (!empty($name) && !empty($vocabulary)) {
      $properties['name'] = $name;
      $properties['vid'] = $vocabulary;
      $terms = \Drupal::entityManager()->getStorage('taxonomy_term')->loadByProperties($properties);
      $term = reset($terms);
      $tid = is_object($term) ? $term->id() : null;
    }
    return $tid;
  }

}
