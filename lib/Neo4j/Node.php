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
 * Neo4j\Node
 *
 * @since       1.0
 * @author      Robert Eichholtz <rei@secure-net-concepts.de>
 * @author      onewheelgood <https://github.com/onewheelgood>
 * @internal    this class handles database connections
 */
class Node extends Property
{
  /**
   * @var Database
   */
  private $_db;

  /**
   * @var int $_id
   */
  private $_id;

  /**
   * @var bool $_is_new
   */
  private $_is_new;

  /**
   * @var array $_pathFinderData
   */
  private $_pathFinderData;

  /**
   * @param Database $db
   */
  public function __construct(Database $db)
  {
    $this->_db = $db;
    $this->_is_new = true;
  }

  /**
   * delete current node
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
   * save current node
   *
   * @throws \Neo4j\Exception\HttpException
   * @return void
   */
  public function save()
  {
    if($this->_is_new)
    {
      list($response, $http_code) = Request::post($this->getUri(), $this->_data);

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
   * get node id
   * @return int
   */
  public function getId()
  {
    return $this->_id;
  }

  /**
   * check if node is saved
   * @return bool
   */
  public function isSaved()
  {
    return !$this->_is_new;
  }

  /**
   * get relationships of node and filtered by directions and types
   *
   * @param string $direction
   * @param array|null $types
   * @return array
   */
  public function getRelationships($direction = Database::DIRECTION_BOTH, $types = NULL)
  {
    $uri = $this->getUri() . '/relationships';

    switch($direction)
    {
      case Database::DIRECTION_INCOMING:
        $uri .= '/' . Database::DIRECTION_INCOMING;
        break;
      case Database::DIRECTION_OUTGOING:
        $uri .= '/' . Database::DIRECTION_OUTGOING;
        break;
      default:
        $uri .= '/' . Database::DIRECTION_BOTH;
    }

    if($types)
    {
      if(is_array($types))
      {
        $types = implode("&", $types);
      }

      $uri .= '/' . $types;
    }

    list($response, $http_code) = Request::get($uri);

    $relationships = array();

    foreach($response as $result)
    {
      $relationships[] = Relationship::inflateFromResponse($this->_db, $result);
    }

    return $relationships;
  }

  /**
   * create new relationship to other node
   *
   * @param Node $node
   * @param string $type
   * @return Relationship
   */
  public function createRelationshipTo(Node $node, $type)
  {
    $relationship = new Relationship($this->_db, $this, $node, $type);
    return $relationship;
  }

  /**
   * get uri
   *
   * @return string
   */
  public function getUri()
  {
    $uri = $this->_db->getBaseUri() . 'node';

    if(!$this->_is_new)
    {
      $uri .= '/' . $this->getId();
    }

    return $uri;
  }

  /**
   * @static
   * @param Database $db
   * @param array $response
   * @return Node
   */
  public static function inflateFromResponse($db, $response)
  {
    $self_path  = explode("/", $response['self']);

    $node = new Node($db);
    $node->_is_new = false;
    $node->_id = end($self_path);
    $node->setProperties($response['data']);

    return $node;
  }

  /**
   * find path between to nodes
   *
   * @todo Add handling for relationships
   * @todo Add algorithm parameter
   *
   * @example curl -H Accept:application/json -H Content-Type:application/json -d '{ "to": "http://localhost:9999/node/3" }' -X POST http://localhost:9999/node/1/pathfinder
   *
   * @throws \Neo4j\Exception\HttpException
   * @throws \Neo4j\Exception\NotFoundException
   *
   * @param Node $toNode
   * @param int|null $maxDepth
   * @param Relationships|null $relationships
   * @param string|null $singlePath
   *
   * @return array
   */
  public function findPaths(Node $toNode, $maxDepth = null, Relationships $relationships = null, $singlePath = null)
  {
    $this->_pathFinderData['to'] = $this->_db->getBaseUri() . 'node' . '/' . $toNode->getId();

    if($maxDepth)
    {
      $this->_pathFinderData['max depth'] = $maxDepth;
    }

    if($singlePath)
    {
      $this->_pathFinderData['single path'] = $singlePath;
    }

    if($relationships)
    {
      $this->_pathFinderData['relationships'] = $relationships->get();
    }

    list($response, $http_code) = Request::post($this->getUri() . '/pathfinder', $this->_pathFinderData);

    if($http_code == 404)
    {
      throw new \Neo4j\Exception\NotFoundException;
    }

    if($http_code != 200)
    {
      throw new \Neo4j\Exception\HttpException("http code: " . $http_code . ", response: " . print_r($response, true));
    }

    $paths = array();
    foreach($response as $result)
    {
      $paths[] = Path::inflateFromResponse($this->_db, $result);
    }

    if(empty($paths))
    {
      throw new \Neo4j\Exception\NotFoundException();
    }

    return $paths;
  }

  /**
   * Convenience method just returns the first path
   *
   * @param Node $toNode
   * @param null $maxDepth
   * @param Relationships|null $relationships
   * @return
   */
  public function findPath(Node $toNode, $maxDepth = null, Relationships $relationships = null)
  {
    $paths = $this->findPaths($toNode, $maxDepth, $relationships, 'true');
    return $paths[0];
  }

}
