var body = $("html, body");

var app = window.angular.module('student', ['ngResource']);

app.controller('studentController', ['$scope', '$http', '$window', '$rootScope',
    function ($scope, $http, $window, $rootScope) {

        var mensajes = [
            { alerta: "alert alert-info", icon: "fa fa-check fa-3x", mensaje: "La calificación obtenida en este ciclo es igual al promedio  del grupo." },
            { alerta: "alert alert-success", icon: "fa fa-line-chart fa-3x", mensaje: "¡Felicidades! La calificación obtenida en este ciclo es más alta que el promedio  del resto del grupo." },
            { alerta: "alert alert-danger", icon: "fa fa-warning fa-3x", mensaje: "La calificación obtenida en este ciclo es inferior que el promedio  del grupo, se debe dar un seguimiento al alumno para mejorar las notas del próximo ciclo." },
            { alerta: "alert alert-warning", icon: "fa fa-question fa-3x ", mensaje: "No se cuenta con la información del grado." }];

        var curp;
        $scope.cifraAnual=null;
        $scope.cifraAnualPeriodo = "";
        $scope.cifraAnualAlumnosGlobales=0;
        $scope.cifraAnualPreliminar = 0;

        $scope.searchAttempt = 0;
        $scope.errorWhileConnecting = 0;
        $scope.studentsFound = 0;

        $scope.studentResponse = null;


        $scope.getAlumnosGlobales = function () {
            //Verificar info estadistica global
            $scope.cifraAnual = sessionStorage.getItem("cifraAnual");
            console.log("Buscando valor de alumnos." + $scope.cifraAnual);
            if ($scope.cifraAnual === null) {
                //Buscar en API el valor de alumnos,docentes y escuelas
                this.getTotales();
                
            } else {
                $scope.cifraAnualAlumnosGlobales = Intl.NumberFormat('en-US').format(JSON.parse($scope.cifraAnual).totalAlumnos);
                $scope.cifraAnualPeriodo = JSON.parse($scope.cifraAnual).periodo;
                $scope.cifraAnualPreliminar = JSON.parse($scope.cifraAnual).preliminar;
            }          
        };

        $scope.getTotales = function () {
            $http.get(
                $scope.getURLEstadistica()
                + 'cifraAnual')
                .success(function (data) {
                    
                    $scope.cifraAnual = JSON.stringify(data.cifraAnualDTO[0]);
                    sessionStorage.setItem("cifraAnual", $scope.cifraAnual);
                    $scope.cifraAnualAlumnosGlobales = Intl.NumberFormat('en-US').format(JSON.parse($scope.cifraAnual).totalAlumnos);
                    $scope.cifraAnualPeriodo = JSON.parse($scope.cifraAnual).periodo;

                    console.log('datos cifraAnual');
                    console.log(data);

                });
        };
        
        $scope.getURLEstadistica = function () {
            //
            return global_uri +'/Estadisticas/';
        };

        $scope.schoolCyclesByStudent = function () {
            $scope.searchAttempt = 1;
            $scope.errorWhileConnecting = 0;
            $scope.studentsFound = 0;
            $scope.processing = 0;


            curp = $scope.curp;
            var prom;

            curp = curp.toUpperCase();

            var expreg = /^([A-Z]{4})([0-9]{6})([HM]{1})(AS|BC|BS|CC|CS|CH|CL|CM|DF|DG|GT|GR|HG|JC|MC|MN|MS|NT|NL|OC|PL|QT|QR|SP|SL|SR|TC|TS|TL|VZ|YN|ZS|NE)[B-DF-HJ-NP-TV-Z]{3}[0-9A-Z]{1}[0-9]{1}$/;

            if (expreg.test(curp)) {

                processing();
                
                $("#btnResetForm").removeClass("disabled");
                $http({
                    url: global_uri + '/alumno/schoolCyclesByStudent/curp=' + curp,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }

                }).success(function (data) {

                    $scope.resultWithData = 1;

                    if(data.students !== undefined && data.students.length > 0){
                        $scope.studentsFound = data.students.length;
                        $scope.studentResponse = data;
                        /*
                        $scope.studentResponse.students.forEach(function (student) {
                            if(student.schoolCyclesDTO !== undefined && student.schoolCyclesDTO.length > 0){
                                console.log("entro a ");
                            }
                        });
                        */
                    }

                    noprocessing();

                }).error(function (error) {
                    
                    console.log("error");
                    
                    noprocessing();
                    $scope.errorWhileConnecting = 1;

                    $('html, body').animate({
                        scrollTop: $("#sectionErrorWhileConnecting").offset().top - 50
                    }, 1000);
                });

            } else {
                console.log("La CURP NO es correcta");
                $("#modalCurpError").modal('show');
            }

        };


        $scope.showEvaluationsByStudentCycle = function (idStudentCycle) {

            $('#divEvaluationsNotFound'+idStudentCycle+' div').remove();
            $('#divEvaluationsTable'+idStudentCycle+' table').remove();
            $('#spinner'+idStudentCycle).show();

            $http({
                url: global_uri + '/alumno/evaluationsByStudentCycle/idStudentCycle=' + idStudentCycle,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }

            }).success(function (data) {

                if(data.evaluationStudentCycle !== undefined
                    && data.evaluationStudentCycle.length > 0
                    && data.evaluationStudentCycle[0].evaluationPeriodsDTO !== undefined
                    && data.evaluationStudentCycle[0].evaluationPeriodsDTO.length > 0)
                {
                        
                    $scope.evaluationsFound = data.evaluationStudentCycle.length;
                    $scope.evaluationResponse = data;


                    var periods = $scope.evaluationResponse.evaluationStudentCycle[0].evaluationPeriodsDTO;
                    $scope.subjectsAssesedFullList = [];

                    //Crea en el DOM la tabla de evaluaciones.
                    $('#divEvaluationsTable'+idStudentCycle).append("<table id=\"evaluationsTable"+idStudentCycle+"\" class=\"table table-striped table-responsive table-calificaciones \"><thead><tr><th><span>Materia</span></th></tr></thead><tbody></tbody></table>");

                    //Obtiene el listado de materias.
                    periods.forEach(function (period) {
                        //Crea la fila con los títulos de los periodos.
                        $('#evaluationsTable'+idStudentCycle+' thead tr').append("<th><span>"+period.period+"</span></th>");

                        period.subjectsAssesedDTO.forEach(function (subjectAssesed) {
                            var addSubjectsAssesed = true;
                            $scope.subjectsAssesedFullList.forEach(function (subjectAssesedFullList) {
                                if ($scope.subjectsAssesedFullList.length > 0) {
                                    if (subjectAssesedFullList.subject == subjectAssesed.subject) {
                                        addSubjectsAssesed = false;
                                    }
                                }
                            });

                            if (addSubjectsAssesed) {
                                $scope.subjectsAssesedFullList.push({ 'subject': subjectAssesed.subject });
                            }
                        });
                    });

                    //Para cada materia, busca la calificación en cada uno de los periodos para insertar la fila en la tabla de evaluaciones.
                    $scope.subjectsAssesedFullList.forEach(function (subjectAssesedFullList,i) {
                        //Crea la fila de la materia.
                        $('#evaluationsTable'+idStudentCycle+' tbody').append("<tr id=\"tr"+i+idStudentCycle+"\"><td><span>"+subjectAssesedFullList.subject+"</span></td></tr>");
                        //Busca la calificación que corresponde y la agrega a la fila con el orden de los periodos.
                        periods.forEach(function (period,j) {
                            var subjectGradeFound = 0;
                            period.subjectsAssesedDTO.forEach(function (subjectAssesed,k) {
                                if (subjectAssesed.subject == subjectAssesedFullList.subject) {
                                    $('#tr'+i+idStudentCycle).append("<td><span>"+subjectAssesed.subjectGrade+"</span></td>");
                                    subjectGradeFound = 1;
                                }
                            });
                            if(subjectGradeFound == 0){
                                $('#tr'+i+idStudentCycle).append("<td><span>-</span></td>");
                            }
                        });
                    });

                }else{
                    $('#divEvaluationsNotFound'+idStudentCycle).append("<div class=\"alert alert-danger\"><p><span>Las calificaciones no se encuentran en nuestros registros.</span></p></div>");
                }

                $('#spinner'+idStudentCycle).hide();

            }).error(function (error) {
                console.log("error");
            });

        }

        $scope.showEvaluationsByStudentCycleOnTabs = function (idStudentCycle, idStudent) {

            $('#divEvaluationsNotFound'+idStudent+' div').remove();
            $('#divEvaluationsTable'+idStudent+' table').remove();
            $('#divDetailCycle'+idStudent+' div').remove();

            $('#spinner'+idStudent).show();

            $http({
                url: global_uri + '/alumno/evaluationsByStudentCycle/idStudentCycle=' + idStudentCycle,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }

            }).success(function (data) {

                var preescolar = 0;

                $scope.studentResponse.students.forEach(function (student) {
                    if (student.id == idStudent) {
                        if (student.schoolCyclesDTO.length > 0) {
                            student.schoolCyclesDTO.forEach(function (schoolCycle) {
                                if(schoolCycle.idStudentCycle == idStudentCycle){
                               
                                    if (schoolCycle.level == "PREESCOLAR"){
                                        preescolar = 1;
                                    }
                                    
                                    var divAvgAcreditation = "";


                                    if(schoolCycle.avgAcreditation > 0){
                                        divAvgAcreditation = "<div class=\"column col-lg-2 alert-datos-incial alert alert-warning text-center  dato-ciclo\">"
                                                    +"<p><span>Promedio</span></p>"
                                                    +"<p><b><span>"+schoolCycle.avgAcreditation
                                                    +"</span></b></p>"
                                                +"</div>"
                                    }

                                    $('#divDetailCycle'+idStudent).append(
                                        "<div class=\"row justify-content-center\" style=\"margin-bottom:0px\">"
                                                +"<div class=\"column col-lg-2 alert-datos-incial alert alert-info text-center  dato-ciclo\">"
                                                    +"<p><span>Ciclo</span></p>"
                                                    +"<p><b><span>"+schoolCycle.schoolCycle+"</span></b></p>"
                                                +"</div>"
                                                +"<div class=\"column col-lg-2 alert-datos-incial alert alert-success text-center  dato-ciclo\">"
                                                    +"<p><span>Nivel</span></p>"
                                                    +"<p><b><span>"+schoolCycle.level+"</span></b></p>"
                                                +"</div>"
                                                +"<div class=\"column col-lg-2 alert-datos-incial alert alert-warning text-center  dato-ciclo\">"
                                                    +"<p><span>Turno</span></p>"
                                                    +"<p><b><span>"+schoolCycle.shift+"</span></b></p>"
                                                +"</div>"
                                                +"<div class=\"column col-lg-2 alert-datos-incial alert alert-info text-center  dato-ciclo\">"
                                                    +"<p><span>Grado</span></p>"
                                                    +"<p><b><span>"+schoolCycle.grade+"</span></b></p>"
                                                +"</div>"
                                                +"<div class=\"column col-lg-2 alert-datos-incial alert alert-success text-center  dato-ciclo\">"
                                                    +"<p><span>Grupo</span></p>"
                                                    +"<p><b><span>"+schoolCycle.group+"</span></b></p>"
                                                +"</div>"
                                                +divAvgAcreditation
/*
                                                +"<div class=\"col-md-2 alert-datos-incial alert alert-info text-center  dato-ciclo\">"
                                                    +"<p><span>Actualización</span></p>"
                                                    +"<p><b><span>"+schoolCycle.updatedAt+"</span></b></p>"
                                                +"</div>"
*/
                                        +"</div>");
                                }
                            }); 
                        }
                    }
                });



                if(data.evaluationStudentCycle !== undefined
                    && data.evaluationStudentCycle.length > 0
                    && data.evaluationStudentCycle[0].evaluationPeriodsDTO !== undefined
                    && data.evaluationStudentCycle[0].evaluationPeriodsDTO.length > 0)
                {
                        
                    $scope.evaluationsFound = data.evaluationStudentCycle.length;
                    $scope.evaluationResponse = data;


                    var periods = $scope.evaluationResponse.evaluationStudentCycle[0].evaluationPeriodsDTO;
                    $scope.subjectsAssesedFullList = [];

                    //Crea en el DOM la tabla de evaluaciones.
                    $('#divEvaluationsTable'+idStudent).append("<table id=\"evaluationsTable"+idStudent+"\" class=\"table table-striped table-responsive table-calificaciones\"><thead><tr><th><span>Materia</span></th></tr></thead><tbody></tbody></table>");

                    //Obtiene el listado de materias.
                    periods.forEach(function (period) {
                        //Crea la fila con los títulos de los periodos.
                        $('#evaluationsTable'+idStudent+' thead tr').append("<th><span>"+period.period+"</span></th>");

                        period.subjectsAssesedDTO.forEach(function (subjectAssesed) {
                            var addSubjectsAssesed = true;
                            $scope.subjectsAssesedFullList.forEach(function (subjectAssesedFullList) {
                                if ($scope.subjectsAssesedFullList.length > 0) {
                                    if (subjectAssesedFullList.subject == subjectAssesed.subject) {
                                        addSubjectsAssesed = false;
                                    }
                                }
                            });

                            if (addSubjectsAssesed) {
                                $scope.subjectsAssesedFullList.push({ 'subject': subjectAssesed.subject });
                            }
                        });
                    });

                    //Para cada materia, busca la calificación en cada uno de los periodos para insertar la fila en la tabla de evaluaciones.
                    $scope.subjectsAssesedFullList.forEach(function (subjectAssesedFullList,i) {
                        //Crea la fila de la materia.
                        $('#evaluationsTable'+idStudent+' tbody').append("<tr id=\"tr"+i+idStudent+"\"><td><span>"+subjectAssesedFullList.subject+"</span></td></tr>");
                        //Busca la calificación que corresponde y la agrega a la fila con el orden de los periodos.
                        periods.forEach(function (period,j) {
                            var subjectGradeFound = 0;
                            period.subjectsAssesedDTO.forEach(function (subjectAssesed,k) {
                                if (subjectAssesed.subject == subjectAssesedFullList.subject) {
                                    console.log("preescolar:"+preescolar);
                                    if(preescolar == 1){
                                        $('#tr'+i+idStudent).append("<td><span>-</span></td>");                                    
                                    }else{
                                        $('#tr'+i+idStudent).append("<td><span>"+subjectAssesed.subjectGrade+"</span></td>");
                                    }
                                    subjectGradeFound = 1;
                                }
                            });
                            if(subjectGradeFound == 0){
                                $('#tr'+i+idStudent).append("<td><span>-</span></td>");
                            }
                        });
                    });

                }else{
                    $('#divEvaluationsNotFound'+idStudent).append("<div class=\"alert alert-danger\"><p><span>Las calificaciones no se encuentran en nuestros registros.</span></p></div>");
                }


                $('#spinner'+idStudent).hide();

            }).error(function (error) {
                console.log("error");
            });

        };

        $scope.alerta = function (estadisticas, prom, grado) {
            console.log(prom);
            console.log(grado);
            console.log(estadisticas);

            var mensajes = [
                { alerta: "alert alert-info", icon: "fa fa-check fa-3x", mensaje: "La calificación obtenida en este ciclo es igual al promedio  del grupo." },
                { alerta: "alert alert-success", icon: "fa fa-line-chart fa-3x", mensaje: "¡Felicidades! La calificación obtenida en este ciclo es más alta que el promedio  del resto del grupo." },
                { alerta: "alert alert-danger", icon: "fa fa-warning fa-3x", mensaje: "La calificación obtenida en este ciclo es inferior que el promedio  del grupo, se debe dar un seguimiento al alumno para mejorar las notas del próximo ciclo." },
                { alerta: "alert alert-warning", icon: "fa fa-question fa-3x ", mensaje: "No se cuenta con la información del grado." }];

            $scope.msjstd = mensajes[3].mensaje;
            $scope.class_alerta = mensajes[3].alerta;
            $scope.icon_alerta = mensajes[3].icon;

            prom = parseFloat(prom);
            estadisticas.forEach(function (gradoEstadistica) {
                if (gradoEstadistica.grado == grado) {
                    if (parseFloat(gradoEstadistica.promedio) < prom) {
                        $scope.msjstd = mensajes[1].mensaje;
                        $scope.class_alerta = mensajes[1].alerta;
                        $scope.icon_alerta = mensajes[1].icon;
                    } else if (parseFloat(gradoEstadistica.promedio) == prom) {
                        $scope.msjstd = mensajes[0].mensaje;
                        $scope.class_alerta = mensajes[0].alerta;
                        $scope.icon_alerta = mensajes[0].icon;

                    } else if (parseFloat(gradoEstadistica.promedio) > prom) {
                        $scope.msjstd = mensajes[2].mensaje;
                        $scope.class_alerta = mensajes[2].alerta;
                        $scope.icon_alerta = mensajes[2].icon;
                    }

                }

            });


        };


        $scope.newSearch = function () {
            $scope.searchAttempt = 0;
            $errorWhileConnecting = 0;
            $("#sectionErrorWhileConnecting").hide();
            $("#sectionResultWithoutData").hide();
            $scope.studentsFound = 0;

            
            console.log("newSearch1102");
        };



        var processing = function () {
            $scope.processing = 1;

            $("#modal-loading").modal('show');
            $('.tiempo_fuera').hide();
            setTimeout(revisaModal, 5000);

        };

        var noprocessing = function () {
            $scope.processing = 0;
            $("#modal-loading").modal('hide');
        };

        var revisaModal = function () {
            /*if (($("#modal-loading").data('bs.modal') || {}).isShown) {*/
            if($("#modal-loading").is(":visible")){
                $('.tiempo_fuera').show();
            }
        };

    }]
);
