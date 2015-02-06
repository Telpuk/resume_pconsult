<?php

class View
{
	private $content = '';
	private $header = '';
	private $footer = '';
	private $javascriptFooter = '';
	private $javascriptHeader = '';
	private $style = '';

	private $data;

	private function _compress( $flag = true )
	{
		if ( $flag && COMPRESS_HTML ):
			return function ( $buf ) {
				return trim( str_replace( array( "\n", "\r", "\t" ), '', $buf ) );
			};
		endif;
	}

	public function render( $arguments = array() )
	{
		try {
			if ( isset( $arguments['js'] ) && JS === true && is_array( $arguments['js'] ) ) {

				if ( isset( $arguments['js']['javascriptFooter'] ) && is_array( $arguments['js']['javascriptFooter'] ) ):
					$src = '';
					$js_c = '';
					foreach ( $arguments['js']['javascriptFooter'] as $key => $js ) {
						if ( $key === "src" ) {
							foreach ( $js as $value ) {
								$src .= '<script type="text/javascript" src=' . $value . '></script>' . "\n";
							}
						} elseif ( $key === "js_c" && !is_null( $js ) && $js ) {
							$js_c .= $js;
						}
					}
					$this->javascriptFooter = $src . $js_c . "\n";
				endif;
				if ( isset( $arguments['js']['javascriptHeader'] ) && is_array( $arguments['js']['javascriptHeader'] ) ):
					$src = '';
					$js_c = '';
					foreach ( $arguments['js']['javascriptHeader'] as $key => $js ) {
						if ( $key === "src" ) {
							foreach ( $js as $value ) {
								$src .= '<script type="text/javascript" src=' . $value . '></script>' . "\n";
							}
						} elseif ( $key === "js_c" && !is_null( $js ) && $js ) {
							$js_c .= $js;
						}
					}
					$this->javascriptHeader = $src . $js_c . "\n";
				endif;
			}

			if ( isset( $arguments['styles'] ) && is_array( $arguments['styles'] ) ) {
				$styleLinks = '';
				$styleCode = '';
				foreach ( $arguments['styles'] as $key => $styles ) {
					if ( $key === "styleLinks" && is_array( $arguments['styles'][$key] ) ) {
						foreach ( $styles as $link ) {
							$styleLinks .= '<link rel="stylesheet" href="' . $link . '">' . "\n";
						}
					} elseif ( $key === "styleCode" && is_string( $arguments['styles'][$key] ) ) {
						$styleCode .= '<style type="text/css">' . $styles . '</style>' . "\n";
					}
				}
				$this->style = $styleLinks . $styleCode . "\n";
			}

			if ( isset( $arguments['data'] ) ) {
				$this->data['data'] = $arguments['data'];
			}

			if ( isset( $arguments['cache_headers'] ) ) {
				$this->cacheHeaders( $arguments['cache_headers'] );
			}

			if ( isset( $arguments['ajax'] ) && $arguments['ajax'] === true ) {
				ob_start( $this->_compress() );
				if ( !@include_once DIR_PROJECT . '/mvc/Views/controller/' . $arguments['view'] . '.phtml' )
					throw new Exception( 'Ajax view' );
				$ajaxContent = ob_get_contents();
				ob_clean();
				return $ajaxContent;
			} else {

				if ( isset( $arguments['data']['helpers'] ) ) {
					unset( $this->data['data']['helpers'] );
					foreach ( $arguments['data']['helpers'] as $key => $helper ) {
						ob_start( $this->_compress() );
						if ( !@include_once( DIR_PROJECT . '/mvc/Views/controller/' . $helper . '.phtml' ) )
							throw new Exception( 'Ajax view' );
						$this->data['helpers'][$key] = ob_get_contents();
						ob_clean();
					}
				}

				ob_start( $this->_compress() );
				if ( !@include_once DIR_PROJECT . '/mvc/Views/controller/' . $arguments['view'] . '.phtml' )
					throw new Exception( 'Content view' );
				$this->content = ob_get_contents();
				ob_clean();


				ob_start( $this->_compress() );
				if ( !@include_once DIR_PROJECT . '/mvc/Views/layout/helpers/header.phtml' )
					throw new Exception( 'Helpers header view' );
				$this->header = ob_get_contents();
				ob_clean();

				ob_start( $this->_compress() );
				if ( !@include_once DIR_PROJECT . '/mvc/Views/layout/helpers/footer.phtml' )
					throw new Exception( 'Content view' );
				$this->footer = ob_get_contents();
				ob_clean();


				ob_start( $this->_compress() );
				if ( !@include_once DIR_PROJECT . '/mvc/Views/layout/index.phtml' )
					throw new Exception( 'Layout view' );
				$layout = ob_get_contents();
				ob_clean();

				return $layout;
			}
		} catch ( Exception $e ) {
			header( "Location: " . BASE_URL . "/error" );
			exit;
		}


	}

	private function  cacheHeaders( $cache )
	{
		if ( $cache === false ) {
			header( "Expires: Mon, 26 Jul 1990 05:00:00 GMT" );
			header( "Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . " GMT" );
			header( "Cache-Control: no-store, no-cache, must-revalidate" );
			header( "Cache-Control: post-check=0, pre-check=0", false );
			header( "Pragma: no-cache" );
		}
	}
}