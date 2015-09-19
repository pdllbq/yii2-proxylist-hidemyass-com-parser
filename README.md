yii2-proxylist.hidemyass.com-parser
===============================
Parse proxy from proxylist.hidemyass.com extension for Yii2

Installation
------------
The preferred way to install this extension is through [composer](http://getcomposer.org/download/).
Either run

```
php composer.phar require pdllbq/yii2-proxylist-hidemyass-com-parser
```

or add


```
"pdllbq/yii2-proxylist-hidemyass-com-parser": "*"
```

to the require section of your `composer.json` file.

Usage
-----

```
use pdllbq\phcparser\Parser;

$data=Parser::getProxy();
```

$data- is array with proxys

Usage exemple
-------------

```php
<?php
namespace app\controllers;

use yii\web\Controller;
use pdllbq\phcparser\Parser;

class ProxyController extends Controller
{
	public function actionIndex()
	{
		print_r(Parser::getProxy());
	}

}
```