# GravityForms to Zendesk Ticket

GravityForms to Zendesk Ticket is a simple Wordpress functions.php filter to pass GravityForms fields to a Zendesk ticket, including attachments.  It utilizes the [Zendesk v2 API](https://developer.zendesk.com/rest_api/docs/core/introduction), PHP, and cURL.

### Version
1.0.0

### Requirements

You need a working Wordpress installation, GravityForms plugin installed and at least one form to reference, Zendesk API key/credentials, and cURL enabled on your server.

### To Use

- Include the filter file from your own Wordpress theme's function.php file or cut/paste the contents of inc/gravityforms-to-zendesk-ticket.php into your own functions.php file.

- Update the Zendesk API credentials in the script.

- Reference the correct GravityForm form ID in the filter reference.

- Reference the appropriate GravityForm form field IDs in the ticket generation portion to send to Zendesk.

- **Please note: this script is for one attachment per ticket, for multi-file uploads the script will need to be modified to split the request [currently on the Todo]**

### To Do

 - Split requests to handle multiple file uploads per ticket.

License
----

MIT

[Christi Richards](http://www.christirichards.com)
[@christirichards](http://twitter.com/christirichards)