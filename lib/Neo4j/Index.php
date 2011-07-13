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
 * Neo4j\Index
 *
 * @since       1.0
 * @author      Robert Eichholtz <rei@secure-net-concepts.de>
 * @author      onewheelgood <https://github.com/onewheelgood>
 * @internal    this class handles database connections
 */
class Index
{
  /**
   * @var \Neo4j\Database
   */
  private $_db;

  /**
   * @var string $_uri
   */
  private $_uri;

  /**
   * @var string $_data
   */
  private $_data;

  /**
   * @param \Neo4j\Database $db
   */
  public function __construct(Database $db)
  {
    $this->_db = $db;
  }

  /**
   * create new node index
   *
   * @throws \Neo4j\Exception\HttpException
   *
   * @param \Neo4j\Node $node
   * @param string $key
   * @param string $value
   *
   * @return void
   */
  public function index(Node $node, $key, $value)
  {
    $this->_uri = $this->_db->getBaseUri() . 'index/node/' . $key . '/' . $value;
    $this->_data = $node->getUri();

    list($response, $http_code) = Request::post($this->_uri, $this->_data);

    if($http_code != 201)
    {
      throw new \Neo4j\Exception\HttpException($http_code);
    }

  }

  /**
   * remove indexed node
   *
   * @throws \Neo4j\Exception\HttpException
   *
   * @param \Neo4j\Node $node
   * @param string $key
   * @param string $value
   *
   * @return void
   */
  public function remove(Node $node, $key, $value)
  {
    $this->_uri = $this->_db->getBaseUri() . 'index/node/' . $key . '/' . $value . '/' . $node->getId();

    list($response, $http_code) = Request::delete($this->_uri);

    if($http_code != 204)
    {
      throw new \Neo4j\Exception\HttpException($http_code);
    }
  }

  /**
   * get nodex by key value pair
   *
   * @throws \Neo4j\Exception\HttpException
   * @throws \Neo4j\Exception\NotFoundException
   *
   * @param string $key
   * @param string $value
   *
   * @return array
   */
  public function getNodes($key, $value)
  {
    $nodes = array();
    $this->_uri = $this->_db->getBaseUri() . 'index/node/' . $key . '/' . $value;

    list($response, $http_code) = Request::get($this->_uri);

    if($http_code != 200)
    {
      throw new \Neo4j\Exception\HttpException("http code: " . $http_code . ", response: " . print_r($response, true));
    }

    foreach($response as $nodeData)
    {
      $nodes[] = Node::inflateFromResponse($this->_db, $nodeData);
    }

    if(empty($nodes))
    {
      throw new \Neo4j\Exception\NotFoundException();
    }

    return $nodes;
  }

  /**
   * get first node by key value pair
   *
   * A hack for now.  The REST API doesn't offer an implementation of
   * org.neo4j.index.IndexServe.getSingleNode();
   * So we just get the first element in the returned array.
   *
   * @param string $key
   * @param string $value
   *
   * @return \Neo4j\Node
   */
  public function getNode($key, $value)
  {
    $nodes = $this->getNodes($key, $value);
    return $nodes[0];
  }
}
