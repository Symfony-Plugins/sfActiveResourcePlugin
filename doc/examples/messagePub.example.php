<?php

/**
 * Signup for a MessagePub account & grab your api key
 *
 * Signup Page: http://messagepub.com/signup
 * API Key: http://messagepub.com/account/edit
 *
 * Very simple wrapper class for notifications - here to just facilitate using sfActiveResource
 * 
 * You can extend it with helper methods - to make use of the object more sensical
 *
*/
class notification extends sfActiveResource {
  var $site = 'https://xxxAPI-IDxxxxxxxx@messagepub.com/';
  var $request_format = 'xml';
  
  public function send() {
    $this->save();
  }
}

/**
 * Now let's create the notification object
 *
 * You can do this in your action, model or, for task driven code - in in a task.
 *
 */
    $notify = new Notification (
        array (
          'body' => 'Message body goes here...',
          'subject' => 'Subject line for emails',
          'recipients' =>
            array (
              'recipient' =>
                  array (
                    'position' => 1,
                    'channel' => 'email',
                    'address' => 'somebody@example.com'
                  )
            )
        )
   );

    /**
     * In the above example - message pub supports a large variety of channel types - twitter, sms, email and even a phone call
     * It'll try each one until it gets a reply - allowing you to implement a notification tree that escallates
     *
     * send the message
     *
     */
    $notify->send();
