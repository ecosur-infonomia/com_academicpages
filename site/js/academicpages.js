/**
* Copyright 2012, 2013 ECOSUR (El Colegio de La Frontera Sur)
*
*  MODEL (AcademicPages.js)
*
*  Top-level javascript file for template view rendering and
*  callback interactivity.
*
* Date: 11/19/12
* Author: awaterma@ecosur.mx
*/

var Model = function () {
    /* Use Strict evaluation */
    "use strict";

    /* No conflict */
    jQuery.noConflict();

    /* Enforce single call to Render */
    var isRendered = false;

    /* For checking for recent publications */
    function isRecent(data) {
        var year = new Date().getFullYear();
        if (+data === +year || +data === +year - 1) {
            return true;
        }
    }

    /*
     * Binds the tab controls.
     */
    function BindTabControls (projects, students, courses, outsideCourses, educationContinua, publications) {
        jQuery('#proyectos').click(function (e) {
            e.preventDefault();
            jQuery(this).tab('show');
            RenderTemplate("#proj-template","#content", projects);
        });

        jQuery('#cursos').click(function(e) {
            e.preventDefault();
            jQuery(this).tab('show');
            RenderTemplate("#cursos-template","#content", CompactedDict(courses.course,"NombreMateria"));
        });

        jQuery('#otros').click(function(e) {
            e.preventDefault();
            jQuery(this).tab('show');
            RenderTemplate("#outside-template","#content", outsideCourses);
        });

        jQuery('#educacion').click(function(e) {
            e.preventDefault();
            jQuery(this).tab('show');
            RenderTemplate("#education-template", "#content", educationContinua);
        });

        jQuery('#doctorados').click(function (e) {
            e.preventDefault();
            jQuery(this).tab('show');
            RenderTemplate("#thesis-template","#content", { student: Filter (students.student,"ProgramaCorto","Doctorado")});
        });

        jQuery('#maestros').click(function (e) {
            e.preventDefault();
            jQuery(this).tab('show');
            RenderTemplate("#thesis-template","#content", { student: Filter (students.student,"ProgramaCorto","Maestría")});
        });

        jQuery('#lic').click(function (e) {
            e.preventDefault();
            jQuery(this).tab('show');
            RenderTemplate("#thesis-template","#content", { student: Filter (students.student,"ProgramaCorto","Licenciatura")});
        });

        jQuery('#todos').click(function(e) {
            e.preventDefault();
            jQuery(this).tab('show');
            RenderTemplate("#publications-template","#content", publications);
        });

        jQuery('#recientes').click(function(e) {
            e.preventDefault();
            jQuery(this).tab('show');
            RenderTemplate("#publications-template","#content", {
                publication: Filter(publications.publication,"PubYear", isRecent)});
        });
    }

    /*
     * Renders a Handelbars template selected with "selector" and populating
     * the entity available via "htmlSelector" with the date "data".
     *
     * @param selector
     * @param htmlSelector
     * @param data
     * @constructor
     */
    function RenderTemplate(selector, htmlSelector, data) {
        var template = Handlebars.compile(jQuery(selector).html());
        jQuery(htmlSelector).html(template(data));
    }

    /*
     * Filters the object "data" for all contained objects that
     * contains the parameter "parameter" equal to "test" or that
     * satisfy a boolean return from the function "test".
     *
     * Non-destructive.
     *
     * @param data
     * @param parameter
     * @param value
     * @return {Array}
     * @constructor
     */
    function Filter(data, parameter, test) {
        var filtered = [];
        try {
            test = eval(test);
        } catch (err) { // do nothing
        }
        var isFunction = test instanceof Function;
        for (var i = 0; i < data.length; i++)
        {
            if (isFunction) {
                if (test(data[i][parameter])) {
                    filtered.push(data[i]);
                }
            } else {
                if (data[i][parameter] === test) {
                    filtered.push(data[i]);
                }
            }
        }
        return filtered;
    }

    /*
     * Takes an array of values, arr, and compacts those values by key, 'key',
     * into a new array indexed by key and containing each original array entry as
     * an array of values.
     *
     * key1->val
     * key1->val
     * key2->val
     *
     * becomes
     *
     * key_column1->
     *  ->key1->val
     *  ->key1->val
     * key_column2->
     *  ->key2->val
     *
     */
    function CompactedDict (original, key_column) {
        var key, dict = {};
        for (var i = 0; i < original.length; i++) {
            key = original[i][key_column];
            /* Extract the key */
            if (typeof dict[key] === 'undefined') {
               dict[key] = [];
               dict[key].push(original[i]);
            } else {
               dict[key].push(original[i]);
            }
        }
        return {
            keys   : Object.keys(dict),
            values : Object.values(dict)
        };
    }


    return {

        /*
         * Initialization function for rendering starting page
         *
         * @param data (personal data)
         * @param related (related academics)
         * @param projects (this individuals projects)
         * @param students (this individuals students)
         */
        Render : function (data, related, projects, students, courses, outsideCourses, educationContinua, publications) {
            if (!isRendered) {
                /* Handlebars render helpers */
                Handlebars.registerHelper('is_active', function() {
                    if (this.Estatus === "Activo" || this.Estatus === "A") {
                        return new Handlebars.SafeString("<span class='active'>Vigente</span>");
                    } else {
                        return new Handlebars.SafeString("Concluido");
                    }
                });

                Handlebars.registerHelper('is_active_thesis', function() {
                    if (this.Estatus === "Activo" || this.Estatus === "A") {
                        return new Handlebars.SafeString("<span class='active'>En&nbsp;proceso</span>");
                    } else {
                        return new Handlebars.SafeString("Concluido");
                    }
                });
                Handlebars.registerHelper('is_active_row', function() {
                    if (this.Estatus === "Activo" || this.Estatus === "A") {
                        return new Handlebars.SafeString("<tr>");
                    } else {
                        return new Handlebars.SafeString("<tr class='muted'>");
                    }
                });
                /* Handlebars navigation helpers */
                Handlebars.registerHelper('nav_filter', function(arr, col, functor, value, id) {
                    var filtered = Filter(arr,col,functor);
                    if (filtered.length > 0) {
                        return new Handlebars.SafeString("<li><a id=\'" + id + "\' tabindex='-1' href=''#'>" + value +
                            "</a></li>");
                    } else {
                        return "";
                    }
                });
                /* Navigation */
                RenderTemplate("#nav-template","#nav-div", {outside: outsideCourses.course, students: students.student,
                    projects: projects.project, courses: courses.course, continua: educationContinua.course,
                    publications: publications});
                BindTabControls (projects, students, courses, outsideCourses, educationContinua, publications);

                /* Image -- Sabatical information is included in the "inactivo" field from our data query. A
                 * SPECIAL CASE is made to ensure that this does not include an absence for directing the institution
                  * (with an IDPuesto == 8).*/

                if (data[0].IDPuesto == 8) {
                    RenderTemplate("#photo-template", "#photo", { imgURL:data[0].Fotografia, inactive:null});
                } else {
                    RenderTemplate("#photo-template", "#photo", { imgURL:data[0].Fotografia, inactive:data[0].Permiso});
                }

                /* Labor/Academic information  */
                RenderTemplate("#datos-personales-template", "#datos-personales", {
                    nombre: data[0].NombrePersonal,
                    apellidos: data[0].ApellidosPersonal,
                    dept: data[0].NombreDepartamentoArea,
                    deptId: data[0].IDDepartamentoArea,
                    grupo: data[0].NombreLineaSubArea,
                    asociados: data[0].grupoAsociado.substring(0, +data[0].grupoAsociado.length - 2),
                    unidad: data[0].NombreUnidad,
                    email: data[0].Email,
                    telephone: data[0].Telefono,
                    extension: data[0].ExtensionTelefonica,
                    titulo: data[0].Titulo,
                    grado: data[0].IDNivelGrado,
                    categoria: data[0].NombreCategoria,
                    sni: data[0].IDNivelSNI,
                    paginaweb: data[0].PaginaWebPersonal
                });

                /* Areas */
                RenderTemplate("#areas-template","#areas-div", {
                    areas:data[0]['AreasDeInteres']
                });

                /* Sintesis  */
                RenderTemplate("#sintesis-template","#sintesis-div", {
                    sintesis:data[0]['SintesisCurricular']
                });

                /* Related Academics */
                RenderTemplate("#rel-template","#rel-div", related);

                /* Projects, Students and/or courses (rendered and active in main content) */
                if (projects.project.length > 0) {
                    RenderTemplate("#proj-template","#content", projects);
                } else if (students.student.length > 0) {
                    var doctors = Filter(students.student, "ProgramaCorto", "Doctorado");
                    var masters = Filter(students.student, "ProgramaCorto", "Maestría");
                    var licens = Filter(students.student, "ProgramaCorto", "Licenciatura");
                    if (doctors.length > 0) {
                        jQuery('#doctorados').tab("show");
                        RenderTemplate("#thesis-template","#content", {student: doctors});
                    } else if (masters.length > 0) {
                        jQuery('#maestros').tab("show");
                        RenderTemplate("#thesis-template","#content", {student: masters});
                    } else {
                        jQuery('#lic').tab("show");
                        RenderTemplate("#thesis-template","#content", {student: licens});
                    }
                } else if (publications.publication.length > 0) {
                    jQuery('#todos').tab("show");
                    RenderTemplate("#publications-template","#content", publications);
                } else if (courses.course.length > 0) {
                    jQuery('#cursos').tab("show");
                    RenderTemplate("#cursos-template","#content",CompactedDict(courses,"NombreMateria"));
                } else if (outsideCourses.course.length > 0) {
                    jQuery('#otros').tab("show");
                    RenderTemplate("#outside-template","#content", outsideCourses);
                } else if (educationContinua.course.length > 0) {
                    jQuery('#educacion').tab("show");
                    RenderTemplate("#education-template", "#content", educationContinua);
                }
            }

            /* Set the guard to true */
            isRendered = true;
        },

        /**
         * Presents a placeholder image for handling 404 errors with images.
         *
         * @param image
         * @constructor
         */
        OnImgError : function (image) {
            /* Null out error handler (prevent loops) */
            image.onerror = null;
            /* Load place holder */
            image.src = "components/com_academicpages/img/placeholder.png";
        }
    };
}();
