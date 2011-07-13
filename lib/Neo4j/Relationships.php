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
 * Neo4j\Relationships
 *
 * @since       1.0
 * @author      Robert Eichholtz <rei@secure-net-concepts.de>
 * @author      onewheelgood <https://github.com/onewheelgood>
 * @internal    this class handles add and get descriptions of a Relationships
 */
class Relationships
{
  /**
   * @var array
   */
  private $_descriptions;

  /**
   * add new node description
   *
   * @param string $type 'node', 'relationship' or 'path'
   * @param string|null $direction 'all', 'in' or 'out'
   * @return void
   */
  function __construct($type, $direction = null)
  {
    if ($direction)
    {
      $this->_descriptions[] = array('type' => $type, 'direction' => $direction);
    }
    else
    {
      $this->_descriptions[] = array('type' => $type);
    }
  }

  /**
   * add new node description
   *
   * @param string $type 'node', 'relationship' or 'path'
   * @param string|null $direction 'all', 'in' or 'out'
   * @return void
   */
  function add($type, $direction = null)
  {
    if ($direction)
    {
      $this->_descriptions[] = array('type' => $type, 'direction' => $direction);
    }
    else
    {
      $this->_descriptions[] = array('type' => $type);
    }
  }

  /**
   * get relationship descriptions
   *
   * @return array
   */
  function get()
  {
    return $this->_descriptions;
  }
}
