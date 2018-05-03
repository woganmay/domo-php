========
Services
========

DataSets
--------

About the Schema
~~~~~~~~~~~~~~~~

Every DataSet has a schema, which is defined as a list of fields of the following types:

* STRING
* LONG
* DOUBLE
* DECIMAL
* DATE
* DATETIME

When creating a schema, each field needs a unique, case-sensitive name, and the order of the schema fields are important when loading data. To make this a little easier, a fluent builder class is provided.

Create a new DataSet
~~~~~~~~~~~~~~~~~~~~

A Domo DataSet consists of the metadata (title, schema, etc), and the records themselves. To create a new dataset, you first need to publish the schema, then follow that with a data import.

This sample uses the Schema Builder to set up a simple dataset::

    $name = "My First Dataset";
    $description = "An optional description";

    $builder = $client->Helpers->SchemaBuilder->create();
    $builder->date("Date");
    $builder->string("Fruit");
    $builder->double("Revenue");
    $schema = $builder->toArray();

    $dataset = $client->API->DataSet->createDataSet($name, $schema, $description);

A successful creation call will return the DataSet object from the API, and any failures will throw an Exception.

Fetch DataSet information
~~~~~~~~~~~~~~~~~~~~~~~~~

To fetch a list of DataSets, use the ``getList()`` method. The Limit and Offset can be used to page through all the datasets you have access to::

    $limit = 50; // Maximum is 50 per call
    $offset = 0;

    $datasets = $client->API->DataSet->getList($limit, $offset);

There's currently no API method for filtering or searching for DataSets, so to find a particular set by name, you will need to pull all of them and page through.

Update DataSet Metadata
~~~~~~~~~~~~~~~~~~~~~~~

To change a DataSet's metadata (name, description or schema), use the ``updateDataSet()`` method::

    $updates = [
      "name" => "New Name",
      "description" => "New Description"
    ];

    $client->API->DataSet->updateDataSet($guid, $updates);

A successful update will return the DataSet object from the API, and any failures will throw an Exception.

Delete a DataSet
~~~~~~~~~~~~~~~~

To irreversibly destroy a DataSet, use the ``deleteDataSet()`` method::

    $client->API->DataSet->deleteDataSet($guid);

This will return a ``TRUE`` or ``FALSE`` depending on whether the DataSet was deleted.

Import data into a DataSet
~~~~~~~~~~~~~~~~~~~~~~~~~~

To replace the data in a DataSet, use the ``importDataSet()`` method. This takes a headerless string variable of CSV content.

**Important!** The order of columns in the import data has to match the order of the dataset schema. Dates should be provided in ISO format (``yyyy-mm-dd``) to ensure there are no errors in parsing::

    $csv = "2018-01-01,Apples,100.00\n2018-01-02,Apples,200.00";

    $client->API->DataSet->importDataSet($guid, $csv);

There are limits to the amount of data you can load this way. If it's a few thousand records it'll be fine, but for larger loads (tens of thousands upwards), you'll want to use the Stream service.

Export data from a DataSet
~~~~~~~~~~~~~~~~~~~~~~~~~~

You can use ``exportDataSet()`` to export the contents of a DataSet as CSV::

    $exportHeaders = false; // Whether to include the header row (default: true)
    $csv = $client->API->DataSet->exportDataSet($guid, $exportHeaders);

The resulting output can be written straight to a file on disk.