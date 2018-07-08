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

Obviously if you already have "repositories" section in your composer.json,
you would add the "type" and "url" block in there.

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

If you have any questions, feel free to use Google because I can't help
you more than what I've provided with this library and README.
