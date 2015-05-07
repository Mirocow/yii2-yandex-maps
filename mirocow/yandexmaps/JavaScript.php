<?php
/**
 * mirocow\yandexmaps\JavaScript class file.
 */

namespace mirocow\yandexmaps;

use yii;
use yii\base\Component;

/**
 * @property string $code
 */
class JavaScript extends Component {
	/** @var string */
	private $_code = '';

	/**
	 * @param string $code
	 */
	public function __construct($code = '') {
		$this->setCode($code);
	}

	/**
	 * @return string
	 */
	function __toString() {
		return $this->getCode();
	}

	/**
	 * @return string
	 */
	public function getCode() {
		return $this->_code;
	}

	/**
	 * @param string $code
	 */
	public function setCode($code) {
		$this->_code = (string) $code;
	}
}