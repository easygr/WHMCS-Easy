Easy.gr Module Whmcs
===========================
 
 

Installation
------------

.. code-block:: bash

First step is to upload the content of folder source to the /path/to/whmcs on your server.
	
	

Settings
------------

.. code-block:: bash

Login in Whmcs Admin Panel and follow the steps below:

a) Go to configuration->domain registrars->registrar settings select Easy.gr and then fill
API_USERNAME & API_PASSWORD from Easy.gr.

b) Go to configuration->domain pricing, add all the TLDs you want. Select Easy.gr
as Registrar.


For Domain Name Search:

a) Download from your server the file includes/whoisservers.php

b) Open the file includes/whoisservers.php and replace with your API_USERNAME & API_PASSWORD

.com|https://api.easy.gr/whoisplain?username=API_USERNAME&password=API_PASSWORD&domain=|HTTPREQUEST-not exist

.gr|https://api.easy.gr/whoisplain?username=API_USERNAME&password=API_PASSWORD&domain=|HTTPREQUEST-not exist

.net|https://api.easy.gr/whoisplain?username=API_USERNAME&password=API_PASSWORD&domain=|HTTPREQUEST-not exist

.eu|https://api.easy.gr/whoisplain?username=API_USERNAME&password=API_PASSWORD&domain=|HTTPREQUEST-not exist

...
...
use the above format and add any domain TLD you like. 


System Requirements
-------------------

.. code-block:: bash

*	Easy.gr API_USERNAME
*	Easy.gr API_PASSWORD
*	php5-curl



Copyright
---------
Easy.gr
