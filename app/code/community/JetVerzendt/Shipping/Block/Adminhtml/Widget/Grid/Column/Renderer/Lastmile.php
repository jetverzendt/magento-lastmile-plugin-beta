<?php

class JetVerzendt_Shipping_Block_Adminhtml_Widget_Grid_Column_Renderer_Lastmile extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {

        $order = Mage::getModel('sales/order')->load($row->getEntityId());
        if ($order->getJetLastMile() != '') {
            $lastMileData = @unserialize($order->getJetLastMile());

            $jetLastmileComment = '';

            if ( isset( $lastMileData['type'] )
                && $lastMileData['type'] == 'dhl_deliverdate'
                && isset( $lastMileData['lastmile_service'] )
                && isset( $lastMileData['lastmile_deliverdate'] )
                && isset( $lastMileData['lastmile_deliverperiod'] )
                && isset( $lastMileData['lastmile_deliverevening'] )
            ) {
                $jetLastmileComment = '<strong>Tijdsvaklevering op: ' . $this->escapeHtml( $lastMileData['lastmile_deliverdate'] ) . '</strong>';
                $jetLastmileComment .= '<br/>' . ( ( $lastMileData['lastmile_deliverevening'] == 1 ) ? 'Avondlevering.' : '' );

            }

            if ( isset( $lastMileData['type'] )
                && $lastMileData['type'] == 'dpd_saterday'
            ) {
                $jetLastmileComment = '<strong>DPD zaterdaglevering op: ' . $this->escapeHtml( date( 'd-m-Y', strtotime( $lastMileData['lastmile_deliverdate'] ) ) ) . '</strong>';
            }

            if ( isset( $lastMileData['type'] )
                && $lastMileData['type'] == 'fadello'
            ) {
                $jetLastmileComment = '<strong>Same Day Delivery</strong>';
            }


            if ( isset( $lastMileData['type'] )
                && $lastMileData['type'] == 'nextdaypremium'
            ) {
                $jetLastmileComment = '<strong>Next Day Delivery</strong>';
            }



            if ( isset( $lastMileData['type'] )
                && $lastMileData['type'] == 'parcelshop'
                && isset( $lastMileData['lastmile_service'] )
                && isset( $lastMileData['lastmile_parcelshop_id'] )
            ) {

                $jetLastmileComment = '<strong>Parcelshop: ' . $this->escapeHtml( $lastMileData['lastmile_parcelshop_id'] ) . '</strong>';
                $jetLastmileComment .= '<br/>' . $this->escapeHtml( $lastMileData['lastmile_parcelshop_description'] );
            }

            return $jetLastmileComment;
        } else {
            return '';
        }
    }
}



