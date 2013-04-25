<?php
/* 
 * Copyright 2012 ECOSUR (El Colegio de La Frontera Sur)
 * view.html.php
 *
 * Date: 10/31/12
 * User: awaterma@ecosur.mx
 * 
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HTML View class for the HelloWorld Component
 */
class AcademicPagesViewAcademicPages extends JView {

    protected $id, $datosPersonales, $datosRelacionados,
        $proyectos, $tesis, $cursos, $cursosYTalleres,
        $educacionContinua, $publicaciones;

    // Overwriting JView display method
    function display($tpl = null)
    {
        try {
            $this->id = $this->get('id');
            $this->datosPersonales = $this->get('DatosPersonales');
            $this->datosRelacionados = $this->get('Related');
            $this->proyectos = $this->get('Projects');
            $this->tesis = $this->get('Tesis');
            $this->cursos = $this->get('CursosDePosgrado');
            $this->cursosYTalleres = $this->get('CursosYTalleres');
            $this->educacionContinua = $this->get('EducacionContinua');
            $this->publicaciones = $this->get('Publications');
        } catch (Exception $e) {
            JError::raiseError(500, $e->getMessage());
        }

        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }
        return parent::display($tpl);
    }
}
