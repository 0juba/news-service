# news-service

##After installation (composer)
* Configure ServiceBuilder lib (all needed dependecies, here: vendor/xandrew/service-builder/src/Configs)
* Run script: app/install.php

That's all, ready to use.

###Examples


Twig example:
GET/ /

API:

*Date format in the all requests: d-m-Y*

####get method:

api/0c1eff79bed398dbff29d65febc0cbbf/get/5

####push method:

api/0c1eff79bed398dbff29d65febc0cbbf/push/5

Request have to contains required params in case of new record: title,alias,content,newsDate

####list method:

api/0c1eff79bed398dbff29d65febc0cbbf/list/2/0/-newsDate/05-01-2015/26-05-2015
