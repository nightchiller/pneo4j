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
 * Neo4j\Path
 *
 * @since       1.0
 * @author      Robert Eichholtz <rei@secure-net-concepts.de>
 * @author      onewheelgood <https://github.com/onewheelgood>
 * @internal    this class handles database connections
 */
class Path
{
  /**
   * @var \Neo4j\Database $_db
   */
  private $_db;

  /**
   * @var string $_endNode
   */
  private $_endNode;

  /**
   * @var string $_startNode
   */
  private $_startNode;

  /**
   * @var int $_length
   */
  private $_length;

  /**
   * @var array $_nodes
   */
  private $_nodes;

  /**
   * @var array $_relationships
   */
  private $_relationships;

  /**
   * @param Database $db
   */
  public function __construct(Database $db)
  {
    $this->_db = $db;
  }

  /**
   * @return int
   */
  public function getLength()
  {
    return $this->_length;
  }

  /**
   * @param int $length
   * @return void
   */
  public function setLength($length)
  {
    $this->_length = $length;
  }

  /**
   * @return string
   */
  public function getEndNode()
  {
    return $this->_endNode;
  }

  /**
   * @param string $node
   * @return void
   */
  public function setEndNode($node)
  {
    $this->_endNode = $node;
  }

  /**
   * @return string
   */
  public function getStartNode()
  {
    return $this->_startNode;
  }

  /**
   * @param string $node
   * @return void
   */
  public function setStartNode($node)
  {
    $this->_startNode = $node;
  }

  /**
   * @return array
   */
  public function getNodes()
  {
    return $this->_nodes;
  }

  /**
   * @param array $nodes
   * @return void
   */
  public function setNodes(array $nodes)
  {
    $this->_nodes = $nodes;
  }

  /**
   * @return array
   */
  public function getRelationships()
  {
    return $this->_relationships;
  }

  /**
   * @param array $relationships
   * @return void
   */
  public function setRelationships(array $relationships)
  {
    $this->_relationships = $relationships;
  }

  /**
   * @static
   * @param $db
   * @param $response
   * @return Path
   */
  public static function inflateFromResponse($db, $response)
  {
    $nodes     = array();
    $relations = array();

    $path = new Path($db);
    $path->setLength($response['length']);
    $path->setStartNode($db->getNodebyUri($response['start']));
    $path->setEndNode($db->getNodebyUri($response['end']));

    foreach($response['nodes'] as $nodeUri)
    {
      $nodes[] = $db->getNodeByUri($nodeUri);
    }

    foreach($response['relationships'] as $relUri) {
      $relations[] = $db->getRelationshipByUri($relUri);
    }

    $path->setNodes($nodes);
    $path->setRelationships($relations);

    return $path;
  }
}
