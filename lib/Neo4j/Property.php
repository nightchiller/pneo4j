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
 * Neo4j\Property
 *
 * @since       1.0
 * @author      Robert Eichholtz <rei@secure-net-concepts.de>
 * @author      onewheelgood <https://github.com/onewheelgood>
 * @internal    this class handles database connections
 */
class Property
{
  /**
   * @var array
   */
  protected $_data;

  /**
   * set property
   *
   * @param string $key
   * @param mixed $value
   * @return void
   */
  public function __set($key, $value)
  {
    if($value === null && isset($this->_data[$key]))
    {
      unset($this->_data[$key]);
    }
    else
    {
      $this->_data[$key] = $value;
    }
  }

  /**
   * get property
   *
   * @param string $key
   * @return null
   */
  public function __get($key)
  {
    if(isset($this->_data[$key]))
    {
      return $this->_data[$key];
    }
    else
    {
      return null;
    }
  }

  /**
   * @param array $data
   * @return void
   */
  public function setProperties(array $data)
  {
    $this->_data = $data;
  }

  /**
   * @return array
   */
  public function getProperties()
  {
    return $this->_data;
  }
}
