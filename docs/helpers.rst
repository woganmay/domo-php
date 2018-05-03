=======
Helpers
=======

The following helpers are available:

DataSet Importer
----------------

If you have a CSV file on disk that you want to quickly load into Domo, there's a straightforward helper method for that::

    $file = "/path/to/import.csv";
    $name = "Dataset Name";

    $dataSet = $client->Helpers->DataSet->createDataSet($name, $file);

The resulting ``$dataSet`` will be the API object, or the method will throw an exception.

Schema Builder
--------------

Build a new Schema
~~~~~~~~~~~~~~~~~~

To create a new schema array, you can use an instance of the SchemaBuilder helper. Chain together all the fields you need, then export it to an array::

    $builder = $client->Helpers->SchemaBuilder->create();

    $builder->string("Full Name");
    $builder->date("Start Date");
    $builder->long("Mobile Number");
    $builder->decimal("Monthly Salary");
    $builder->double("Hourly Rate");
    $builder->datetime("Last Login At");
    $schema = $builder->toArray();

The ``$schema`` array will be created in the same order that the builder methods are called.

Create a Schema from sample data
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

If you have the first two rows of a CSV file, there's a helper that can take a guess at the schema. You'll need to parse them from the import file yourself::

    $headers = [ "Date",       "Fruit",  "Revenue" ];
    $record  = [ "2018-01-01", "Apples", 100.00    ];

    $schema = $client->Helpers->SchemaBuilder->inferSchema($headers, $record);

The resulting ``$schema`` can be used in a ``createDataSet()`` call.