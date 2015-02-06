<?php
use Yandex\Disk\DiskClient;

class YandexDisk extends DiskClient {
	private $_fileName = null;

	public function __construct($token = null){
		parent::__construct($token);
	}

	public function parseArrayMovieFILES(){
		if(isset($_FILES['uploadFileYandex']) && is_uploaded_file($_FILES['uploadFileYandex']['tmp_name']['movie']) && !$_FILES['uploadFileYandex']['error']['movie']){
			$this->_fileName = uniqid().'.'.$this->_getFormatFile($_FILES['uploadFileYandex']['name']['movie']);
			$this->uploadFile(
				'/movie/',
				array(
					'path' => $_FILES['uploadFileYandex']['tmp_name']['movie'],
					'size' => $_FILES['uploadFileYandex']['size']['movie'],
					'name' => $this->_fileName
				)
			);

			return $this->_fileName;
		}
		return false;
	}
	public function parseArrayAudioFILES(){
		if(isset($_FILES['uploadFileYandex']) && is_uploaded_file($_FILES['uploadFileYandex']['tmp_name']['audio']) && !$_FILES['uploadFileYandex']['error']['audio']){

			$this->_fileName = uniqid().'.'.$this->_getFormatFile($_FILES['uploadFileYandex']['name']['audio']);
			$this->uploadFile(
				'/audio/',
				array(
					'path' => $_FILES['uploadFileYandex']['tmp_name']['audio'],
					'size' => $_FILES['uploadFileYandex']['size']['audio'],
					'name' => $this->_fileName
				)
			);
			return $this->_fileName ;
		}
		return false;
	}

	public function getFileNameUpload(){
		return $this->_fileName;
	}

	public function _getFormatFile($file){
		return mb_strtolower( substr( strrchr( $file, '.' ), 1 ), 'utf-8' );
	}

	public function deletefileyandex($dir=null, $file_name = null){
		if($dir && $file_name){
			try {
				if ( $dir === 'movie' ) {
					if($this->delete( 'movie/' . $file_name) ){
						return true;
					}
				}
				if ( $dir === 'audio' ) {
					if($this->delete( 'audio/' . $file_name )){
						return true;
					}
				}
				return false;
			}catch(ErrorException $e){
				$e->getMessage('Произошла ошибка на сервере!!');
			}
		}
	}

	public function uploadfileyandex($dir=null, $file_name = null, $name_user=null){
		if($dir && $file_name && $name_user){
			try {
				if ( $dir === 'movie' ) {
					$file = $this->downloadFile( 'movie/' . $file_name );
					$flxt = $this->_getFormatFile( $file_name );
					$name_user .= ' (видео запись)';
				}
				if ( $dir === 'audio' ) {
					$file = $this->downloadFile( 'audio/' . $file_name );
					$flxt = $this->_getFormatFile( $file_name );
					$name_user .= ' (аудио запись)';
				}
				header( "Pragma: public" );
				header( "Expires: 0" );
				header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
				header( "Cache-Control: private", false );
				header( "Content-Type: " . $this->_getTypeContent( $flxt ) );
				header( "Content-Disposition: attachment; filename='".$name_user.'.'.$flxt."';" );
				header( "Content-Transfer-Encoding: binary" );
				header( "Content-Length: " . filesize( $file ) );
				readfile( $file );
				exit();
			}catch(ErrorException $e){
				$e->getMessage('Произошла ошибка на сервере!!');
			}
		}
	}


	private function _getTypeContent($flxt = null){
		$tpe = null;
		switch($flxt){
			case "gif": $tpe="image/gif"; break;
			case "png": $tpe="image/png"; break;
			case "jpg": $tpe="image/jpg"; break;
			case "3gp": $tpe="video/3gpp"; break;
			case "jad": $tpe="text/vnd.sun.j2me.app-descriptor"; break;
			case "jar": $tpe="application/java-archive"; break;
			case "wml": $tpe="text/vnd.wap.wml"; break;
			case "wbmp": $tpe="image/vnd.wap.wbmp"; break;
			case "mid": $tpe="audio/midi"; break;
			case "mp3": $tpe="audio/mp3"; break;
			case "mp4": $tpe="video/mp4"; break;
			case "flv": $tpe="video/x-flv"; break;
			case "ics": $tpe="text/calendar"; break;
			case "pdf": $tpe="application/pdf"; break;
			case "exe": $tpe="application/octet-stream"; break;
			case "zip": $tpe="application/zip"; break;
			case "doc": $tpe="application/msword"; break;
			case "xls": $tpe="application/vnd.ms-excel"; break;
			case "ppt": $tpe="application/vnd.ms-powerpoint"; break;
			default: $tpe="application/force-download";
		}

		return $tpe;
	}

	private function _checkFilesFormatMovie($file){
		if ( !isset( $this->_format_[mb_strtolower( substr( strrchr( $photo['name'], '.' ), 1 ), 'utf-8' )] ) ) {
			return array('photo_format'=> true);
		}
		return true;
	}


} 