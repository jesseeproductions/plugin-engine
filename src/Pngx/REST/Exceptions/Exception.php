<?php

namespace Research\Project\REST\Exceptions;
/**
 * Class Tribe__REST__Exceptions__Exception
 */
class Exception extends \Exception {
	/**
	 * @var int
	 */
	protected $status;

	public function __construct( $message, $code, $status ) {
		$this->message = $message;
		$this->code    = $code;
		$this->status  = $status;
	}

	/**
	 * Return the error status.
	 * @return int
	 */
	public function getStatus() {
		return $this->status;
	}
}