<?php

interface PNGX__Documentation__Swagger__Builder_Interface {
	/**
	 * Registers a documentation provider for a path.
	 *
	 * @param                                            $path
	 * @param PNGX__REST__Endpoints__READ_Endpoint_Interface $endpoint
	 */
	public function register_documentation_provider( $path, PNGX__Documentation__Swagger__Provider_Interface $endpoint );

	/**
	 * @return PNGX__Documentation__Swagger__Provider_Interface[]
	 */
	public function get_registered_documentation_providers();

	/**
	 * Registers a documentation provider for a definition.
	 *
	 * @param                                                  string $type
	 * @param PNGX__Documentation__Swagger__Provider_Interface       $provider
	 */
	public function register_definition_provider( $type, PNGX__Documentation__Swagger__Provider_Interface $provider );

	/**
	 * @return PNGX__Documentation__Swagger__Provider_Interface[]
	 */
	public function get_registered_definition_providers();
}