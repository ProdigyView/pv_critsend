<?php
/**
 * The adapter is used to ovveride the PVMail sendEmail method and ensure that all outgoing mail use Critsend.
 * The adapter is loosely coupled and can be easily turned off by commenting it out below.
 */
PVMail::addAdapter('PVMail', 'sendEmail', 'Critsend');