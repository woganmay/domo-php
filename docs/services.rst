========
Services
========

DataSets
--------

This service lets you create, update and delete datasets. You can also import and export data as CSV, though it's advisable to limit this to smaller sets (<50MB). For really big loads you'll want to use the Streams API.

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

Working with PDP
~~~~~~~~~~~~~~~~

domo-php includes a set of methods for working with PDP on a dataset:

* ``getPDPList()``
* ``getDataSetPDP()``
* ``createDataSetPDP()``
* ``updateDataSetPDP()``
* ``deleteDataSetPDP()``

These will be documented in more detail at a later date. It doesn't look like the PDP system is accessible through the Domo UI anymore, so while the API is still creating policies, there's no way to interact with them through the UI anyway.

Groups
------

Groups are pretty simple - they're just containers that can hold users. There's the option to set a group as the "default" group for new users to join, but that method doesn't seem to work.

Creating and populating a group
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Simple enough to create a group::

    $name = "My Group";
    $group = $client->API->Group->createGroup($name);

To populate the group, you will need the User IDs of the people you want to add. Users are added one at a time, by sending in the Group ID and the User ID to add::

    $client->API->Group->addUser($group->id, 12345);

Users are removed from groups in a similar way::

    $client->API->Group->removeUser($group->id, 12345);

Renaming a group
~~~~~~~~~~~~~~~~

To rename a group, you just need its ID::

    $client->API->Group->renameGroup($group->id, "New Name");

Activating and deactivating groups
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

If you need to activate or deactivate groups, there are simple methods for that::

    $client->API->Group->activateGroup($group->id);
    $client->API->Group->deactivateGroup($group->id);

Deleting a group
~~~~~~~~~~~~~~~~

By deleting a group, it'll be removed from any pages or cards it's associated to. The users in the group won't be affected::

    $client->API->Group->deleteGroup($group->id);

Pages
-----

This service lets you work with pages and collections.

Getting existing pages
~~~~~~~~~~~~~~~~~~~~~~

As with every other service, a ``getList()`` method lets you get a paginated list of existing pages::

    $limit = 100; // Maximum: 500
    $offset = 0;
    $pages = $client->API->Page->getList($limit, $offset);

Creating Pages and Collections
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Pages are a lot like Groups - containers for things. Creating them just requires a name::

    $page = $client->API->Page->createPage("Page Name");

You can optionally pass in an array of properties. To nest a page, you'll want a parentId for another page.

To add a new collection to the system, you need the Page ID and the title::

    $collection = $client->API->Page->createPageCollection($page->id, "My Collection");

Populating Pages and Collections
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

To assign cards to collections (or the pages they contain), you need to issue an update call with the IDs you want. There's a simple function for pages::

    $client->API->Page->addCard($page->id, $card_id);

To do this for collections, you'll want to do an update:

    $client->API->Page->updatePageCollection($page->id, [ 'cardIds' => [123,456] ]);

The same works for removing cards - just issue updates absent the card IDs you want to remove.

Deleting Pages and Collections
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Deleting pages won't delete the cards themselves. Deleting a parent page won't cascade down to the child pages - they just become orphaned::

    $client->API->Page->deletePage($page->id);
    $client->API->Page->deletePageCollection($page->id, $collection->id);

Users
-----

Getting Users
~~~~~~~~~~~~~

Use the ``getList()`` method to fetch existing users:

    $limit = 10;
    $offset = 0;
    $users = $client->API->User->getList($limit, $offset);

Adding new users
~~~~~~~~~~~~~~~~

When creating a new user, you need a primary email address (unique in your instance), and you have the option of sending an email invite or not.

You can't use this endpoint to set or change the user's password, so you'll usually want the invite sent. If you don't, the only way to set a password would be to go into the admin panel and use the Reset Password feature (or have the user do a self-service reset).

To create a user with all the defaults::

    $name = "John Doe";
    $email = "john.doe@example.org";
    $user = $client->API->User->createUser($name, $email);

That will create a Participant user with no additional attributes, without sending an invite. To do a full-on onboarding::

    $profile = [
        "title" => "Junior Something",
        "mobile" => "+18001234567",
        "employeeNumber" => "007"
    ];
    $sendInvite = true;
    $user = $client->API->User->createUser("Full User", "fulluser@example.org", "Privileged", $profile, $sendInvite);

This creates a user with some prepopulated profile fields, and dispatches an email invite.

Updating Users
~~~~~~~~~~~~~~

**Important!** There's an oddity with this endpoint. In order to do an incremental update, you need to specify the user's existing email addresses::

    $client->API->User->updateUser(123, "john.doe@example.org", [ "title" => "Senior Something" ]);

Deleting Users
~~~~~~~~~~~~~~

To delete a user, you just need the ID::

    $client->API->User->deleteUser(123);


