========
Overview
========

The domo-php library is organized into two parts - API and Helpers. Mostly you'll be using the API object to interact with the API itself. The Helpers streamline some of the more tedious tasks.

This distinction is made in the object structure itself::

   $client = new DomoPHP($id, $secret);

   $client->API; // Proxies for accessing the Domo API
   $client->Helpers; // Helpers for the more tedious stuff

.. toctree::
   :maxdepth: 2

   api
   helpers