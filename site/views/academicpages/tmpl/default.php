<?php
/* 
 * Copyright 2012, 2013 Andrew Waterman and ECOSUR (El Colegio de La Frontera Sur)
 * default.php
 *
 *
 * Renders self-contained handlebars templates on the front-end
 * using JavaScript (JS).
 *
 * Date: 10/31/12
 * Author: awaterma@ecosur.mx
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
/* Turn off Mootools captions */
unset($this->_scripts[JURI::root(true).'/media/system/js/caption.js']);
/*
 * Copyright 2013 ECOSUR (El Colegio de La Frontera Sur)
 *
 * frontend.php
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$document = JFactory::getDocument();
$document->setTitle($this->id);
$document->addStyleSheet('components/com_academicpages/css/bootstrap-responsive.min.css');
$document->addStyleSheet('components/com_academicpages/css/academicpages.css');
$document->addScript('components/com_academicpages/js/jquery.min.js');
$document->addScript('components/com_academicpages/js/handlebars-1.0.rc.3.js');
$document->addScript('components/com_academicpages/js/bootstrap.min.js');
$document->addScript('components/com_academicpages/js/academicpages.js');
?>
<noscript>
    Esta página usa <a href="http://handlebarsjs.com">Handlebars</a> JavaScript plantillas para render y la interacción
    con el usuario. Por favor, active JavaScript para ver este contenido.
</noscript>
<!-- Layout -->
<div class="container-fluid" xmlns="http://www.w3.org/1999/html">
    <div id="top" class="row-fluid">
        <div id="photo" class="span3"></div>
        <div id="datos-personales" class="span5"></div>
        <div id="rel-div" class="span3 bs-docs-example scrolled-overflow"></div>
    </div>
    <div id="areas-div" class="row-fluid"></div>
    <div id="sintesis-div" class="row-fluid"></div>
    <div id="nav-div" class="row-fluid top-margin"></div>
    <div id="content" class="row-fluid"></div>
</div>
<!-- Templates -->
<script id="nav-template" type="text/x-handlebars-template">
    <ul id="navTab" class="nav nav-tabs hidden-phone">
        {{#if courses}}
        <li><a id="cursos" href="#">Cursos de Posgrado</a></li>
        {{/if}}
        {{#if outside}}
        <li><a id="otros" href="#">Cursos en Otros Posgrados</a></li>
        {{/if}}
        {{#if continua}}
        <li><a id="educacion" href="#">Educación Continua</a></li>
        {{/if}}
        {{#if projects}}
        <li class="active"><a id="proyectos" href="#">Proyectos</a></li>
        {{/if}}}
        {{#if publications.publication}}
        <li class="dropdown">
            <a class="dropdown-toggle" role="button" data-toggle="dropdown" href="#">Publicaciones<b class="caret"></b></a>
            <ul class="dropdown-menu" role="menu" >
                {{nav_filter publications.publication "PubYear" "isRecent" "Recientes" "recientes"}}
                <li><a id="todos" tabindex="-1" href="#">Todas</a></li>
            </ul>
        </li>
        {{/if}}
        {{#if students.length}}
        <li class="dropdown">
            <a class="dropdown-toggle" role="button" data-toggle="dropdown" href="#">Tesis<b class="caret"></b></a>
            <ul class="dropdown-menu" role="menu" >
                {{nav_filter students "ProgramaCorto" "Doctorado" "Doctorado" "doctorados"}}
                {{nav_filter students "ProgramaCorto" "Maestría" "Maestría" "maestros"}}
                {{nav_filter students "ProgramaCorto" "Licenciatura" "Licenciatura" "lic"}}
            </ul>
        </li>
        {{/if}}
    </ul>
</script>
<script id="photo-template" type="text/x-handlebars-template">
    {{#if inactive}}
    <a class="pull-left" href="#">
        <img class="media-object bottom-margin" src="http://200.23.34.37/Personal/Fotografias/{{imgURL}}.jpg" alt="Imagen de Investigador" onerror="Model.OnImgError(this)">
    </a>
    <div class="alert alert-warning">Temporalmente se encuentra ausente de la institución.</div>
    {{else}}
    <img class="media-object bottom-margin" src="http://200.23.34.37/Personal/Fotografias/{{imgURL}}.jpg" alt="Imagen de Investigador" onerror="Model.OnImgError(this)">
    {{/if}}

</script>
<script id="datos-personales-template" type="text/x-handlebars-template">
    <br>
    <h1>{{#if titulo}}{{titulo}}&nbsp;{{/if}}{{nombre}} {{apellidos}}</h1>
    <dl>
        <dt><a href="<?php echo JRoute::_('index.php?option=com_areasacademic&area={{deptId}}&Itemid=1487')?>">{{dept}}</a></dt>
        <dd>Grupo: {{grupo}}</dd>
        {{#if asociados}}
        <dd>Grupos Asociados: {{asociados}}</dd>
        {{/if}}
        <dt>Unidad {{unidad}}</dt>
        <dd>Categoría Académica: {{categoria}}</dd>
        {{#if sni}}
        <dd>Nivel SNI: {{sni}}</dd>
        {{/if}}
        <dd><i class="icon-envelope"></i>&nbsp;<a href="mailto:{{email}}">{{email}}</a></dd>
        {{#if paginaweb}}<dd><i class="icon-home"></i>&nbsp;<a target="_blank" href='{{paginaweb}}'>pagina web personal</a></dd>{{/if}}
        {{#if extension}}<dd>ext. {{extension}}</dd>{{/if}}}
    </dl>
</script>
<script id="areas-template" type="text/x-handlebars-template">
    {{#if areas}}
    <h3>Áreas de Interés</h3>
    <dl>
        <dd>{{areas}}</dd>
    </dl>
    {{/if}}
</script>
<script id="sintesis-template" type="text/x-handlebars-template">
    {{#if sintesis}}
    <h3>Síntesis Curricular</h3>
    <dl>
        <dd>{{sintesis}}</dd>
    </dl>
    {{/if}}
</script>
<script id="rel-template" type="text/x-handlebars-template">
    <ul>
        {{#each related}}
        <li><a href="<?php echo JRoute::_('index.php?option=com_academicpages&id={{this.IDPersona}}')?>"><i class="icon-user"></i>&nbsp;{{#if this.Titulo}}{{this.Titulo}}{{/if}}{{this.NombreAbreviado}}</a></li>
        {{/each}}
    </ul>
</script>
<script id="proj-template" type="text/x-handlebars-template">
    {{#if project}}
    <table id="projects-table" class="table hidden-phone">
        <tr>
            <th>Título</th><th>Financiado por</th><th>Nivel de Participación</th><th>Estatus</th><th></th>
        </tr>
        {{#each project}}
        {{is_active_row}}
        <td>{{this.Titulo}}</td>
        <td>{{this.NombreContraparte}}</td>
        <td>{{this.NivelParticipacion}}</td>
        <td>{{is_active}}</td>
        <td><a href="#{{this.ClaveProyecto}}" class="btn" role="button" data-toggle="modal"><i class="icon-info-sign"></i></a></td>
        </tr>
        <div id="{{this.ClaveProyecto}}" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="text-info">Más Detalles</h3>
            </div>
            <div class="modal-body">
                <h2><em>{{this.Titulo}}</em></h2>
                <dl>
                    {{#if this.PaginaWeb}}
                    <dt>URL:</dt><dd><a class="label label-info" href="<?php echo('{{this.PaginaWeb}}');?>">{{this.PaginaWeb}}</a></dd>
                    {{/if}}
                    {{#if this.ObjetivoGeneral}}
                    <dt>Objetivo general</dt>
                    <dd><p class="well">{{this.ObjetivoGeneral}}</p></dd>
                    {{/if}}
                    {{#if this.Resumen}}
                    <dt>Resumen</dt>
                    <dd>{{this.Resumen}}</dd>
                    {{/if}}
                    {{#if this.ImpactoEsperado}}
                    <dt>Impacto esperado</dt>
                    <dd>{{this.ImpactoEsperado}}</dd>
                    {{/if}}
                    {{#if this.NombreContraparte}}
                    <dt>Financiado por</dt>
                    <dd>{{this.NombreContraparte}}</dd>
                    {{/if}}
                    {{#if this.InstitucionesQueParticipan}}
                    <dt>Instituciones que participan</dt>
                    <dd>{{this.InstitucionesQueParticipan}}</dd>
                    {{/if}}
                    <dt>Estatus</dt>
                    <dd>{{#if EndDate}}Concluido en {{EndDate}}{{else}}Vigente{{/if}}</dd>
                </dl>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
        {{/each}}
    </table>
    {{/if}}
</script>
<script id="cursos-template" type="text/x-handlebars-template">
    <table id="course-table" class="table hidden-phone">
        <tr>
            <th>Título</th><th>Año</th><td></td>
        </tr>
    {{#each values}}
        <tr>
            {{! As each row should have at least 1 element, we use that element (0 indexed) to pull our shared information }}
            {{! An iterator would be cleaner, but that would require another helper. }}
            <td>{{this.[0].NombreMateria}}</td>
            <td>{{this.[0].Year}}</td>
            <td><a href="#{{this.[0].ROWID}}" class="btn" role="button" data-toggle="modal"><i class="icon-info-sign"></i></a></td>
        </tr>
    {{/each}}
    </table>
    {{! Double walk, as tables can't be nested within tables, even when within a hidden div. }}
    {{#each values}}
    <div id="{{this.[0].ROWID}}" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h3 class="text-info">Más Detalles</h3>
        </div>
        <div class="modal-body">
            <h2><em>{{this.[0].NombreMateria}}</em></h2>
            <table class="table-condensed table-striped hidden-phone">
                <tr class="success">
                    <th>Programa</th>
                    <th>Unidad</th>
                    <th>Créditos</th>
                    <th>Impartido en</th>
                    <th>Descripción</th>
                </tr>
                {{#each this}}
                <tr>
                    <td>{{this.Programa}}</td>
                    <td>{{this.Unidad}}</td>
                    <td>{{this.Creditos}}</td>
                    <td>{{this.Year}}</td>
                    <td>{{#if this.URL}}<a target='_blank' href='{{this.URL}}'>PDF</a>{{/if}}</td>
                </tr>
                {{/each}}
            </table>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn" data-dismiss="modal">Cerrar</button>
        </div>
    </div>
    {{/each}}
</script>
<script id="outside-template" type="text/x-handlebars-template">
    {{#if course}}
    <table id="outside-table" class="table hidden-phone">
        <tr>
            <th>Nombre</th><th>Nivel</th><th>Institucion</th><th>Lugar</th>
        </tr>
        {{#each course}}
        <tr>
            <td>{{this.NombreCurso}}</td>
            <td>{{this.NivelCursoImpartido}}</td>
            <td>{{this.Institucion}}</td>
            <td>{{this.Lugar}}</td>
        </tr>
        {{/each}}
    </table>
    {{/if}}
</script>
<script id="education-template" type="text/x-handlebars-template">
    {{#if course}}
    <table id="education-table" class="table hidden-phone">
        <tr>
            <th>Título</th><th>Descripción</th><th>Año</th><th></th>
        </tr>
        {{#each course}}
        <tr>
            <td>{{this.TituloEvenFort}}</td>
            <td>{{this.DescripcionTipoProducto}}</td>
            <td>{{this.Year}}</td>
            <td><a href="#{{this.IDEvenFort}}" class="btn" role="button" data-toggle="modal"><i class="icon-info-sign"></i></a></td>
        </tr>
        <div id="{{this.IDEvenFort}}" class="modal hide" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="text-info">Más Detalles</h3>
            </div>
            <div class="modal-body">
                <h2><em>{{this.TituloEvenFort}}</em></h2>
                <dl>
                    {{#if this.ImpactoEsperado}}
                    <dt>Impacto esperado</dt>
                    <dd><p class="well">{{this.ImpactoEsperado}}</p></dd>
                    {{/if}}
                    {{#if this.NombreContraparte}}
                    <dt>Nombre contraparte</dt>
                    <dd>{{this.NombreContraparte}}</dd>
                    {{/if}}
                    {{#if this.InstitucionesSinRegistro}}
                    <dt>Instituciones participantes</dt>
                    <dd>{{this.InstitucionesSinRegistro}}</dd>
                    {{/if}}
                    <dt>Horas de duración</dt><dd>{{this.HorasDuracion}}</dd>
                </dl>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
        {{/each}}
    </table>
    {{/if}}
</script>
<script id="thesis-template" type="text/x-handlebars-template">
    {{#if student}}
    <table id="thesis" class="table hidden-phone">
        <tr>
            <th>Tesis</th><th>Alumno</th><th>Estatus</th>
        </tr>
        {{#each student}}
        {{is_active_row}}
        <td>{{this.Tesis}}</td><td>{{this.Alumno}}</td><td>{{is_active_thesis}}</td>
        </tr>
        {{/each}}
    </table>
    {{/if}}
</script>
<script id="publications-template" type="text/x-handlebars-template">
    {{#if publication}}
    <table id="publications" class="table hidden-phone">
        <tr>
            <th>Cita</th><th>Tipo de publicación</th>
        </tr>
        {{#each publication}}
        <tr>
            <td><cite>{{this.Cita}}</cite></td><td>{{this.DescripcionTipoProducto}}</td>
        </tr>
        {{/each}}
    </table>
    {{/if}}
</script>
<!-- View -->
<style>
        /* Override red triangle images from estilo.css */
    li {
        list-style-image: none;
    }
</style>
<script>
    (function () {
        /* Initial JSON dictionaries */
        var related = <?php echo $this->datosRelacionados;?>;
        var personnel = <?php echo $this->datosPersonales;?>;
        var projects = <?php echo $this->proyectos;?>;
        var students = <?php echo $this->tesis;?>;
        var courses = <?php echo $this->cursos;?>;
        var outsideCourses = <?php echo $this->cursosYTalleres;?>;
        var educationContinua = <?php echo $this->educacionContinua;?>;
        var publications = <?php echo $this->publicaciones;?>;

        /* Render the main page */
        /* Note: see academicpages.js for implementation */
        Model.Render(personnel.data, related, projects, students, courses, outsideCourses, educationContinua,
            publications);
    })();
</script>

