<?php
/**
 * Front controller
 * 
 * @author Antoine Pouch <ant-1@pouch.name>
 * @package gestionlite
 * @module htdocs
 */

require dirname(dirname(__FILE__)) . '/conf/config.php';
require 'protect.php';
require 'dbConnector.class.php';
require 'event/listenerDeclarationParser.class.php';
require 'controller/context.class.php';
require_once 'model/account.class.php';
require_once 'model/movement.class.php';
require_once 'model/recurringMovement.class.php';

listenerDeclarationParser::parse(dirname(dirname(__FILE__)) . '/conf');
dbConnector::initialize(DB_HOST, DB_NAME, DB_USER, DB_PASS);

//Process recurring movements if any pending
require 'processRecurringMovements.php';

//Remove old graph files
require 'removeOldGraphs.php';

context::getInstance()->getController()->run();
context::getInstance()->getController()->show();
?>