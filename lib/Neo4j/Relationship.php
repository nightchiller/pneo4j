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
 * Neo4j\Relationship
 *
 * @since       1.0
 * @author      Robert Eichholtz <rei@secure-net-concepts.de>
 * @author      onewheelgood <https://github.com/onewheelgood>
 * @internal    this class handles database connections
 */
class Relationship extends Property
{
  /**
   * @var array $_descriptions
   */
  private $_descriptions;

  /**
   * @var bool $_is_new
   */
  private $_is_new;

  /**
   * @var \Neo4j\Database
   */
  private $_db;

  /**
   * @var int $_id
   */
  private $_id;

  /**
   * @var string $_type
   */
  private $_type;

  /**
   * @var \Neo4j\Node
   */
  private $_node1;

  /**
   * @var \Neo4j\Node
   */
  private $_node2;

  /**
   * @param Database $db
   * @param Node $start_node
   * @param Node $end_node
   * @param string $type
   */
  public function __construct(Database $db, Node $start_node, Node $end_node, $type)
  {
    $this->_db     = $db;
    $this->_is_new = true;
    $this->_type   = $type;
    $this->_node1  = $start_node;
    $this->_node2  = $end_node;
  }

  /**
   * @return int
   */
  public function getId()
  {
    return $this->_id;
  }

  /**
   * @return bool
   */
  public function isSaved()
  {
    return !$this->_is_new;
  }

  /**
   * @return string
   */
  public function getType()
  {
    return $this->_type;
  }

  /**
   * @param $type
   * @return bool
   */
  public function isType($type)
  {
    return $this->_type == $type;
  }

  /**
   * @return Node
   */
  public function getStartNode()
  {
    return $this->_node1;
  }

  /**
   * @return Node
   */
  public function getEndNode()
  {
    return $this->_node2;
  }

  /**
   * @param Node $node
   * @return Node
   */
  public function getOtherNode(Node $node)
  {
    return ($this->_node1->getId() == $node->getId()) ? $this->getStartNode() : $this->getEndNode();
  }

  /**
   * @throws \Neo4j\Exception\HttpException
   * @return void
   */
  public function save()
  {
    if($this->_is_new)
    {

      $payload = array(
        'to' => $this->getEndNode()->getUri(),
        'type' => $this->_type,
        'data' => $this->_data
      );

      list($response, $http_code) = Request::post($this->getUri(), $payload);

      if($http_code != 201)
      {
        throw new \Neo4j\Exception\HttpException("http code: " . $http_code . ", response: " . print_r($response, true));
      }
    }
    else
    {
      list($response, $http_code) = Request::put($this->getUri() . '/properties', $this->_data);

      if($http_code != 204)
      {
        throw new \Neo4j\Exception\HttpException("http code: " . $http_code . ", response: " . print_r($response, true));
      }
    }

    if($this->_is_new)
    {
      $path = explode("/", $response['self']);
      $this->_id     = end($path);
      $this->_is_new = false;
    }
  }

  /**
   * delete a relationship
   *
   * @throws \Neo4j\Exception\HttpException
   * @return void
   */
  public function delete()
  {
    if(!$this->_is_new)
    {
      list($response, $http_code) = Request::delete($this->getUri());

      if($http_code != 204)
      {
        throw new \Neo4j\Exception\HttpException("http code: " . $http_code . ", response: " . print_r($response, true));
      }

      $this->_id = null;
      $this->_id_new = true;
    }
  }

  /**
   * @return string
   */
  public function getUri()
  {
    if($this->_is_new)
    {
      $uri = $this->getStartNode()->getUri() . '/relationships';
    }

    else
    {
      $uri = $this->_db->getBaseUri() . 'relationship/' . $this->getId();
    }

    #if (!$this->_is_new) $uri .= '/'.$this->getId();

    return $uri;
  }

  public static function inflateFromResponse($db, $response)
  {
    $start_path = explode("/", $response['start']);
    $end_path   = explode("/", $response['end']);
    $self_path  = explode("/", $response['self']);

    $start    = $db->getNodeById(end($start_path));
    $end      = $db->getNodeById(end($end_path));

    $relationship = new Relationship($db, $start, $end, $response['type']);
    $relationship->_is_new = false;
    $relationship->_id = end($self_path);
    $relationship->setProperties($response['data']);

    return $relationship;
  }

}
