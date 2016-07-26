<?php

class JetVerzendt_Shipping_Block_Adminhtml_Widget_Grid_Column_Renderer_Lastmile extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        if ($row->getJetLastMile() != '') {
            $return   = '';
            $lastmile = @unserialize($row->getJetLastMile());


            if (isset($lastmile['type']) && $lastmile['type'] == 'dhl_deliverdate') {
                $return .= '<strong>';
                $return .= 'Bezorgmoment - ';
                $return .= (isset($lastmile['lastmile_service'])) ? $this->escapeHtml($lastmile['lastmile_service']) : '';
                $return .= '</strong><br/>';

                $return .= ($lastmile['lastmile_deliverdate']) ? 'Bezorgdatum: ' . $this->escapeHtml($lastmile['lastmile_deliverdate']) . '<br/>' : '';
                $return .= ($lastmile['lastmile_deliverperiod']) ? 'Tijdvak: ' . $this->escapeHtml($lastmile['lastmile_deliverperiod']) : '';
                $return .= (isset($lastmile["lastmile_deliverevening"]) && $lastmile["lastmile_deliverevening"]) ? ' <strong>(avondlevering!)</strong>' : '';

            }

            if (isset($lastmile['type']) && $lastmile['type'] == 'parcelshop') {
                $return .= '<strong>';
                $return .= 'Parcelshop - ';
                $return .= (isset($lastmile['lastmile_service'])) ? $this->escapeHtml($lastmile['lastmile_service']) : '';
                $return .= '</strong><br/>';

                $return .= ($lastmile['lastmile_parcelshop_description']) ? $this->escapeHtml($lastmile['lastmile_parcelshop_description']) : '';

            }

            if (isset($lastmile['type']) && $lastmile['type'] == 'dpd_saterday') {
                $return .= '<strong>DPD Zaterdaglevering</strong><br/>';
                $return .= ($lastmile['lastmile_deliverdate']) ? 'Zaterdag '.  date('d-m-Y', strtotime($lastmile['lastmile_deliverdate'])) : '';

            }

            return $return;
        } else {
            return '';
        }
    }
}



