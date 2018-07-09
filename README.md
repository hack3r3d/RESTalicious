# REST PHP

Simple class to build a REST API using PHP.

## INSTALLATION
You install this with composer. I guess you could do it without composer,
but that seems dumb.

Add this to your composer.json

```
"repositories" : [{
  "type" : "vcs",
  "url": "git@github.com:hack3r3d/RESTalicious.git"
}]
```

```
"require" : {
  "RESTalicious" : "dev-master"
}
```

Obviously if you already have a "repositories" section in your composer.json,
you would add the "type" and "url" block in there. The same for "require."

Also, obviously you would need to run install the RESTalicious library.

```
composer update
```

## USAGE

Create a class, for instance Webservice.php that extends RESTalicious\Rest.

In Webservices.php you create your nouns and verbs. So if you wanted
to create Rest api for a toilet you would create a method called toilet
that might look like this.

```
protected function toilet()
{
    switch ($this->verb) {
        case 'flush':
            $this->toiletFlush();
        break;
    }
    return $this->response;
}
```

So toilet is the noun and flush is the verb, hence the toiletFlush() method
call. The real work is done in toiletFlush() and you should return some 
sort of response object - that's the $this->response at the end of the
toilet method. I would create a class called Response or something, that
would get returned to the client as JSON. Those are the details you
need to figure out on your own.

In terms of how to wire this all up to make a Rest interface, I 
create a file called api.php in a web directory for instance
that looks like this.

```
<?php
require '../vendor/autoload.php';
use hack3r3\Bathroom\AuthenticationException;
use hack3r3d\Bathroom\Webservice;
if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
    $_SERVER['HTTP_ORIGIN'] = $_SERVER['SERVER_NAME'];
}

try {
    $webservice = new Webservice($_REQUEST['request'], $_SERVER['HTTP_ORIGIN']);
    header('Content-Type: application/json');
    echo $webservice->processAPI();
} catch (AuthenticationException $ae) {
    header('HTTP/1.1 401 Unauthorized');
    echo $ae->getMessage();
}
```

I also create a .htaccess file that looks like this.

```
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^api/v1/((?!api.php$).+)$ api.php?request=$1 [QSA,NC,L]
</IfModule>
```

This allows for the Rest interface URLs to look like this.

```
http://localhost/Toilet/web/api/v1/toilet/flush
```

This is clearly just one way to do this, you can implement your design
however you want, but this example shows how the different components
might fit together.

If you have any questions, feel free to use Google because I can't help
you more than what I've provided with this library and README.