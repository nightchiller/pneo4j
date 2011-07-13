<?php

/*
 * This file is part of the pneo4j package.
 *
 * (c) Robert Eichholtz <rei@secure-net-concepts.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This file was original forked from https://github.com/prehfeldt/Neo4J-REST-PHP-API-client
 */
namespace Neo4j;

/**
 * Neo4j\Traversal
 *
 * @since       1.0
 * @author      Robert Eichholtz <rei@secure-net-concepts.de>
 * @author      onewheelgood <https://github.com/onewheelgood>
 */
class Traversal
{
  const BREADTH_FIRST = 'breadth_first';
  const DEPTH_FIRST = 'depth_first';

  /**
   * @var \Neo4j\Database $_db
   */
  private $_db;

  /**
   * @var array $_description
   */
  private $_description;

  /**
   * @var string $_order
   */
  private $_order;

  /**
   * @var string $_uniqueness
   */
  private $_uniqueness;

  /**
   * @var array $_relationships
   */
  private $_relationships;

  /**
   * @var array $_pruneEvaluator
   */
  private $_pruneEvaluator;

  /**
   * @var array $_returnFilter
   */
  private $_returnFilter;

  /**
   * @var array $_data
   */
  private $_data;

  /**
   * @var int $_maxDepth
   */
  private $_maxDepth;

  /**
   * @param Database $db
   */
  public function __construct(Database $db)
  {
    $this->_db = $db;
  }

  /**
   * Adds a relationship description.
   *
   * @param string $type
   * @param string|null $direction 'all', 'in' or 'out'
   * @return void
   */
  public function relationships($type, $direction = null)
  {
    if($direction)
    {
      $this->_relationships[] = array('type' => $type, 'direction' => $direction);
    }
    else
    {
      $this->_relationships[] = array('type' => $type);
    }

    $this->_description['relationships'] = $this->_relationships;
  }

  /**
   * set order to breath_first
   *
   * @return void
   */
  public function breadthFirst()
  {
    $this->_order = self::BREADTH_FIRST;
    $this->_description['order'] = $this->_order;
  }

  /**
   * set order to depth_first
   *
   * @return void
   */
  public function depthFirst()
  {
    $this->_order = self::DEPTH_FIRST;
    $this->_description['order'] = $this->_order;
  }

  /**
   * set prune evaluator
   *
   * @param string $language
   * @param string $body
   * @return void
   */
  public function prune($language, $body)
  {
    $this->_pruneEvaluator['language'] = $language;
    $this->_pruneEvaluator['body'] = $body;
    $this->_description['prune evaluator'] = $this->_pruneEvaluator;
  }

  /**
   * set return filter
   * @param string $language
   * @param string $name can be 'all' or 'all_but_start_node'
   * @return void
   */
  public function returnFilter($language = "builtin", $name = "all")
  {
    $this->_returnFilter['language'] = $language;
    $this->_returnFilter['name'] = $name;
    $this->_description['return filter'] = $this->_returnFilter;
  }

  /**
   * set max_depth
   *
   * @param int $depth
   * @return void
   */
  public function maxDepth($depth)
  {
    $this->_maxDepth = $depth;
    $this->_description['max depth'] = $this->_maxDepth;
  }

  /**
   * @magic __invoke
   *
   * @return array
   */
  public function __invoke()
  {
    return $this->_description;
  }

  /**
   * traverse
   *
   * @throws \Neo4j\Exception\HttpException
   * @param $node
   * @param $returnType
   * @return array
   */
  public function traverse($node, $returnType)
  {
    $this->_data = $this->_description;
    $uri = $node->getUri() . '/traverse' . '/' . $returnType;

    list($response, $http_code) = HTTPUtil::jsonPostRequest($uri, $this->_data);

    if($http_code != 200)
    {
      throw new \Neo4j\Exception\HttpException($http_code);
    }

    $objects = array();

    if($returnType == Database::TYPE_NODE)
    {
      $inflateClass = 'Node';
      $inflateFunc = 'inflateFromResponse';
    }
    elseif($returnType == Database::TYPE_RELATIONSHIP)
    {
      $inflateClass = 'Relationship';
      $inflateFunc = 'inflateFromResponse';
    }
    else
    {
      $inflateClass = 'Path';
      $inflateFunc = 'inflateFromResponse';
    }

    foreach($response as $result)
    {
      $objects[] = $inflateClass::$inflateFunc($this->_db, $result);
    }

    return $objects;
  }
}
