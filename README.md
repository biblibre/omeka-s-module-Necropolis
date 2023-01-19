# Necropolis (module for Omeka S)

Necropolis keeps track of deleted resources (items, item sets and media) so you
can see which resources have been deleted, when, and by who.

## Installation

See the official documentation section on
[adding modules to Omeka S](https://omeka.org/s/docs/user-manual/modules/#adding-modules-to-omeka-s)

## Usage

Once the module is enabled, it will automatically save deleted resources in a
separate database table (`necropolis_resource`).
The list of deleted resources can be accessed by a link in the admin menu.

**WARNING:** Uninstalling the module will delete all saved resources permanently.

## License

Necropolis is distributed under the GNU General Public License, version 3.
The full text of this license is given in the license file.
