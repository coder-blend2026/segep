$(document).ready(function(){
		$('.list-unstyled').addClass( "col-xs-12 col-sm-12" );
		$('.col-md-10').addClass( "col-xs-8 col-sm-8" );
		$('#title').addClass( "col-md-12 col-sm-12" );
		$('.col-md-12').addClass( "col-xs-12  col-sm-12" );
		$('.col-md-4').addClass( "col-xs-12  col-sm-12" );
		$('.col-md-5').addClass( "col-xs-12  col-sm-12" );
	/* $(".flag-banner").css( { "background-size" : "100% auto" } );
	var width = $(window).width();
	var height = $(window).height();
	if ( width>=768 ) {
		$(".flag-banner").css( { "min-height" : "470px" } );
		$(".flag-banner").css( { "margin-bottom" : "50px" } );
	}
	else{
		$(".flag-banner").css( { "min-height" : "250px" } );
		$(".flag-banner").css( { "margin-bottom" : "0px" } );
	} */
		var as = document.querySelectorAll('article a');
	var urls = [];
	urlReplace ='https://www.gob.mx/shcp/acciones-y-programas/tesoreria-de-la-federacion-tesofe';
	for (var i = 0; i < as.length; i++) {
		if (as[i].href === urlReplace){
			as[i].setAttribute("href",'https://www.gob.mx/shcp/tesofe');
		}
	}
	$(".flag-banner").css( { "background-image" : "none" } );
	// cambios 11nov2025 for liconsa

	const currentUrl = window.location.href;

	// List of URLs to exclude from redirect
	const exceptionUrls = [
		"https://www.gob.mx/liconsa/documentos/puntos-de-venta-liconsa?state=published",
		"https://www.gob.mx/liconsa/documentos/centros-de-trabajo-en-entidades-federativas",
		"https://www.gob.mx/liconsa/acciones-y-programas/convenios-con-actores-sociales",
		"https://www.gob.mx/liconsa/documentos/aviso-de-privacidad-integral-de-liconsa",
		"https://www.gob.mx/liconsa/documentos/requisitos-y-documentacion-para-el-ingreso-a-la-actividad-de-adquisicion-de-leche-nacional-en-los-centros-de-acopio-de-liconsa?idiom=es",
		"https://www.gob.mx/liconsa/acciones-y-programas/protocolo-para-la-atencion-inmediata-ante-la-ocurrencia-de-desastres-naturales-contingencias-o-emergencias-sanitarias"
	];


	// Check if current URL starts with 'liconsa' but is NOT one of the exceptions
	if (
		currentUrl.startsWith("https://www.gob.mx/liconsa") &&
		!exceptionUrls.includes(currentUrl)
	) {
		// Redirect while preserving the rest of the path
		window.location.href = currentUrl.replace(
			"https://www.gob.mx/liconsa",
			"https://www.gob.mx/lecheparaelbienestar"
		);
	}
});

