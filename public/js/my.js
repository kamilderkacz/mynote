/**
 * Created by Chicken on 2015-09-15.
 */
$(document).ready(function () {

    // Tryb edycji w widoku listy sekcji
    $('#edit').click(function() {
        $('[name="edit-button"]').toggle('fast');
        $('[name="delete-button"]').toggle('fast');
    });
    // Ukrycie CZEGOŚ
    $('.hide-this-class').click(function() {
        $(this).hide();
    });
    //Potwierdzenie usuwania
    $('[type=delete-action]').click(function (event) {
        event.preventDefault();
        var warning = $(this).attr('title');
        var location = $(this).attr('href');
        bootbox.dialog({
            message: warning, /*""Czy na pewno chcesz usunąć konto? Wszystkie znajdujące się na niej dane (w tym notatki!) zostaną usunięte z systemu.""*/
            title: "<span=\"bootbox-danger\"><i class=\"fa fa-exclamation-triangle\" style=\"color:#C9302C;\"></i> Uwaga!</span>",
            buttons:
            {
                "success":
                {
                    "label": "Tak",
                    "className": "btn-sm btn-danger",
                    "callback": function () { // funkcja jaka ma zostać wykonana po sukcesie
                        window.location.replace(location);
                    }
                },
                "danger":
                {
                    "label": "Nie",
                    "className": "btn-sm btn-success",
                    "callback": function () { // funkcja jaka ma zostać wykonana po anulowaniu
                    }
                }
            }
        });
    });
    
    // Skrypt: Zmiany widoczności sekcji
    $('[name=icon-changing-visibility]').click(function (event) {
        event.preventDefault();
        var iconName = $(this).attr('class');
        var changeTo;
        if(iconName === 'fa fa-lock') { changeTo = 'public'; } 
        if(iconName === 'fa fa-globe') { changeTo = 'private'; } 
        var sectionID = $(this).attr('id');
        //te argsy SĄ DOBRZE
        
        bootbox.dialog({
            message: 'Zamierzasz zmienić widoczność sekcji. Może to oznaczać, że będzie ona widoczna dla każdego w Internecie. Czy chcesz kontynuować? ', 
            title: "<span=\"bootbox-warning\"><i class=\"glyphicon glyphicon-question-sign\" style=\"color:#5BC0DE\"></i> Informacja</span>",
            buttons:
            {
                "success":
                {
                    "label": "Tak",
                    "className": "btn-sm btn-danger",
                    "callback": function () { 

                        $.ajax({
                            type     : "POST",
                            url      : "/section/changevisibility", // względny URL
                            data     : {
                                change_to : changeTo,
                                section_id : sectionID
                            },
                            success : function(response) { 
                                console.log(response);
                                response = JSON.parse(response);

//                                                bootbox.alert(response.msg);
                                bootbox.dialog({
                                    message: response.msg,
                                    title: "<span=\"bootbox-warning\"><i class=\"glyphicon glyphicon-info-sign\" style=\"color:#5BC0DE\"></i> Informacja</span>",
                                });
                            },
                            error: function() {
                                bootbox.dialog({
                                    message: "Wystąpił nieznany błąd...",
                                    title: "<span=\"bootbox-warning\"><i class=\"glyphicon glyphicon-remove-sign\" style=\"color:#d9534f\"></i> Błąd</span>",
                                });
                            }
                        });



                    }
                },
                "danger":
                {
                    "label": "Nie",
                    "className": "btn-sm btn-default",
                    "callback": function () { }
                }
            }
        });
    });
    
    // Umożliwienie liście #sortable bycia sortowalnym i skrypt zmiany kolejn.
    $('#sortable').sortable({
    axis: 'y',
    update: function (event, ui) {
        var data = $(this).sortable('serialize');
        alert(data);
        // POST to server using $.post or $.ajax
//        $.ajax({
//            data: data,
//            type: 'POST',
//            url: '/your/url/here'
//        });
    }
    });
    



}); // end of (document).ready

/* 
 * Function to paste a date to title of note input.
 */
function pasteToday() {
    var d = new Date();
    var day = d.getDate(); //1-31
    var mon = d.getMonth(); //0-11
    var month = '';
    switch(mon) {
    case 0:
        month = 'stycznia';
        break;
    case 1:
        month = 'lutego';
        break;
    case 2:
        month = 'marca';
        break;
    case 3:
        month = 'kwietnia';
        break;
    case 4:
        month = 'maja';
        break;
    case 5:
        month = 'czerwca';
        break;
    case 6:
        month = 'lipca';
        break;
    case 7:
        month = 'sierpnia';
        break;
    case 8:
        month = 'września';
        break;
    case 9:
        month = 'października';
        break;
    case 10:
        month = 'listopada';
        break;
    case 11:
        month = 'grudnia';
        break;
    default:
        month = 'miesiąca ?';
    } 
    var result = day + ' ' + month;
    $("[name='title']").val(result);
}

/*
 * Function to set sections per page.
 */
function setItemsPerPage() {
        
        var iPP = $('#ipp-select').val();
        
        $.ajax({
            url     :  "/section/setipp",
            type  : "POST",
            data    : 
            {
                iPP: iPP
            },
            success: function(data, textStatus, jqXHR ) { 
                alert(textStatus);

            },
            error: function(request, textStatus, errorThrown) {
                alert(errorThrown);
            },
            complete: function(jqXHR, textStatus) {
//                alert(textStatus);        
            }
        });
      }