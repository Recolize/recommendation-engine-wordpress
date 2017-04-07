<?php
/**
 * Recolize WordPress WooCommerce Conversion Tracking
 *
 * @section LICENSE
 * This source file is subject to the GNU General Public License Version 3 (GPLv3).
 *
 * @category Recolize
 * @package Recolize_RecommendationEngine
 * @author Recolize GmbH <service@recolize.com>
 * @copyright 2015 Recolize GmbH (http://www.recolize.com)
 * @license http://opensource.org/licenses/GPL-3.0 GNU General Public License Version 3 (GPLv3).
 */
class Recolize_WooCommerceConversionTracking
{
    /**
     * Initialize the insertion of the Recolize conversion tracking on the WooCommerce order success page.
     *
     * @return Recolize_WooCommerceConversionTracking
     */
    public function initialize()
    {
        add_action('woocommerce_thankyou', array($this, 'addRecolizeConversionTracking'));
        return $this;
    }

    /**
     * Add the Recolize javascript conversion tracking.
     *
     * @param int $orderId the id of the order
     *
     * @return Recolize_WooCommerceConversionTracking
     */
    public function addRecolizeConversionTracking($orderId)
    {
        $order = new WC_Order($orderId);

        $snippet = <<<EOF
<script type="text/javascript">
    var RecolizeParameters = RecolizeParameters || {};
    RecolizeParameters['itemAction'] = 'sale';
    RecolizeParameters['saleData'] = {};

EOF;

        foreach ($order->get_items() as $item) {
            /** @var WC_Order_Item_Product $item */
            /** @var WC_Product $product */
            $product = $item->get_product();
            $snippet .= sprintf("    RecolizeParameters['saleData']['%s'] = '%.2f';\n", get_the_guid($product->get_id()), $item->get_total());
        }

        $snippet .= "</script>";

        echo $snippet;

        return $this;
    }
}