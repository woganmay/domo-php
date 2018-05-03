========
Overview
========

The domo-php library is organized into two parts - Services and Helpers. Mostly you'll be using the Services to interact with the API itself. The Helpers streamline some of the more tedious tasks.

This distinction is made in the object structure itself::

    $client = new DomoPHP($id, $secret);

    $client->API; // Services that use the Domo API
    $client->Helpers; // Helpers for the more tedious stuff

.. toctree::
   :maxdepth: 2

   services
   helpers