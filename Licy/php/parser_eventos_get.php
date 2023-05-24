public function parser_eventos_get(){
		$id_folio = $this->input->get('id_folio');
		if (!isset($id_folio)) {
			$this->response((array('estatus' => 0, 'texto' => 'El campo de id_folio esta vacio favor de verificar', 'parametros' => '')), REST_Controller::HTTP_BAD_REQUEST);
			return false;
		}
		if (!preg_match('#^\d+(?:\.[05]0*)?$#', $id_folio)) {
			$this->response((array('estatus' => 0, 'texto' => 'id_folio invalido: ' . $id_folio, 'parametros' => '')), REST_Controller::HTTP_BAD_REQUEST);
			return false;
		}
		$this->id_folio = $id_folio;
		$folio_carpeta = str_pad($this->id_folio, 6, "0", STR_PAD_LEFT);
		$ruta_carpeta = $this->config->item('url_folio') . $folio_carpeta;

		if (!(file_exists($ruta_carpeta) && is_dir($ruta_carpeta))) {
			$this->response((array('estatus' => 0, 'texto' => 'La carpeta No existe : ' . $ruta_carpeta, 'parametros' => $ruta_carpeta)), REST_Controller::HTTP_BAD_REQUEST);
			return false;
		}
		$nombre_archivo = $folio_carpeta . '_eventos.json';
		$ruta_completa = $ruta_carpeta . '/' . $nombre_archivo;

		if (file_exists($ruta_completa) && !is_dir($ruta_completa)) {
			$this->response((array('estatus' => 0, 'texto' => 'El archivo: ' . $nombre_archivo . ' ya existe en esta ruta: ' . $ruta_completa, 'parametros' => $ruta_completa)), REST_Controller::HTTP_BAD_REQUEST);
			return false;
		}

		$this->load->library('simple_html_dom');
		$this->html = new simple_html_dom();

		//--------------------DOCUMENTOS --------------------------------//
		$ruta_carpeta_html = $this->config->item('url_html').'/';
		$archivos = glob($ruta_carpeta_html . '*');
		$this->data_result = array();
		foreach ($archivos as $archivo) {
			$this->saveHTMLData($archivo);
		}
		if (!file_put_contents($ruta_completa, json_encode($this->data_result, JSON_UNESCAPED_UNICODE))) {
			$this->response(array('estatus' => 0, 'texto' => 'Error al generar y guardar el archivo JSON ', 'parametros' => ''), REST_Controller::HTTP_BAD_REQUEST);
			return false;
		}
//		$new_folder=$this->config->item('url_html_respaldo').'/';
//		$this->moveHTMLFiles($ruta_carpeta_html,$new_folder);
		$this->response(array('estatus' => 1, 'texto' => 'Archivo JSON generado y guardado exitosamente en ' . $ruta_completa, 'parametros' => ''), REST_Controller::HTTP_OK);
	}