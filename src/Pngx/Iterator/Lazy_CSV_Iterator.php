<?php
namespace Pngx\Iterator;

use Iterator;
use SplFileObject;

/**
 * Class Lazy_CSV_Iterator
 *
 * @since 3.3.0
 *
 */
class Lazy_CSV_Iterator implements Iterator {

	/**
	 * The current pointer of the csv file.
	 *
	 * @since 3.3.0
	 *
	 * @var int
	 */
	private $pointer = 0;

	/**
	 * The current line of the csv file.
	 *
	 * @since 3.3.0
	 *
	 * @var mixed
	 */
	private $line;

	/**
	 * The csv file.
	 *
	 * @since 3.3.0
	 *
	 * @var SplFileObject
	 */
	private $file;

	/*
	 * Constructor of Lazy_CSV_Iterator.
	 *
	 * @since 3.3.0
	 *
	 */
	public function __construct( string $filePath ) {
		$this->file = new SplFileObject( $filePath );
	}

	/**
	 * Get the current line.
	 *
	 * @since 3.3.0
	 *
	 * @return mixed The current line of a csv file.
	 */
	public function current() {
		return $this->line;
	}

	/**
	 * Get the next line.
	 *
	 * @since 3.3.0
	 */
	public function next() {
		$this->line = $this->file->fgets();
		$this->pointer ++;
	}

	/**
	 * Get the current key.
	 *
	 * @since 3.3.0
	 *
	 * @return int The current pointer
	 */
	public function key(): int {
		return $this->pointer;
	}

	public function valid(): bool {
		return ! empty( $this->line ) && $this->file->valid();
	}

	/**
	 * Reset the iterator.
	 *
	 * @since 3.3.0
	 */
	public function rewind() {
		$this->pointer = 0;
		$this->file->seek( 0 );
		$this->line = $this->file->fgets();
	}
}
