<?php
class ControllerExtensionTotalOnlinepay extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/total/onlinepay');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('total_onlinepay', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=total', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=total', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/total/onlinepay', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/total/onlinepay', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=total', true);

		if (isset($this->request->post['total_onlinepay_total'])) {
			$data['total_onlinepay_total'] = $this->request->post['total_onlinepay_total'];
		} else {
			$data['total_onlinepay_total'] = $this->config->get('total_onlinepay_total');
		}

		if (isset($this->request->post['total_onlinepay_fee'])) {
			$data['total_onlinepay_fee'] = $this->request->post['total_onlinepay_fee'];
		} else {
			$data['total_onlinepay_fee'] = $this->config->get('total_onlinepay_fee');
		}
		if (isset($this->request->post['total_onlinepay_payments'])) {
			$data['total_onlinepay_payments'] = $this->request->post['total_onlinepay_payments'];
		} else {
			$data['total_onlinepay_payments'] = $this->config->get('total_onlinepay_payments');
		}


		if (isset($this->request->post['total_onlinepay_status'])) {
			$data['total_onlinepay_status'] = $this->request->post['total_onlinepay_status'];
		} else {
			$data['total_onlinepay_status'] = $this->config->get('total_onlinepay_status');
		}

		if (isset($this->request->post['total_onlinepay_sort_order'])) {
			$data['total_onlinepay_sort_order'] = $this->request->post['total_onlinepay_sort_order'];
		} else {
			$data['total_onlinepay_sort_order'] = $this->config->get('total_onlinepay_sort_order');
		}

		$this->load->model('setting/extension');

		$extensions = $this->model_setting_extension->getInstalled('payment');

		foreach ($extensions as $key => $value) {
			if (!is_file(DIR_APPLICATION . 'controller/extension/payment/' . $value . '.php') && !is_file(DIR_APPLICATION . 'controller/payment/' . $value . '.php')) {
				$this->model_setting_extension->uninstall('payment', $value);

				unset($extensions[$key]);
			}
		}

		$data['payment_methods'] = array();

		// Compatibility code for old extension folders
		$files = glob(DIR_APPLICATION . 'controller/extension/payment/*.php');

		if ($files) {
			foreach ($files as $file) {
				$extension = basename($file, '.php');

				$this->load->language('extension/payment/' . $extension, 'extension');

				$text_link = $this->language->get('extension')->get('text_' . $extension);

				if ($text_link != 'text_' . $extension) {
					$link = $text_link;
				} else {
					$link = '';
				}

				if ($this->config->get('payment_' . $extension . '_status')) {
					$data['payment_methods'][] = array(
						'name'       => $this->language->get('extension')->get('heading_title'),
						'code'		=> $extension
					);
				}
			}
		}



		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/total/onlinepay', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/total/onlinepay')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}




}