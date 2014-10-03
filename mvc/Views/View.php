<?php
	class View{
		private $_helpers_menu = '';
		private $_content = '';
		private $_javascript = '';

		private $data;

		public function render($arguments = []){
			try{

				if(isset($arguments['js']) && JS === true){
					$src = '';
					$js_c = '';
					foreach($arguments['js'] as $key => $js){
						if($key === "src"){
							foreach($js as $value){
								$src .= "<script type='text/javascript' src='{$value}'></script>\n";
							}
						}elseif($key === "js_c"){
							$js_c .= "\n<script type='text/javascript'>{$js_c}</script>\n";
						}
					}
					$this->_javascript = $js_c.$src."\n";
				}

				if(isset($arguments['cache_headers'])){
					$this->cacheHeaders($arguments['cache_headers']);
				}
				if(isset($arguments['data']['helpers'])){
					foreach($arguments['data']['helpers'] as $key=>$helper) {
						ob_start ();
						if (!@include_once dirname(__FILE__)."/controller/".$helper.".phtml")
							throw new Exception();

						$this->data['helpers'][$key] = ob_get_clean ();
					}
					unset($arguments['data']['helpers']);
				}

				if(isset($arguments['data'])){
					$this->data['data'] = $arguments['data'];
				}

				if(isset($arguments['ajax']) && $arguments['ajax'] === true){
					ob_start();
					if(!@include_once dirname(__FILE__)."/controller/{$arguments['view']}.phtml")
						throw new Exception();
					return ob_get_clean();
				}else{
					ob_start();
					if(!@include_once dirname(__FILE__)."/controller/{$arguments['view']}.phtml")
						throw new Exception();
					$this->_content = ob_get_clean();
					ob_start();
					if(!@include_once dirname(__FILE__). "/layout/index.phtml")
						throw new Exception();
					return ob_get_clean();
				}
			}catch (Exception $e){
				header("Location: ".BASE_URL."/error");
				exit;
			}

		}

		private  function  cacheHeaders($cache){
			if($cache === false){
				header("Expires: Mon, 26 Jul 1990 05:00:00 GMT");
				header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
				header("Cache-Control: no-store, no-cache, must-revalidate");
				header("Cache-Control: post-check=0, pre-check=0", false);
				header("Pragma: no-cache");
			}
		}
	}