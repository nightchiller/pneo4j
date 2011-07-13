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
 * Neo4j\Request
 *
 * @since       1.0
 * @author      Robert Eichholtz <rei@secure-net-concepts.de>
 * @author      onewheelgood <https://github.com/onewheelgood>
 * @internal    this class handles add and get descriptions of a Relationships
 */
class Request
{
  const GET    = 'GET';
	const POST   = 'POST';
	const PUT    = 'PUT';
	const DELETE = 'DELETE';

  /**
   * A general purpose HTTP request method
   *
   * @todo Batch jobs are overloading the local server so try twice, with a pause in the middle
   *
   * @static
   * @throws \Neo4j\Exception\CurlException
   * @param string $url
   * @param string $method
   * @param string $post_data
   * @param string $content_type
   * @param string $accept_type
   *
   * @return array
   */
	private static function connect($url, $method='GET', $post_data='', $content_type='', $accept_type='')
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

		if ($post_data)
		{
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Length: '.strlen($post_data),'Content-Type: '.$content_type,'Accept: '.$accept_type));
		}

		$count = 6;
		do {
			$count--;
			$response = curl_exec($ch);
			$error = curl_error($ch);

			if ($error != '')
      {
				echo "Curl got an error, sleeping for a moment before retrying: $count\n";
				sleep(10);
				$found_error = true;
			}
      else
      {
				$found_error = false;
			}

		} while ($count && $found_error);

		if ($error != '')
    {
			throw new \Neo4j\Exception\CurlException($error);
		}

		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		curl_close($ch);

		return array($response, $http_code);
	}

  /**
   * A HTTP request that returns json and optionally sends a json payload (post only)
   *
   * @static
   * @param string $url
   * @param string $method
   * @param array|null $data
   *
   * @return array
   */
	public static function request($url, $method, array $data = null)
	{
		$json      = json_encode($data);
		$result    = self::connect($url, $method, $json, 'application/json', 'application/json');
		$result[0] = json_decode($result[0], TRUE);

		return $result;
	}

  /**
   * send a put request
   *
   * @static
   * @param string $url
   * @param array $data
   *
   * @return array
   */
	public static function put($url, array $data)
	{
		return self::request($url, self::PUT, $data);
	}

  /**
   * send a post request
   *
   * @static
   * @param string $url
   * @param array $data
   *
   * @return array
   */
	public static function post($url, array $data)
	{
		return self::request($url, self::POST, $data);
	}

  /**
   * send a get request
   *
   * @static
   * @param string $url
   *
   * @return array
   */
	public static function get($url)
	{
		return self::request($url, self::GET);
	}

  /**
   * send a delete request
   *
   * @static
   * @param string $url
   *
   * @return array
   */
	public static function delete($url)
	{
		return self::request($url, self::DELETE);
	}
}
