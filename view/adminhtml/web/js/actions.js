require(['jquery', 'Magento_Ui/js/modal/modal'], function ($, modal) {

    var modaloption = {
        type: 'popup',
        modalClass: 'modal-popup-gm',
        responsive: true,
        innerScroll: true,
        clickableOverlay: true,
        title: 'Message',
        buttons: []
    };

    $('.modal-popup-gm').remove();
    var createModal = modal(modaloption, $('#msg_box_popup'));

    jQuery(document).on('click', '.print-button', function () {
        // var list = [];
        // jQuery("input[name=tm_checkbox_list]:checked").each(function () {
        //     console.log(jQuery(this).val());
        //     list.push(jQuery(this).val());
        // });

        console.log("hello_tab_grid_massactionJsObject",hello_tab_grid_massactionJsObject.checkedString);
        var items = hello_tab_grid_massactionJsObject.checkedString.split(',');
        console.log("splited",items);

        if (items.length > 4){
            alert("Maximum 4 products allowed per label");
            return;
        }
        popuplabels(items);
    });


    jQuery(document).on('click', '.print-button-popup', function () {
        SubmitLocations();
    });



    function popuplabels(list) {
            var base_url = window.location.origin;
            var customurl = base_url + "/admin/pricetickets/grid/popuplabels";
            jQuery("#loading-mask").show();
            console.log("list selected ",list);
            console.log("window.FORM_KEY ",window.FORM_KEY);
            jQuery.ajax({
                cache: false,
                showLoader: true,
                type: "POST",
                form_key: window.FORM_KEY,
                data: {
                    'productlist': list
                },
                url: customurl,
                complete: function (results) {
                    console.log(results);
                    jQuery("#msg_box_popup").html(results.responseText);
                    $('#msg_box_popup').modal('openModal');

                },
                error: function (xhr, status, errorThrown) {
                    console.log('Error happens. Try again .....',errorThrown);
                }

            });
    }


    
    function SubmitLocations() {
        var base_url = window.location.origin;
        var customurl = base_url + "/admin/pricetickets/grid/submitlocation";
        jQuery("#loading-mask").show();
        console.log("window.FORM_KEY ",window.FORM_KEY);
        console.log("Form Data========= ",jQuery('#pdflabelform').serialize(true));
        jQuery.ajax({
            cache: false,
            showLoader: true,
            type: "POST",
            form_key: window.FORM_KEY,
            data: jQuery('#pdflabelform').serialize(true),
            url: customurl,
            complete: function (results) {
                console.log(results);
                if (results.responseJSON.success == true){
                    window.location.reload();
                }else {
                    alert(results.responseJSON.message);
                }
                

            },
            error: function (xhr, status, errorThrown) {
                console.log('Error happens. Try again .....',errorThrown);
            }

        });
    }



});