<?php
    /*
     * Copyright 2013 ECOSUR (El Colegio de La Frontera Sur) and Andrew Waterman
     * 
     * Academic Pages Router for Search Engine Friendly URLs.
     *
     * Date: 8/5/13
     * User: awaterma@ecosur.mx 
     */

    defined('_JEXEC' ) or die ('Restricted access');

    function AcademicPagesBuildRoute(&$query) {
        $segments = array();
        if (isset($query['id'])) {
           $segments[] = $query['id'];
           unset($query['id']);
        }

        return $segments;
    }

    function AcademicPagesParseRoute($segments) {
        $vars = array();
        $vars['id'] = (int) $segments[0];
        return $vars;
    }



