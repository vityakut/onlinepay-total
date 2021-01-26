<?php
class ModelExtensionTotalonlinepay extends Model {
	public function getTotal($total) {
		if (isset($this->session->data['payment_method'])) {
			$sub_total = $this->cart->getSubTotal();
			if (in_array($this->session->data['payment_method']['code'], $this->config->get('total_onlinepay_payments')) && ($sub_total > $this->config->get('total_onlinepay_total')) && ($sub_total > 0)){
				$this->load->language('extension/total/onlinepay');
				$discount = ($sub_total * $this->config->get('total_onlinepay_fee') / 100);
				$total['totals'][] = array(
					'code'       => 'onlinepay',
					'title'      => sprintf($this->language->get('text_onlinepay'), $this->config->get('total_onlinepay_fee')),
					'value'      => -$discount,
					'sort_order' => $this->config->get('total_onlinepay_sort_order')
				);

				$total['total'] -= $discount;
			}
		}
	}
}