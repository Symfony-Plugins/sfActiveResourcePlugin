##Overview##

The sfActiveResource plugin is an implementation of the ruby ActiveResource approach to consuming REST based services based initially off the PHP lib by John Luxford  [http://github.com/lux](http://github.com/lux) and extended to support a more flexible approach for use with Symfony 1.3+.

It supports all the things you'd expect from a REST based consumer, flexible parameters, named methods and support for callbacks from REST servers.

##Usage##

The current implementation can make use of simple named wrappers for setting the initial properties:

    class notification extends sfActiveResource {
      var $site = 'https://xxxxAPI-IDxxx@messagepub.com/';
      var $request_format = 'xml';

      public function send() {
        $this->save();
      }

    }



Using it is a snap - here's an example for using the [MessagePub](http://messagepub.com) Service:

    $notify = new notification(
        array(
          'body' => 'Message body..',
          'subject' => 'Subject line...',
          'recipients' =>
            array (
              'recipient' =>
                  array (
                    'position' => 1,
                    'channel' => 'email',
                    'address' => 'person@example.com'
                  )
            )
        )
    );

    $notify->send();


If the remote REST service supports callbacks - then you can provide a module/action that the sfActiveResource API should invoke when it receives the callback - along with an optional controller should you wish to use a different symfony app.

    $postbackUrl = Notification::callbackURL(module, action, $this->getController());
    $notify = new notification(
       array(
         'postback_url' => $postbackUrl,
         ...
       )
    );

    $notify->send();




### Included Service Examples ###
[MessagePub Messaging](http://messagepub.com/)

[Zerigo DNS Privisioning](http://www.zerigo.com/)

Included in the doc folder are a few extra examples to illustrate how to use the plugin.

## Coming Features ##
Using the Symfony Components DI container to better encapsulate and describe the services.  Currently with the defining your own simple class is cumbersome - a lot of overhead for a little convenience.  It would be far better to use a simple YML or XML approach to describing the service instance.

The DI container however needs a little tweaking to support this type of approach.

