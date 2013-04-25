<?php
    /*
     * Copyright 2012 ECOSUR (El Colegio de La Frontera Sur)
     * (TopLevel) academicpages.php
     *
     * Date: 10/31/12
     * User: awaterma@ecosur.mx
     *
     */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import joomla controller library
jimport('joomla.application.component.controller');

// Get an instance of the controller prefixed by AcademicPages
$controller = JController::getInstance('AcademicPages');

// Perform the Request task
$controller->execute(JRequest::getCmd('task'));

// Redirect if set by the controller
$controller->redirect();
