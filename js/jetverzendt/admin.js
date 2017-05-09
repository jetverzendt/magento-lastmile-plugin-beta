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



function setJetPickupDate() {
    var jet_service_daily_pickup = $('jet_daily_pickup').getValue();
    if (jet_service_daily_pickup) {
        $('fields_jet_pickup_date').hide();
        $('jet_pickup_date').clear();
    } else {
        $('fields_jet_pickup_date').show();
    }
}