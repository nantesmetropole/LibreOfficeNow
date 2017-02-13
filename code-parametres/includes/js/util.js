var dtRegex = new RegExp(/\b\d{1,2}[\/-]\d{1,2}[\/-]\d{4}\b/);
$(document).ready(function() {
	$("#progressbar").progressbar({
		value : parseInt($("input[name=progressbarVal]").val())
	});
	$('input.contentDate').blur(function(){
		if($(this).val() != '' && !dtRegex.test($(this).val())) {
			alert('la date est non valide');
			$(this).focus();
		}
	});
	$('input.listeCampagnes').click(function(){
		$("input[name=idPage]").val("1");
	});
	$('input.migrationEtapeSuivante').click(function(){
		$('input[name=inputHiddenSigneNavigationEtape]').val("+");
	});
	$('input.consulterParAgent').click(function() {
		$('input[name=idPage]').val("2");
	});
	$('input.retourArriereRevenir').click(function(){
		$('input[name=idPage]').val("4");
	});
	$('input.migrationTrompe').click(function(){
		$('input[name=idPage]').val("2");
	});
	$('input.annulerRetour').click(function(){
		$('input[name=idPage]').val("1");
	});
	$('a.consulterListeCampagnes').click(function() {
		$('input[name=idPage]').val("5");
		$("form").submit();
	});
	$('input.migrationEtapePrecedente').click(function(){
		$('input[name=inputHiddenSigneNavigationEtape]').val("-");
		$('input[name=idPage]').val("2");
	});
    $('#datatables').dataTable({
        "sPaginationType":"full_numbers",
        "aaSorting":[[2, "desc"]],
        "bJQueryUI":true
    });
    $('#datatables').delegate('.supprimerCampagne', 'click', function(e){
    	supprimerCampagne($(this).parents('tr').find('td:first-child').text());
    });
    $('#datatables').delegate('.detailCampagne', 'click', function(e){
    	consulterDetailCampagne($(this).parents('tr').find('td:first-child').text());
    });
    $('#datatablesAgents').dataTable({
        "sPaginationType":"full_numbers",
        "aaSorting":[[0, "asc"]],
        "bJQueryUI":true
    });
    $('#datatablesAgents').delegate('.supprimerAgent', 'click', function(e){
    	supprimerAgent($(this).parents('tr').find('td:first-child').text());
    });
});

/**
 * supprimer une campagne
 * 
 * @param idCampagne
 */
function supprimerCampagne(idCampagne) {
	try {
		if (confirm("Voulez-vous supprimer la campagne n°"+idCampagne+" ?")) {
			var form = document.forms.accueilCampagne;
			form.idPage.value = '3';
			form.numero.value = idCampagne;
			form.submit();
		}
	} catch (exception) {
		alert(exception.description + " dans la methode supprimerCampagne.");
	}
}

/**
 * consulter le Detail d'une campagne
 * 
 * @param idCampagne
 */
function consulterDetailCampagne(idCampagne) {
	try {
		var form = document.forms.accueilCampagne;
		if (null == form) {
			form = document.forms.accueilCampagneConsultation;
			form.idPage.value = '6';
		} else {
			form.idPage.value = '2';
		}
		form.numero.value = idCampagne;
		form.submit();
	} catch (exception) {
		alert(exception.description + " dans la methode consulterDetailCampagne.");
	}
}

/**
 * supprimer un agent
 * 
 * @param idAgent
 */
function supprimerAgent(idAgent) {
	try {
		if (confirm("Voulez-vous supprimer l'agent "+idAgent+" ?")) {
			var form = document.forms.accueilCampagne;
			form.idPage.value = '8';
			form.idAgent.value = idAgent;
			form.submit();
		}
	} catch (exception) {
		alert(exception.description + " dans la methode supprimerAgent.");
	}
}

/**
 * ajouter un agent
 */
function ajouterUnAgent() {
	try {
		var form = document.forms.accueilCampagne;
		form.idPage.value = '9';
		form.submit();
	} catch (exception) {
		alert(exception.description + " dans la methode ajouterUnAgent.");
	}
}

/**
 * export CSV 
 */
function exportCSV(idPageExport) {
	try {
		//document.forms.entete.telechargement.value = 'true';
		var form = document.forms.accueilCampagneConsultation;
		form.idPage.value = idPageExport;
		form.action = 'upload.php';
		form.submit();
		form.action = 'index.php';
	} catch (exception) {
		alert(exception.description + " dans la methode exportCSV.");
	}
}

