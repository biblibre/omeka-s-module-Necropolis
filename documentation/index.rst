Necropolis User Manual
======================

`Necropolis`_ is a module for Omeka S that keep track of deleted resources
(items, item sets and media).

.. _Necropolis: https://github.com/biblibre/omeka-s-module-Necropolis

Installation
------------

See the official documentation section on
`adding modules to Omeka S <https://omeka.org/s/docs/user-manual/modules/#adding-modules-to-omeka-s>`_

Usage
-----

Once the module is enabled, it will automatically save deleted resources in a
separate database table (``necropolis_resource``).
The list of deleted resources can be accessed by a link in the admin menu.

**WARNING:** Uninstalling the module will delete all saved resources permanently.

License
-------

Necropolis is distributed under the GNU General Public License, version 3.
The full text of this license is given in the ``LICENSE`` file.
