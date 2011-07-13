pneo4j is a PHP REST Client for Neo4j

This Library is forked from [Neo4J-REST-PHP-API-client](https://github.com/onewheelgood/Neo4J-REST-PHP-API-client)

Integration in Symfony2
=======================

Add pneo4j to your vendor/ dir
---------------------------------------------

Using the vendors script
~~~~~~~~~~~~~~~~~~~~~~~~

Add the following lines in your ``deps`` file::

    [pneo4j]
        git=git://github.com/nightchiller/pneo4j.git

Run the vendors script::

    ./bin/vendors install

Using submodules
~~~~~~~~~~~~~~~~

::

    $ git submodule add git://github.com/nightchiller/pneo4j.git vendor/pneo4j

Register autoloading
----------------------------------------

::

    // app/autoload.php
    $loader->registerPrefixes(array(
        ...
        'Neo4j' => __DIR__.'/../vendor/pneo4j/lib',
    ));
