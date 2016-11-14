<?php

/**
 * Clickable Links plugin.
 *
 * @package Omeka\Plugins\ClickableLinks
 */
class ClickableLinksPlugin extends Omeka_Plugin_AbstractPlugin {

  protected $_hooks = array(
    'initialize',
  );

  /**
   * Connect display filter
   */
  public function hookInitialize() {
    $db = get_db();

    // Add pseudo code filter to all elements
    $sql = "
      SELECT es.name AS el_set, el.name AS el_name
      FROM `$db->Elements` el
      LEFT JOIN `$db->ElementSets` es ON el.element_set_id = es.id
    ";
    $elements = $db->fetchAll($sql);
    foreach($elements as $element) {
        add_filter(
            array("Display", 'Item', $element["el_set"], $element["el_name"]),
            array($this, "filterDisplay")
        );
    }

  }

  /**
  * Filters element text via RegEx and adds <a href="..." target="_blank">...</a> tags
  */
  public function filterDisplay($text, $args) {
    $result = $text;

    // die("<pre>" . print_r($args["element_text"],true) . "</pre>");

    // make sure to leave html fields add_translation_source
    $isHtml = $args["element_text"]["html"];

    if (!$isHtml) {
      // borrowed from http://stackoverflow.com/a/5341330/5394093 ...
      $result = preg_replace(
        '!(((f|ht)tp(s)?://)[-a-zA-Zа-яА-Я()0-9@:%_+.~#?&;//=]+)!i',
        '<a target="_blank" href="$1">$1</a>',
        $result
      );
      // ... Which isn't perfect according to https://mathiasbynens.be/demo/url-regex
      // but which should cover lots of real-life URLs
    }

    return $result;
  }

}
