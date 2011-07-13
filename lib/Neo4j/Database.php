<?php

/*
 * This file is part of the neo4jBundle.
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
 * Neo4j\Graph\Database
 *
 * @since       1.0
 * @author      Robert Eichholtz <rei@secure-net-concepts.de>
 * @author      onewheelgood <https://github.com/onewheelgood>
 * @internal    this class handles database connections
 */
class Database
{
  /**
   * An entity is in MANAGED state when its persistence is managed by an EntityManager.
   */
	const RELATIONSHIP_GLOBAL = 'relationship global';

  /**
   * An entity is in MANAGED state when its persistence is managed by an EntityManager.
   */
	const RELATIONSHIP_PATH = 'relationship path';

  /**
   * An entity is in MANAGED state when its persistence is managed by an EntityManager.
   */
	const RELATIONSHIP_RECENT = 'relationship recent';

  /**
   * An entity is in MANAGED state when its persistence is managed by an EntityManager.
   */
	const NODE_GLOBAL = 'node global';

  /**
   * An entity is in MANAGED state when its persistence is managed by an EntityManager.
   */
	const NODE_PATH = 'node path';

  /**
   * An entity is in MANAGED state when its persistence is managed by an EntityManager.
   */
	const NODE_RECENT = 'node recent';

  /**
   * An entity is in MANAGED state when its persistence is managed by an EntityManager.
   */
	const NODE_NONE = 'none';

  /**
   * An entity is in MANAGED state when its persistence is managed by an EntityManager.
   */
  const DIRECTION_BOTH = 'all';

  /**
   * An entity is in MANAGED state when its persistence is managed by an EntityManager.
   */
	const DIRECTION_INCOMING = 'in';

  /**
   * An entity is in MANAGED state when its persistence is managed by an EntityManager.
   */
	const DIRECTION_OUTGOING = 'out';

  /**
   * An entity is in MANAGED state when its persistence is managed by an EntityManager.
   */
  const TYPE_NODE = 'node';

  /**
   * An entity is in MANAGED state when its persistence is managed by an EntityManager.
   */
	const TYPE_RELATIONSHIP = 'relationship';

  /**
   * An entity is in MANAGED state when its persistence is managed by an EntityManager.
   */
	const TYPE_PATH = 'path';

  /**
   * @var string
   */
  private $base_uri;

  /**
   * @param string $base_uri
   */
  public function __construct($base_uri)
  {
    $this->base_uri = $base_uri;
  }

  /**
   * get the node by uri
   *
   * @throws \Neo4j\Exception\HttpException
   * @throws \Neo4j\Exception\NotFoundException
   * @param string $uri The Request URI
   * @return Neo4j\Node
   */
  public function getNodeByUri($uri)
  {
    list($response, $http_code) = Request::get($uri);

    if($http_code == 404)
    {
      throw new \Neo4j\Exception\NotFoundException;
    }

    if($http_code != 200)
    {
      throw new \Neo4j\Exception\HttpException("http code: " . $http_code . ", response: " . print_r($response, true));
    }

    return Node::inflateFromResponse($this, $response);
  }

  /**
   * get the node by id
   *
   * @param int $node_id The Node Id
   * @return Neo4j\Node
   */
  public function getNodeById($node_id)
  {
    $uri = $this->base_uri . 'node/' . $node_id;

    return $this->getNodeByUri($uri);
  }

  /**
   * get relationship by id
   *
   * @param int $relationship_id
   * @return Neo4j\Relationship
   */
  public function getRelationshipById($relationship_id)
  {
    $uri = $this->base_uri . 'relationship/' . $relationship_id;

    return $this->getRelationshipByUri($uri);
  }

  /**
   * get relationship by uri
   *
   * @throws \Neo4j\Exception\NotFoundException
   * @throws \Neo4j\Exception\HttpException
   * @param string $uri
   * @return Neo4j\Relationship
   */
  public function getRelationshipByUri($uri)
  {
    list($response, $http_code) = Request::get($uri);

    if($http_code == 404)
    {
      throw new \Neo4j\Exception\NotFoundException;
    }

    if($http_code != 200)
    {
      throw new \Neo4j\Exception\HttpException("http code: " . $http_code . ", response: " . print_r($response, true));
    }

    return Relationship::inflateFromResponse($this, $response);
  }

  /**
   * create a new node
   *
   * @return \Neo4j\Node
   */
  public function createNode()
  {
    return new Node($this);
  }

  /**
   * create a empty node
   * @return Node
   */
  public function createEmptyNode()
  {
    $node = new Node($this);
    return $node->save();
  }


  public function getRoot()
  {
    list($response, $http_code) = Request::get($this->base_uri);

    if($http_code == 404)
    {
      throw new \Neo4j\Exception\NotFoundException;
    }

    if($http_code != 200)
    {
      throw new \Neo4j\Exception\HttpException("http code: " . $http_code . ", response: " . print_r($response, true));
    }

    return $response;
  }

  /**
   * get base uri
   *
   * @return string
   */
  public function getBaseUri()
  {
    return $this->base_uri;
  }
}
