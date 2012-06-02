PV Critsend
==============

PV Critend is an application than intergrates Critsend with ProdigyView. If you are unfamiliar with Critsend, http://www.critsend.com/, it is a service for delivering emails.

##What is ProdigyView
ProdigyView is an open-source framework for building applications. Unlike many frameworks, it is formless and was not built around the MVC design pattern but utilizes adapters, filters and obversers to take an aspect oriented approach to programming.

##Installation
1. Copy or clone the files in pv_critsend into your designated librares folder.
2. Make sure php soap module is installed on your server
3. At the beginning of execution of your script set the username and password used to access critsend ex: define('CRITSEND_API_USER', 'joe@example.com') ; define('CRITSEND_API_PASSWORD', 'abc123');
4. At the beginning of execution, add the library to ProdigyView execution(ex: PVLibraries::addLibrary('pv_critsend');

###Example
<pre><code>
define('CRITSEND_API_USER', 'joe@example.com') ;
define('CRITSEND_API_PASSWORD', 'abc123');
PVLibraries::addLibrary('pv_critsend');

/**
Use explicit loading to ensure that the design patterns are included
ex: PVLibraries::addLibrary('pv_critsend', array('explicit_load' => true));
*/

$args = array(
			'receiver' => 'contact@prodigyview.com',
			'subject' => 'Hello Word',
			'text_message' => 'Just Saying Hello World',
			'html_message' => 'Just Saying Hello World',
			'sender' => 'me@example.com',
			'reply_to' => 'me@example.com',
			'mailfrom_friendly' => 'John Doe'
);

Critsend::sendEmail($args);
</code></pre>

##Overriding PVMail::sendMail
Critsend for ProdigyView comes with an adapter that allows overriding of the regular mail function in ProdigyView. This mean you will never have to instantiate an Critsend object, but just you the regular PVMail::sendEmail(). The adapter will always use Critsend for sending an email. This is designed to be loosely coupled.

#Observer
The method 'sendEmail' has a observer that will be used to attach functionality after email has attempted to be sent. Using the obsever, you will get a dump of the data that attempted to be sent.

###Example

<pre><code>
Critsend::addObserver('Critsend::sendEmail', 'read_closure', function($args, $results) {
//Execution of code here
}, array('type' => 'closure'));