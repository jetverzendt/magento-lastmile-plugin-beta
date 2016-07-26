document.observe('dom:loaded', function () {
    $$(".jet_label_printed").each(
        function (item) {
            if (item.innerHTML.indexOf('Nee') >= 0) {
                item.update('<div class="no">Nee</div>');
            }

            if (item.innerHTML.indexOf('Ja') >= 0) {
                item.update('<div class="yes">Ja</div>');
            }

        }
    )

    jetverzendtUpdateOrders();

});


function jetverzendtUpdateOrders() {
    new Ajax.Request(jetverzendt_ajax_url, {
        method: 'get',
        onCreate: function (request) {
            Ajax.Responders.unregister(varienLoaderHandler.handler);
        },
        onSuccess: function (transport) {
            // do nothing
        },
        onFailure: function () {
            //alert('Error');
        }
    });

    setTimeout(function () {
            jetverzendtUpdateOrders();
        },
        15000);
}

function setActiveJetverzendt(status) {
    if (status) {
        $('is_jetverzendt_active').writeAttribute('value', 1);
    } else {
        $('is_jetverzendt_active').writeAttribute('value', 0);
    }

    $$('.shipping_default_tracking').each(
        function (e) {
            if (status == true) {
                e.setStyle({display: 'none'});
            } else {
                e.setStyle({display: 'inline'});
            }
        }
    );
    $$('.shipping_jetverzendt_shipment').each(
        function (e) {
            if (status == true) {
                e.setStyle({display: 'inline'});
            } else {
                e.setStyle({display: 'none'});
            }
        }
    );
}

function setJetShipmentAndOrderFields() {
    var jet_service_daily_pickup = $('jet_daily_pickup').getValue();
    if (jet_service_daily_pickup) {
        $('fields_jet_pickup_date').hide();
        $('jet_pickup_date').clear();
    } else {
        $('fields_jet_pickup_date').show();
    }


    $$(".jetverzendt_fields").each(
        function (item) {
            item.hide();
        }
    )

    var jet_product = $('jet_product').getValue();
    if (jet_product) {
        $$("." + jet_product).each(
            function (item) {
                item.show();
            }
        )
    }


    var jet_service = $('jet_service').getValue();
    if (jet_service) {
        $$("." + jet_service).each(
            function (item) {
                item.show();
            }
        )
    }
}
