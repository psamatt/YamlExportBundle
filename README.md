# YamlExportBundle

Symfony2 bundle to export database records into YAML format using a Symfony2 Command. 

This bundle allows you to export specific database records into YAML format enabling DBUnit testing on your repositories functions. This can be used as a very generic export of all rows such as `SELECT * FROM table` or you can create a very specific use case using powerful DQL (or native SQL) to export those rows that enable you to have a full test suite for your repository functions.

## What you need 
This bundle requires Symfony 2 (or greater) including Doctrine 2

## Installation

### Step 1: Download the YamlExportBundle using composer

Add YamlExportBundle in your composer.json:

```js
{
    "require": {
        "Psamatt/YamlExportBundle": "dev-master"
    }
}

```

Now tell *composer to download the bundle by running the command:

` $ php composer.phar update Psamatt/YamlExportBundle `

Composer will install the bundle to your project's `vendor/psamatt` directory.

* Note: If you don't have Composer yet, download it following the instructions on
http://getcomposer.org/ or just run the following command:

`curl -s https://getcomposer.org/installer | php`

### Step 2: Enable the bundle

Enable the bundle in the kernel:

```php

<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Psamatt\YamlExportBundle\PsamattYamlExportBundle(),
    );
}

```


## Example usage

#### Using DQL and writing out to Terminal

`php app/console YamlExport:data-dump "SELECT * FROM AcmeStoreBundle:BlogPost"`

#### Using DQL and storing output to a specified file

`php app/console YamlExport:data-dump "SELECT * FROM AcmeStoreBundle:BlogPost" > /path/to/file.yml`

#### Using DQL namespaced entity to a specified file

`php app/console YamlExport:data-dump "SELECT * FROM \Acme\StoreBundle\Entity\BlogPost" > /path/to/file.yml`

#### Using SQL and storing output to a specified file

`php app/console YamlExport:data-dump "SELECT * FROM blog_posts" --sql > /path/to/file.yml`

** Then in your Unit Test file you need to specify the YAML file **

```php
// ..
	public function getDataSet()
	{
		$dataSet = new \PHPUnit_Extensions_Database_DataSet_YamlDataSet(dirname(__FILE__) . "/_files/BlogPost/seed.yml");
		// .. Add further YAML files
		// $dataSet->addYamlFile(dirname(__FILE__) . "/_files/path/to/other/seed.yml");		
		return $dataSet;
	}
// ..

```

