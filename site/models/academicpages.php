<?php
    /*
     * Copyright 2012 ECOSUR (El Colegio de La Frontera Sur)
     * (Model) academicpages.php
     *
     * Date: 10/31/12
     * User: awaterma@ecosur.mx
     *
     */
    defined('_JEXEC') or die('Restricted access');
    jimport('joomla.application.component.modelitem');

    /**
     * Academic Pages Model
     */
    class AcademicPagesModelAcademicPages extends JModel
    {

        protected $id, $rfc, $configuration;

        public function getId() {
            if (!isset($this->id)) {
                /* Make sure we are getting a clean int as id */
                $jinput = JFactory::getApplication()->input;
                $this->id = $jinput->getInt('id','332');
            }
        }

        public function getRFC() {
            if (!isset($this->id)) {
                $this->getId();
            }
            $bdi = $this->getBDIConnection();
            $sql = "SELECT rfc FROM [01_Personal] where IDPersona= ? ";
            /* Only interaction with DB using id from request */
            $stmt = $bdi->prepare($sql);
            $stmt->execute(array($this->id));
            $this->rfc = $stmt->fetch()[0];
            $bdi = null;
        }


        /*
         * Get the Working Data for a specific Name. From the requirements
         * documentation:  rfc, unidad, área y línea de investigación, correo electrónico
         * y extensión telefónica.
         *
         * Requirements Section A, Section B and Section C.
         *
         * @Return: A json encoded string of the result set
        */
        public function getDatosPersonales() {
            if (!isset($this->id)) {
                $this->getId();
            }
            if (!isset($this->rfc)) {
                $this->getRFC();
            }
            $bdi = $this->getBDIConnection();
            $sql = "SELECT Fotografia, NombrePersonal, ApellidosPersonal, NombreAbreviado, NombreUnidad,
              NombreDepartamentoArea, NombreLineaSubArea, NombreCategoria, IDNivelSNI, Titulo,
              IDNivelGrado, CarreraOEspecialidad, ExtensionTelefonica, Email, SintesisCurricular,
              AreasDeInteres, PaginaWebPersonal, IDPuesto, (CASE WHEN EXISTS (
                  SELECT NombreLineaSubArea FROM [01_LineasYSubAreasAsignadasAlPersonalAsociados]
                                                 laa INNER JOIN [01_LineasYSubAreas] ll ON laa.IDLineaSubArea = ll.IDLineaSubArea
                  WHERE laa.RFC = PERSONAL.RFC AND EsLaLineaActual = 1) THEN
                (SELECT NombreLineaSubArea + ', ', '' AS 'data()'
                 FROM [01_LineasYSubAreasAsignadasAlPersonalAsociados] laa INNER JOIN
                   [01_LineasYSubAreas] ll ON laa.IDLineaSubArea = ll.IDLineaSubArea
                 WHERE laa.RFC = PERSONAL.RFC AND EsLaLineaActual = 1 FOR xml path('')) ELSE '' END) AS grupoAsociado,
              (CASE WHEN PT.[FechaInicio] <= GETDATE() AND PT.[FechaTermino] >= GETDATE() THEN LOWER([DescripcionPermiso]) END) AS Permiso,
              NombreLineaSubArea, [01_LineasYSubAreas].IDLineaSubArea, [01_DepartamentosYAreas].IDDepartamentoArea
            from [01_Personal] as PERSONAL
              INNER JOIN [01_DepartamentosYAreasAsignadasAlPersonal] ON PERSONAL.RFC = [01_DepartamentosYAreasAsignadasAlPersonal].RFC
              INNER JOIN [01_DepartamentosYAreas] ON [01_DepartamentosYAreas].IDDepartamentoArea = [01_DepartamentosYAreasAsignadasAlPersonal].IDDepartamentoArea
              INNER JOIN [01_LineasYSubAreasAsignadasAlPersonal] ON PERSONAL.RFC = [01_LineasYSubAreasAsignadasAlPersonal].RFC
              INNER JOIN [01_LineasYSubAreas] ON [01_LineasYSubAreas].IDLineaSubArea = [01_LineasYSubAreasAsignadasAlPersonal].IDLineaSubArea
                                                 AND [01_DepartamentosYAreas].IDDepartamentoArea = [01_LineasYSubAreas].IDDepartamentoArea
              INNER JOIN [01_UnidadesAsignadasAlPersonal] on [01_UnidadesAsignadasAlPersonal].RFC=PERSONAL.RFC
              INNER JOIN [01_Unidades] on [01_Unidades].IDUnidad=[01_UnidadesAsignadasAlPersonal].IDUnidad
              INNER JOIN [01_NivelesEstudioYGradosObtenidos] on [01_NivelesEstudioYGradosObtenidos].RFC = PERSONAL.RFC
                                                                AND [01_NivelesEstudioYGradosObtenidos].EsElGradoConcluidoMasAlto = 1
              INNER JOIN [01_CategoriasAdministrativasObtenidas] as CAT on PERSONAL.RFC = CAT.RFC and CAT.EsLaCategoriaAdministrativaActual = 1
              INNER JOIN [01_ClasificacionesDeCategorias] as CLAS on CAT.IDCategoriaAdministrativa = CLAS.IDCategoria
              LEFT OUTER JOIN [01_PuestosLaboralesObtenidos] as PLO on PLO.RFC = PERSONAL.RFC AND PLO.EsUnPuestoActual=1
              LEFT OUTER JOIN [01_PermisosTomados] AS PT ON PT.RFC = PERSONAL.RFC AND PT.FechaInicio <= GETDATE()
                AND PT.FECHATERMINO > GETDATE()
              LEFT OUTER JOIN [01_ClasificacionesDePermisos] ON PT.TipoDePermiso = [01_ClasificacionesDePermisos].TipoPermiso
              LEFT OUTER JOIN [01_NivelesSNIObtenidos] as SNI on PERSONAL.RFC = SNI.RFC and SNI.EsElNivelSNIActual = 1
            WHERE PERSONAL.Estatus = 1
                  and [01_DepartamentosYAreasAsignadasAlPersonal].EsElDepartamentoActual = 1
                  and [01_DepartamentosYAreas].Estatus = 1
                  and [01_LineasYSubAreasAsignadasAlPersonal].EsLaLineaActual = 1
                  and [01_LineasYSubAreas].Estatus = 1
                  and [01_UnidadesAsignadasAlPersonal].EsLaUnidadActual = 1
                  and PERSONAL.rfc = ?";
            $stmt = $bdi->prepare($sql);
            $stmt->execute(array($this->rfc)); 
            $row = $this->copy_values($stmt->fetchAll());
            $bdi = null;
            $encoded = json_encode(array("data" => $row));
            return $encoded;
        }

        /**
         * Gets the publications this individual has in the database.
         *
         * Requirements Section D (phase 2).
         */
        public function getPublications() {
            if (!isset($this->id)) {
                $this->getId();
            }
            if (!isset($this->rfc)) {
                $this->getRFC();
            }
            $bdi = $this->getBDIConnection();
            $sql = "SELECT [02_Publicaciones].Cita, [02_Publicaciones].NombresDeLosAutores,
               [02_ClasificacionGlobalDeProductos].DescripcionTipoProducto,
               YEAR([02_Publicaciones].FechaDePublicacion) as PubYear,
               MONTH([02_Publicaciones].FechaDePublicacion) as PubMonth,
               convert(varchar, [02_Publicaciones].FechaDePublicacion, 3) as PubDate
               FROM[02_Publicaciones]
               INNER JOIN [02_PersonalPorPublicacion] ON [02_Publicaciones].IDPublicacion = [02_PersonalPorPublicacion].IDPublicacion
               INNER JOIN [02_ClasificacionGlobalDeProductos] on [02_ClasificacionGlobalDeProductos].IDTipoProducto = [02_Publicaciones].IDTipoProducto
               WHERE RFC = ? 
               ORDER BY [02_Publicaciones].FechaDePublicacion DESC";
            $stmt = $bdi->prepare($sql);
            $stmt->execute(array($this->rfc)); 
            $result = $this->copy_values($stmt->fetchAll());
            $bdi = null;
            $encoded = json_encode(array("publication"=>$result));
            return $encoded;
        }

        /**
         * Gets the Academics at Ecosur related to this individual by Linea.
         *
         * Requirements Section E.
         *
         * @return string
         */
        public function getRelated() {
            if (!isset($this->id)) {
                $this->getId();
            }
            if (!isset($this->rfc)) {
                $this->getRFC();
            }
            $bdi = $this->getBDIConnection();
            $sql = "SELECT Titulo, NombreAbreviado, IDPersona FROM [01_Personal] as PERSONAL
                INNER JOIN [01_LineasYSubAreasAsignadasAlPersonal] as ASSIGNED ON PERSONAL.RFC = ASSIGNED.RFC
                INNER JOIN [01_LineasYSubAreas] as LINEAS ON LINEAS.IDLineaSubArea = ASSIGNED.IDLineaSubArea
                WHERE NombreLineaSubArea in (
                  SELECT NombreLineaSubArea from [01_LineasYSubAreas] as LINEAS2
                      INNER JOIN [01_LineasYSubAreasAsignadasAlPersonal] as ASSIGNED2 ON ASSIGNED2.IDLineaSubArea = LINEAS2.IDLineaSubArea AND LINEAS2.estatus = 1
                      INNER JOIN [01_Personal] as PERSONAL ON ASSIGNED2.rfc = PERSONAL.RFC AND PERSONAL.Estatus = 1
                      WHERE PERSONAL.rfc = '$this->rfc')
                AND PERSONAL.Estatus = 1
                AND ASSIGNED.EsLaLineaActual = 1
                AND Lineas.estatus = 1
                AND Personal.rfc != ? 
                ORDER BY NombreAbreviado";
            $stmt = $bdi->prepare($sql);
            $stmt->execute(array($this->rfc)); 
            $result = $this->copy_values($stmt->fetchAll());
            $bdi = null;
            $encoded = json_encode(array("related" => $result));
            return $encoded;
        }



        /**
         * Gets the courses that this individual has given/taken in Posgrado.
         * Requirements Section F, item A.
         */
        public function getCursosDePosgrado() {
            if (!isset($this->id)) {
                $this->getId();
            }
            if (!isset ($this->rfc)) {
                $this->getRFC();
            }

            $pos = $this->getPosgradoConnection();
            $sql = "SELECT row_number() OVER (order by getdate()) as ROWID, NombreMateria, Programa, Unidad, Creditos,
                    Participacion, Substring(ProgramaCurso, 10, len(ProgramaCurso)) as URL, convert(varchar, filtrofecha, 3) as EndDate,
                    Year(filtrofecha) as Year FROM BasePosgrado.dbo.DDI_OfertaCursos WHERE CURSOCANCELADO = 0 AND CURSOCERRADO = 0
                    AND rfc =  ORDER BY filtrofecha DESC";
            $stmt = $pos->prepare($sql);
            $stmt->execute(array($this->rfc)); 
            $result = $this->copy_values($stmt->fetchAll());
            $pos = null;
            return json_encode(array("course" => $result));
        }

        /**
         * Gets the courses and workshops that this individual has given in other Posgrados
         * Requirements Section F, item B.
         */
        public function getCursosYTalleres() {
            if (!isset($this->id)) {
                $this->getId();
            }
            if (!isset ($this->rfc)) {
                $this->getRFC();
            }
            $bdi = $this->getBDIConnection();
            $sql = "SELECT NombreCurso, NivelCursoImpartido, Institucion, Lugar, Creditos
                    FROM [07_DocenciaNoValidada] WHERE Institucion != 'ECOSUR' AND RFC = ?";
            $stmt = $bdi->prepare($sql);
            $stmt->execute(array($this->rfc)); 
            $result = $this->copy_values($stmt->fetchAll());
            $bdi = null;
            return json_encode(array("course"=>$result));
        }


        /**
         * Gets the courses and workshops that this individual has given/participated in
         * as continuous education.
         *
         * Requirements Section F, item D.
         *
         */
        public function getEducacionContinua() {
            if (!isset($this->id)) {
                $this->getId();
            }
            if (!isset ($this->rfc)) {
                $this->getRFC();
            }
            $bdi = $this->getBDIConnection();
            $sql = "SELECT [08_EventosYFortalecimientos].IDEvenFort, TituloEvenFort, ImpactoEsperado, NombreContraparte, InstitucionesSinRegistro, HorasDuracion,
                    DescripcionTipoProducto, convert(varchar, FechaFin, 3) as EndDate, Year(FechaFin) as Year, SectorPrivado, SectorPublico, SectorSocial,
                    LugarRealizacion, Calificado, [08_InstitucionesPorEvenFort].IDContraparte
                    FROM [08_EventosYFortalecimientos]
                    INNER JOIN [08_ParticipantesPorEvenFort] on [08_ParticipantesPorEvenFort].IDEvenFort = [08_EventosYFortalecimientos].IDEvenFort
                    INNER JOIN [01_Personal] on [01_Personal].RFC = [08_ParticipantesPorEvenFort].RFC
                    INNER JOIN [02_ClasificacionGlobalDeProductos] on [02_ClasificacionGlobalDeProductos].IDTipoProducto = TipoEvenFort
                    LEFT JOIN [08_InstitucionesPorEvenFort] on [08_InstitucionesPorEvenFort].IDEvenFort = [08_ParticipantesPorEvenFort].IDEvenFort
                    LEFT JOIN [03_Contrapartes] ON [03_Contrapartes].IDContraparte = [08_InstitucionesPorEvenFort].IDContraparte
                    WHERE TipoEvenFort in (50,51) AND Calificado = 2
                    AND [01_Personal].RFC = ? order by FechaFin DESC";
            $stmt = $bdi->prepare($sql);
            $stmt->execute(array($this->rfc)); 
            $result = $this->copy_values($stmt->fetchAll());
            $bdi = null;
            $merged = $this->merge("IDEvenFort","NombreContraparte",$result);
            return json_encode(array("course"=>$merged));
        }

        /**
         * Gets the thesis that this individual has supervised (Licenciatura, Maestro, Doctorado).
         * Requirements Section F, item C.
         */
        public function getTesis() {
            if (!isset($this->id)) {
                $this->getId();
            }
            if (!isset($this->rfc)) {
                $this->getRFC();
            }
            $bdi = $this->getBDIConnection();
            /* Get all students who have completed work */
            $sql = "SELECT  [02_Tesis].NombresDeLosAlumnos AS 'Alumno', [02_Tesis].TituloDeLaTesis AS 'Tesis', [02_Tesis].Status AS 'Estatus',
                    [02_GradosParaTesis].DescripcionGradoObtenido As 'ProgramaCorto', [02_PersonalPorTesis].NivelDeParticipacionEnLaTesis AS 'Participacion',
                    convert(varchar,[02_Tesis].FechaDelExamen,3) as EndDate
                    FROM [02_Tesis]
                      INNER JOIN [02_GradosParaTesis] AS [02_GradosParaTesis] ON [02_Tesis].GradoObtenido=[02_GradosParaTesis].IDGradoObtenido
                      INNER JOIN [02_PersonalPorTesis] ON [02_Tesis].IDTesis=[02_PersonalPorTesis].IDTesis
                      WHERE [02_Tesis].Status='Concluida' AND [02_PersonalPorTesis].RFC = ? 
                      ORDER BY [02_Tesis].FechaDelExamen ASC, IDGradoObtenido";
            $stmt = $bdi->prepare($sql);
            $stmt->execute(array($this->rfc)); 
            $result1 = $this->copy_values($stmt->fetchAll());
            $bdi = null;
            /* Get ONLY the active students in the Posgrado DB */
            $pos = $this->getPosgradoConnection();
            $sql = "SELECT Alumno, Tesis, Estatus, ProgramaCorto, Participacion, convert(varchar, FechaDeTerminoDelPosgrado, 3) as EndDate
              from BasePosgrado.dbo.Z_cnsIsiAlumnosTutores as POSGRADO where POSGRADO.Estatus = 'Activo' AND 
              POSGRADO.RFCInvestigador = ? order by ProgramaCorto ASC, FechaDeTerminoDelPosgrado ASC;";
            $stmt = $pos->prepare($sql);
            $stmt->execute(array($this->rfc)); 
            $result2 = $this->copy_values($stmt->fetchAll());
            $pos = null;
            /* Reverse data structure to show active students first, ASC ordering should make this work properly */
            $reversed = array_reverse (array_merge($result1, $result2));
            return json_encode(array("student" => $reversed));
        }

        /**
         * Gets the projects that this individual has been involved with.
         *
         * Requirements Section G.
         *
         * @return string
         */
        public function getProjects() {
            if (!isset($this->id)) {
                $this->getId();
            }
            if (!isset($this->rfc)) {
                $this->getRFC();
            }
            $bdi = $this->getBDIConnection();
            $sql = "SELECT Proyectos.ClaveProyecto, Proyectos.Titulo, Proyectos.ObjetivoGeneral, NombreContraparte, Proyectos.status as Estatus,
                    Proyectos.Resumen, Proyectos.ImpactoEsperado, InstitucionesQueParticipan, PaginaWeb,
                    Year(Proyectos.FechaInicio) as StartDate,
                    Year(Proyectos.FechaTermino) as EndDate,
                    Proyectos.FechaTermino from [01_Personal] as PERSONAL
                    INNER JOIN [04_PersonalPorProyecto] as PPP on PPP.claveparticipante = PERSONAL.RFC
                    INNER JOIN [04_Proyectos] AS PROYECTOS ON PROYECTOS.ClaveProyecto = PPP.ClaveProyecto
                    INNER JOIN [03_Contrapartes] ON [03_Contrapartes].IDContraparte = Proyectos.EntidadFinanciadora
                    WHERE Personal.rfc = ? order by Proyectos.Status ASC, Proyectos.FechaTermino DESC, Proyectos.FechaInicio DESC";
            $stmt = $bdi->prepare($sql);
            $stmt->execute(array($this->rfc)); 
            $result = $this->copy_values($stmt->fetchAll());
            $bdi = null;
            return json_encode(array("project"=>$result));
        }

        /**
         * Merges all records with the id column, "id",
         * that have duplicates in the column, "duplicateCol"
         * into single records.
         * @param data
         * @param dateParameter
         * @constructor
         */
        private function merge($idCol, $duplicateCol, $data) {
            $merge = array();
            for ($i = 0; $i < count($data); $i++) {
                $key = "key-" . strval($data[$i][$idCol]);
                if (array_key_exists($key, $merge)) {
                    $stored = $merge[$key];
                    $current = $data[$i];
                    $stored[$duplicateCol] = $stored[$duplicateCol] . ", " . $current[$duplicateCol];
                    $merge[$key] = $stored;
                } else {
                    $merge[$key] = $data[$i];
                }
            }
            /*
               Spool out key based array into 0 based dictonary
            */
            $ret = array();
            $counter = 0;
            foreach ($merge as $key=>$value) {
                $ret[$counter++] = $value;
            }
            return $ret;
        }


        /*
            Converts all values in the associative array, $array, to utf8.
            (Utility function).
        */
        private function copy_values($array) {
            if (! $array)
                return $array;
            $copy = array();
            $keys = array_keys($array);
            foreach ($keys as $key) {
                $copy[$key] = $array[$key];
            }
            return $copy;
        }

        /* Gets a connection to the BDI MSSQL Server database */
        private function getBDIConnection() {
            require 'dbconfig.php';
            $resource = null;
            try {
                $resource = new PDO($bdi['url'], $bdi['user'], $bdi['password']);
            } catch(PDOException $e) {
                error_log('BDI PDO Connection Exception!',0);
                error_log($e->getMessage(), 0);
                error_log($e->getTraceAsString(), 0);
            }
            return $resource;
        }

        /* Gets a connection to the Posgrado MSSQL Server database */
        private function getPosgradoConnection() {
            require 'dbconfig.php';
            $resource = null;
            try {
                $resource = new PDO($posgrado['url'], $posgrado['user'], $posgrado['password']);
            } catch (PDOException $e) {
                error_log('POSGRADO Connection Exception!', 0);
                error_log($e->getMessage(), 0);
                error_log($e->getTraceAsString(), 0);
            }
            return $resource;
        }
    }
