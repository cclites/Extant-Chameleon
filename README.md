##SSCP
<hr>
<b>Description: </b>ShipStation to ControlPad integration<br> 
<b>By: </b>Chad Clites<br>
<b>Email: </b><a a href="mailto:chad@extant.digital">chad@extant.digital</a><br>
<b>Slack: </b>extantdigital.slack.com<br><br> 

Document revision date: 08/26/2020<br>

<hr>

#####Requirements
PHP 7.2 or greater

<hr>

#####Installation

<hr>

#####SSCP Operation

######<i>ControlPad to ShipStation Conversion</i>
<ul>
    <li>Cron job runs artisan cron:process-cp-to-ss</li>
    <li>SSCP pulls orders by API key.</li>
    <li>ControlPad order data is transformed into ShipStation order data</li>
    <li>Orders are entered into ShipStation</li>
    <li>Update ControlPad orders to Pending</li>
</ul>
<br>

######<i>ShipStation Shipping Notification</i>
<ul>
    <li>When an order is marked as shipped, ShipStation hits the SSCP api endpoint.</li>
    <li>SSCP updates the status of the related CP order to Fulfilled</li>
</ul>

<hr>

#####General Development Notes:

SSCP does not run off a database like a conventional web app, but instead acts as a translation layer between ControlPad and other providers. There are no model 'relationships' between SS and CP like would normally exist.

<br>

######<i>Resources</i>

SSCP uses ControlPadResource and ShipStationResouce to transform order data from one type to another. When sending an order to SS, ControlPadResource is used to convert the data to a SS representation.

<br>

######<i>Models</i>

Models are used to create data necessary for transmission via API, such as headers, or encrypting authentication tokens.

<br>

######<i>Configs</i>

Sscp.php holds endpoints and system authentication data for API providers and should be populated by a .env file

Logging.php holds configuration information for Rollbar.

Database.php holds configs for cache storage.

Auths.php holds user API tokens and other credentialing information.

<br>

######<i>Jobs</i>

The job classes for ShipStation and ControlPad queues are unused for the moment until load testing or performance issues dictate otherwise. These functions are stubbed in and ready to be expanded as needed.

<br>

######<i>Webhooks</i>

Webhooks should follow the following format:

https://shipping-api.controlpad.com/shipstation/webhooks/CLIENT_NAME/notify-shipped 
https://shipping-api.controlpad.com/shippingeasy/webhooks/CLIENT_NAME/notify-shipped 

<hr> 

#####Operational Notes:

User authentications are loaded from auths config. When the Cron runs, it looks for an array of users users in auths config, and processes each user.

<hr>

#####DEVELOPER UPDATES
08/26/2020

######<i>Credentials</i>

To support multi-tenency and multiple APIs, config/auths.php was modified to allow mamangement of multiple shippers.

######<i>Operational Notes</i>
App was modified to allow for multiple shippers

######<i>Crons</i>

Each integration has their own cron job.
- cron:process-cp-to-ss
- cron:process-cp-to-se

<br>

######<i>Code Reorganization</i>

Code base has been reorganized to use repository and transformer patterns. By following a more conventional pattern, it will be easier to establish a point-by-point checklist for integrating new providers.

<br>

######<i>Dependencies</i>
There are no new required dependencies in this update.







