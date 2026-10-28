//var app = angular.module("indexApp", ["ngResource"]);

app.controller("searchReferenceCtrl", [
    "$scope",
    "$http",
    "$window",
    "$rootScope",
    function ($scope, $http, $window, $rootScope) {
        $http.defaults.headers.get = { "Content-Type": "application/x-www-form-urlencoded;charset=utf-8" };

        $scope.showFinder = true;
        $scope.showMoreLevels = true;
        $scope.showSchedules = true;
        $scope.showPagination = false;
        $scope.numberOfReferenceRecords = null;
        $scope.referencePages = null;
        $scope.lastPage = null;
        $scope.referenceNumberOfPages = null;
        $scope.stats = null;
        $scope.searchButtons = true;
        $scope.showTeachers = false;
        $scope.detailContact = null;

        $.fn.dataTable.ext.errMode = "none";

        $scope.callbackFunction = function () {
            console.log("Cargó callbackFunction");
        };
        /*FUNCIONES en el index
         */
        //
        $scope.getURLReferencias = function () {
            return global_uri + "/referencia/consultaReferencias";
        };
        $scope.getURLArchivos = function () {
            return global_uri + "/archivo/buscarArchivos/grupo=";
        };
        //
        $scope.getURLDescargaArchivos = function (pGrupo, pId) {
            if (pGrupo === undefined) {
                pGrupo = "";
            }
            if (pId === undefined) {
                pId = "";
            }
            return global_uri + "/archivo/buscarArchivos/grupo=" + pGrupo + "&id=" + pId;
        };
        //
        $scope.showContacto = function (contacto) {
            $http
                .get($scope.getURLReferencias() + "/id=" + contacto.idEnlace)
                .success(function (data) {
                    $scope.detailContact = contacto;
                    console.log(data);
                })
                .error(function (error) {
                    $scope.detailContact = contacto;
                    console.log("Error:");
                    console.log(error);
                });

            $("#modal_datos_enlaces").modal("show");
            //console.log ("Pintando valor de detalleContacto:");
            //console.log ($scope.detailContact);
        };
        //
        $scope.getReferencias = function () {
            var totalRecords = 0;

            var table = $("#tblEnlaces").DataTable({});

            //if (table != undefined)
            table.destroy();

            //console.log($scope.getURLSchools() + parametros + '&primer=1' + '&ultimo=1000');

            table = $("#tblEnlaces").DataTable({
                responsive: true,
                language: {
                    lengthMenu: "Mostrar _MENU_ registros por página.",
                    zeroRecords: "No se encontraron registros.",
                    info: " ",
                    //"infoEmpty": "No hay registros aún.",
                    //"infoFiltered": "(filtrados de un total de _MAX_ registros)",
                    infoEmpty: " ",
                    infoFiltered: "(filtrado de un total de _MAX_ registros)",
                    search: "Búsqueda",
                    LoadingRecords: "Cargando ...",
                    Processing: "Procesando...",
                    SearchPlaceholder: "Comience a teclear...",
                    iDisplayLength: 20,
                    sPaginationType: "full_numbers",
                    sServerMethod: "POST",
                    aLengthMenu: [
                        [5, 10, 15, 20, -1],
                        [5, 10, 15, 20, "All"]
                    ],
                    //"iDisplayLength": 20,
                    paginate: {
                        previous: "Anterior",
                        next: "Siguiente"
                    }
                },
                searching: true,
                bLengthChange: true,
                ajax: {
                    // "url": $scope.getURLSchools() + parametros,
                    url: $scope.getURLReferencias() + "/id=",
                    beforeSend: function (request) {
                        request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded;charset=utf-8");
                    },
                    data: function (data) {
                        //console.info(data)
                        //processing();
                    },
                    dataSrc: function (d) {
                        console.log("resolvió el rest de busqueda");
                        //noprocessing();
                        //console.log('resultado de la busqueda:');
                        //console.log(d);

                        //si no encuentra los datos
                        $("html, body").animate(
                            {
                                //scrollTop: 290
                            },
                            1000
                        );
                        return d.datos;
                    }
                },
                columns: [
                    { name: "entidad", data: "entidad" },
                    { name: "rol", data: "rolSiged" },
                    { name: "nombre", data: "nombre" },
                    { name: "Puesto", data: "puesto" },
                    { name: "Actualizado", data: "fechaActualizacion" }
                ]
            });

            $("#tblEnlaces tbody").unbind("click");

            $("#tblEnlaces tbody").on("click", "tr", function () {
                var contacto = table.row(this).data();
                //console.log(this);

                $scope.showContacto(contacto);
            });
        };

        $scope.descargarArchivo = function (pIdArchivo) {
            console.log("Id Archivo 1258 : " + pIdArchivo);
            $scope.processing();
            $http({
                method: "get",
                url: $scope.getURLArchivos() + "CCTS&id=" + pIdArchivo,
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json"
                }
            })
                .success(function (data) {
                    console.log(data);
                    blobB64 = $scope.b64toBlob(data.datos[0].base64, "", 512);
                    console.log(blobB64);
                    var file = new Blob([blobB64], { type: data.datos[0].type });
                    saveAs(file, data.datos[0].name);
                    $scope.noprocessing();
                })
                .error(function (error) {
                    console.log(error);
                    $scope.fileNotFound();
                });
        };
        $scope.b64toBlob = function (b64Data, contentType, sliceSize) {
            sliceSize = 512;
            const byteCharacters = atob(b64Data);
            const byteArrays = [];

            for (let offset = 0; offset < byteCharacters.length; offset += sliceSize) {
                const slice = byteCharacters.slice(offset, offset + sliceSize);

                const byteNumbers = new Array(slice.length);
                for (let i = 0; i < slice.length; i++) {
                    byteNumbers[i] = slice.charCodeAt(i);
                }

                const byteArray = new Uint8Array(byteNumbers);
                byteArrays.push(byteArray);
            }

            const blob = new Blob(byteArrays, { type: contentType });
            return blob;
        };

        $scope.getArchivosDescargaHistorica = function () {
            var table = $("#tblDescargas").DataTable({});

            //if (table != undefined)
            table.destroy();

            //console.log($scope.getURLSchools() + parametros + '&primer=1' + '&ultimo=1000');

            table = $("#tblDescargasHistorico").DataTable({
                responsive: true,
                language: {
                    lengthMenu: "Mostrar _MENU_ registros por página.",
                    zeroRecords: "No se encontraron registros.",
                    info: " ",
                    //"infoEmpty": "No hay registros aún.",
                    //"infoFiltered": "(filtrados de un total de _MAX_ registros)",
                    infoEmpty: " ",
                    infoFiltered: "(filtrado de un total de _MAX_ registros)",
                    search: "Búsqueda",
                    LoadingRecords: "Cargando ...",
                    Processing: "Procesando...",
                    SearchPlaceholder: "Comience a teclear...",
                    iDisplayLength: 20,
                    sPaginationType: "full_numbers",
                    sServerMethod: "POST",
                    aLengthMenu: [
                        [10, 15, 20, -1],
                        [10, 15, 20, "All"]
                    ],
                    //"iDisplayLength": 20,
                    paginate: {
                        previous: "Anterior",
                        next: "Siguiente"
                    }
                },
                searching: true,
                bLengthChange: true,
                ajax: {
                    // "url": $scope.getURLSchools() + parametros,
                    url: $scope.getURLArchivos() + "INIFED&id=",
                    beforeSend: function (request) {
                        request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded;charset=utf-8");
                    },
                    data: function (data) {
                        //console.info(data)
                        //processing();
                    },
                    dataSrc: function (d) {
                        console.log("resolvió el rest de busqueda archivos descarga");
                        //noprocessing();
                        //console.log('resultado de la busqueda:');
                        //console.log(d);

                        //si no encuentra los datos
                        $("html, body").animate(
                            {
                                //scrollTop: 290
                            },
                            1000
                        );
                        return d.datos;
                    }
                },
                columns: [
                    { className: "col-md-6 col-lg-6", name: "Descripción", data: "descripcion" },
                    { className: "col-md-3 col-lg-3", name: "Fecha de Actualización", data: "fileUpdate" },
                    {
                        className: "col-md-3 col-lg-3",
                        name: "Descarga",
                        data: "name",
                        defaultContent: '<i class="fa fa-pencil"/>',
                        className: "row-edit dt-center"
                    }
                ]
            });

            $("#tblDescargasHistorico tbody").unbind("click");

            $("#tblDescargasHistorico tbody").on("click", "tr", function () {
                console.log("Descargar archivo tblDescargasHistorico");
                var archivo = table.row(this).data();
                $scope.descargarArchivo(archivo.idFile);
            });
        };

        $scope.getArchivosDescarga = function () {
            var table = $("#tblDescargas").DataTable({});

            //if (table != undefined)
            table.destroy();

            //console.log($scope.getURLSchools() + parametros + '&primer=1' + '&ultimo=1000');

            table = $("#tblDescargas").DataTable({
                responsive: true,
                language: {
                    lengthMenu: "Mostrar _MENU_ registros por página.",
                    zeroRecords: "No se encontraron registros.",
                    info: " ",
                    //"infoEmpty": "No hay registros aún.",
                    //"infoFiltered": "(filtrados de un total de _MAX_ registros)",
                    infoEmpty: " ",
                    infoFiltered: "(filtrado de un total de _MAX_ registros)",
                    search: "Búsqueda",
                    LoadingRecords: "Cargando ...",
                    Processing: "Procesando...",
                    SearchPlaceholder: "Comience a teclear...",
                    iDisplayLength: 20,
                    sPaginationType: "full_numbers",
                    sServerMethod: "POST",
                    aLengthMenu: [
                        [10, 15, 20, -1],
                        [10, 15, 20, "All"]
                    ],
                    //"iDisplayLength": 20,
                    paginate: {
                        previous: "Anterior",
                        next: "Siguiente"
                    }
                },
                searching: true,
                bLengthChange: true,
                ajax: {
                    // "url": $scope.getURLSchools() + parametros,
                    url: $scope.getURLArchivos() + "CCTS&id=",
                    beforeSend: function (request) {
                        request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded;charset=utf-8");
                    },
                    data: function (data) {
                        //console.info(data)
                        //processing();
                    },
                    dataSrc: function (d) {
                        console.log("resolvió el rest de busqueda archivos descarga");
                        //noprocessing();
                        //console.log('resultado de la busqueda:');
                        //console.log(d);

                        //si no encuentra los datos
                        $("html, body").animate(
                            {
                                //scrollTop: 290
                            },
                            1000
                        );
                        return d.datos;
                    }
                },
                columns: [
                    { className: "col-md-6 col-lg-6", name: "Descripción", data: "descripcion" },
                    { className: "col-md-3 col-lg-3", name: "Fecha de Actualización", data: "fileUpdate" },
                    {
                        className: "col-md-3 col-lg-3",
                        name: "Descarga",
                        data: "name",
                        defaultContent: '<i class="fa fa-pencil"/>',
                        className: "row-edit dt-center"
                    }
                ]
            });

            $("#tblDescargas tbody").unbind("click");

            $("#tblDescargas tbody").on("click", "tr", function () {
                console.log("Descargar archivo CCTS");
                var archivo = table.row(this).data();
                $scope.descargarArchivo(archivo.idFile);
            });
        };

        $scope.getArchivosDescargaCenso = function () {
            var table = $("#tblDescargasCenso").DataTable({});

            //if (table != undefined)
            table.destroy();

            //console.log($scope.getURLSchools() + parametros + '&primer=1' + '&ultimo=1000');

            table = $("#tblDescargasCenso").DataTable({
                responsive: true,
                language: {
                    lengthMenu: "Mostrar _MENU_ registros por página.",
                    zeroRecords: "No se encontraron registros.",
                    info: " ",
                    //"infoEmpty": "No hay registros aún.",
                    //"infoFiltered": "(filtrados de un total de _MAX_ registros)",
                    infoEmpty: " ",
                    infoFiltered: "(filtrado de un total de _MAX_ registros)",
                    search: "Búsqueda",
                    LoadingRecords: "Cargando ...",
                    Processing: "Procesando...",
                    SearchPlaceholder: "Comience a teclear...",
                    iDisplayLength: 20,
                    sPaginationType: "full_numbers",
                    sServerMethod: "POST",
                    aLengthMenu: [
                        [10, 15, 20, -1],
                        [10, 15, 20, "All"]
                    ],
                    //"iDisplayLength": 20,
                    paginate: {
                        previous: "Anterior",
                        next: "Siguiente"
                    }
                },
                searching: true,
                bLengthChange: true,
                ajax: {
                    // "url": $scope.getURLSchools() + parametros,
                    url: $scope.getURLArchivos() + "CEMABE&id=",
                    beforeSend: function (request) {
                        request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded;charset=utf-8");
                    },
                    data: function (data) {
                        //console.info(data)
                        //processing();
                    },
                    dataSrc: function (d) {
                        console.log("resolvió el rest de busqueda archivos descarga");
                        //noprocessing();
                        //console.log('resultado de la busqueda:');
                        //console.log(d);

                        //si no encuentra los datos
                        $("html, body").animate(
                            {
                                //scrollTop: 290
                            },
                            1000
                        );
                        return d.datos;
                    }
                },
                columns: [
                    { className: "col-md-6 col-lg-6", name: "Descripción", data: "descripcion" },
                    { className: "col-md-3 col-lg-3", name: "Fecha de Actualización", data: "fileUpdate" },
                    {
                        className: "col-md-3 col-lg-3",
                        name: "Descarga",
                        data: "name",
                        defaultContent: '<i class="fa fa-pencil"/>',
                        className: "row-edit dt-center"
                    }
                ]
            });

            $("#tblDescargasCenso tbody").unbind("click");

            $("#tblDescargasCenso tbody").on("click", "tr", function () {
                console.log("Descargar archivo ");
                var archivo = table.row(this).data();
                $scope.descargarArchivo(archivo.idFile);
            });
        };
        ///////////////
        $scope.getArchivosDescargaIndicadores = function () {
            console.log("Entra funcion DescargaIndicadores");
            var table = $("#tblDescargasIndicadores").DataTable({});

            //if (table != undefined)
            table.destroy();

            //console.log($scope.getURLSchools() + parametros + '&primer=1' + '&ultimo=1000');

            table = $("#tblDescargasIndicadores").DataTable({
                responsive: true,
                language: {
                    lengthMenu: "Mostrar _MENU_ registros por página.",
                    zeroRecords: "No se encontraron registros.",
                    info: " ",
                    //"infoEmpty": "No hay registros aún.",
                    //"infoFiltered": "(filtrados de un total de _MAX_ registros)",
                    infoEmpty: " ",
                    infoFiltered: "(filtrado de un total de _MAX_ registros)",
                    search: "Búsqueda",
                    LoadingRecords: "Cargando ...",
                    Processing: "Procesando...",
                    SearchPlaceholder: "Comience a teclear...",
                    iDisplayLength: 20,
                    sPaginationType: "full_numbers",
                    sServerMethod: "POST",
                    aLengthMenu: [
                        [10, 15, 20, -1],
                        [10, 15, 20, "All"]
                    ],
                    //"iDisplayLength": 20,
                    paginate: {
                        previous: "Anterior",
                        next: "Siguiente"
                    }
                },
                searching: true,
                bLengthChange: true,
                ajax: {
                    // "url": $scope.getURLSchools() + parametros,
                    url: $scope.getURLArchivos() + "INDICADORES&id=",
                    beforeSend: function (request) {
                        request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded;charset=utf-8");
                    },
                    data: function (data) {
                        //console.info(data)
                        //processing();
                    },
                    dataSrc: function (d) {
                        console.log("resolvió el rest de busqueda archivos descarga");
                        //noprocessing();
                        //console.log('resultado de la busqueda:');
                        //console.log(d);

                        //si no encuentra los datos
                        $("html, body").animate(
                            {
                                //scrollTop: 290
                            },
                            1000
                        );
                        return d.datos;
                    }
                },
                columns: [
                    { className: "col-md-6 col-lg-6", name: "Descripción", data: "descripcion" },
                    { className: "col-md-3 col-lg-3", name: "Fecha de Actualización", data: "fileUpdate" },
                    {
                        className: "col-md-3 col-lg-3",
                        name: "Descarga",
                        data: "name",
                        defaultContent: '<i class="fa fa-pencil"/>',
                        className: "row-edit dt-center"
                    }
                ]
            });

            $("#tblDescargasIndicadores tbody").unbind("click");

            $("#tblDescargasIndicadores tbody").on("click", "tr", function () {
                console.log("Descargar archivo ");
                var archivo = table.row(this).data();
                $scope.descargarArchivo(archivo.idFile);
            });
        };

        ///////////////

        $scope.getCalendarioNE = function () {
            var totalRecords = 0;

            var table = $("#tblCalendarioNE").DataTable({});

            //if (table != undefined)
            table.destroy();

            //console.log($scope.getURLSchools() + parametros + '&primer=1' + '&ultimo=1000');

            table = $("#tblCalendarioNE").DataTable({
                responsive: true,
                language: {
                    lengthMenu: "Mostrar _MENU_ registros por página.",
                    zeroRecords: "No se encontraron registros.",
                    info: " ",
                    //"infoEmpty": "No hay registros aún.",
                    //"infoFiltered": "(filtrados de un total de _MAX_ registros)",
                    infoEmpty: " ",
                    infoFiltered: "(filtrado de un total de _MAX_ registros)",
                    search: "Búsqueda",
                    LoadingRecords: "Cargando ...",
                    Processing: "Procesando...",
                    SearchPlaceholder: "Comience a teclear...",
                    iDisplayLength: 20,
                    sPaginationType: "full_numbers",
                    sServerMethod: "POST",
                    aLengthMenu: [
                        [5, 10, 15, 20, -1],
                        [5, 10, 15, 20, "All"]
                    ],
                    //"iDisplayLength": 20,
                    paginate: {
                        previous: "Anterior",
                        next: "Siguiente"
                    }
                },
                searching: true,
                bLengthChange: true,
                ajax: {
                    // "url": $scope.getURLSchools() + parametros,
                    url: $scope.getURLReferencias() + "/id=",
                    beforeSend: function (request) {
                        request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded;charset=utf-8");
                    },
                    data: function (data) {
                        //console.info(data)
                        //processing();
                    },
                    dataSrc: function (d) {
                        console.log("resolvió el rest de busqueda");
                        //noprocessing();
                        //console.log('resultado de la busqueda:');
                        //console.log(d);

                        //si no encuentra los datos
                        $("html, body").animate(
                            {
                                //scrollTop: 290
                            },
                            1000
                        );
                        return d.datos;
                    }
                },
                columns: [
                    { name: "info", data: "infoNE" },
                    { name: "periodo", data: "periodoNE" },
                    { name: "plazo", data: "plazoNE" }
                ]
            });

            $("#tblCalendarioNE tbody").unbind("click");

            $("#tblCalendarioNE tbody").on("click", "tr", function () {
                var contacto = table.row(this).data();
                //console.log(this);

                $scope.showContacto(contacto);
            });
        };

        ///////////////

        /////OFM:Inicia código para descarga info alumnos, escuelas y docentes 911
        $scope.getArchivosDescargaInfo911 = function () {
            console.log("Entró 911, getArchivosDescargaInfo911");
            var table = $("#tblDescargasInfo911").DataTable({});

            //if (table != undefined)
            table.destroy();

            //console.log($scope.getURLSchools() + parametros + '&primer=1' + '&ultimo=1000');

            table = $("#tblDescargasInfo911").DataTable({
                responsive: true,
                language: {
                    lengthMenu: "Mostrar _MENU_ registros por página.",
                    zeroRecords: "No se encontraron registros.",
                    info: " ",
                    //"infoEmpty": "No hay registros aún.",
                    //"infoFiltered": "(filtrados de un total de _MAX_ registros)",
                    infoEmpty: " ",
                    infoFiltered: "(filtrado de un total de _MAX_ registros)",
                    search: "Búsqueda",
                    LoadingRecords: "Cargando ...",
                    Processing: "Procesando...",
                    SearchPlaceholder: "Comience a teclear...",
                    iDisplayLength: 20,
                    sPaginationType: "full_numbers",
                    sServerMethod: "POST",
                    aLengthMenu: [
                        [10, 15, 20, -1],
                        [10, 15, 20, "All"]
                    ],
                    //"iDisplayLength": 20,
                    paginate: {
                        previous: "Anterior",
                        next: "Siguiente"
                    }
                },
                //"info":false,
                searching: true,
                bLengthChange: true,
                ajax: {
                    // "url": $scope.getURLSchools() + parametros,
                    url: $scope.getURLArchivos() + "DATABI911&id=",
                    beforeSend: function (request) {
                        request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded;charset=utf-8");
                    },
                    data: function (data) {
                        //console.info(data)
                        //processing();
                    },
                    dataSrc: function (d) {
                        console.log("resolvió el rest de busqueda archivos descarga");
                        //noprocessing();
                        //console.log('resultado de la busqueda:');
                        //console.log(d);

                        //si no encuentra los datos
                        $("html, body").animate(
                            {
                                //scrollTop: 290,
                            },
                            1000
                        );
                        return d.datos;
                    }
                },
                columns: [
                    { className: "col-md-6 col-lg-6", name: "Descripción", data: "descripcion" },
                    { className: "col-md-2 col-lg-2", name: "Fecha de Actualización", data: "fileUpdate" },
                    {
                        className: "col-md-5 col-lg-5",
                        name: "Descarga",
                        data: "name",
                        defaultContent: '<i class="fa fa-pencil"/>',
                        className: "row-edit dt-center"
                    }
                ]
            });

            $("#tblDescargasInfo911 tbody").unbind("click");

            $("#tblDescargasInfo911 tbody").on("click", "tr", function () {
                console.log("Descargar archivo INFO 911");
                var archivo = table.row(this).data();
                $scope.descargarArchivo(archivo.idFile);
            });
        };
        /////OFM:Finaliza código para descarga info alumnos, escuelas y docentes 911
        $scope.inicializarIndex = function () {
            $scope.getReferencias();
            $scope.getArchivosDescarga();
            $scope.getArchivosDescargaCenso();
            $scope.getArchivosDescargaHistorica();
            $scope.getArchivosDescargaInfo911();
            $scope.getArchivosDescargaIndicadores();
        };

        $scope.processing = function () {
            $("#modal-loading").modal("show");
            $(".tiempo_fuera").hide();
            $("#modal-fileNotFound").modal("hide");
            setTimeout(revisaModal, 3000);
        };

        $scope.noprocessing = function () {
            $("#modal-loading").modal("hide");
        };
        $scope.fileNotFound = function () {
            console.log("Archivo no encontrado");
            $scope.noprocessing();
            $("#modal-fileNotFound").modal("show");
        };
        revisaModal = function () {
            if (($("#modal-loading").data("bs.modal") || {}).isShown) {
                $(".tiempo_fuera").show();
            }
        };
    }
]);
