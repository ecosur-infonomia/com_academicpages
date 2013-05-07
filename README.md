Academic Pages
===============

*A Joomla! component*

Academic Pages is a project to provide more information on Academics, Tehcnicians and others 
working at El Colegio de La Frontera Sur in San Cristóbal de Las Casas, Chiapas, Mexico. This
project is intended to be staged on the Joomla 2.5 installation at ECOSUR for use with the CMS
that governs published information regarding the University.

The project makes light use of the Joomla! framework, providing most functionality for users in
the form of a Single Page Application, driven by simple JSON dictionaries and Handlebars templates.


INSTALL

In order to install this component, be sure that the Joomla server you deploy to has PDO enabled
using FreeTDS. Be sure to update the top part of the used freetds.conf file on your system with
the following in the global settings (the top part of the file):

[global]
tds version = 8.0
client charset = UTF-8


LICENSE

As a Joomla! component, the application is released under version 3 of the GPL. 

Feel free to copy, distribute, modify and use according to the license, whose text is replicated 
in the local copy, LICENSE.txt.


