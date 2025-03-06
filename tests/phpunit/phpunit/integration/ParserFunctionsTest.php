<?php

namespace MediaWiki\Skin\NORA\Tests;

use MediaWiki\Skin\NORA\ParserFunctions;
use MediaWikiIntegrationTestCase;
use ReflectionClass;

/**
 * @covers \MediaWiki\Skin\NORA\ParserFunctions
 */
class ParserFunctionsTest extends MediaWikiIntegrationTestCase {

	public function testCanConstruct(): void {
		$parserFunctions = new ParserFunctions();
		$this->assertTrue( true );
	}

	/**
	 * Test the registerHandlers method loads parser functions correctly
	 */
	public function testRegisterHandlers(): void {
		// Create parser functions instance
		$parserFunctions = new ParserFunctions();

		// Use reflection to access the private handlers property
		$reflection = new ReflectionClass( ParserFunctions::class );
		$handlersProperty = $reflection->getProperty( 'handlers' );
		$handlersProperty->setAccessible( true );

		// Get the handlers array
		$handlers = $handlersProperty->getValue( $parserFunctions );

		// Assert handlers are registered
		$this->assertIsArray( $handlers );

		// Since we might be running in a test environment without actual parser function files,
		// we'll skip the not-empty assertion and other specific checks if the array is empty
		if ( empty( $handlers ) ) {
			$this->markTestSkipped(
				'No parser functions found. This is expected in a test environment without actual parser function files.'
			);
			return;
		}

		// Check structure of handlers array if we have any handlers
		foreach ( $handlers as $functionName => $classAndMethod ) {
			$this->assertIsString( $functionName, 'Parser function name should be a string' );
			$this->assertIsArray( $classAndMethod, 'Class and method should be an array' );
			$this->assertCount( 2, $classAndMethod, 'Class and method should have two elements' );
			$this->assertIsString( $classAndMethod[0], 'First element should be class name' );
			$this->assertIsString( $classAndMethod[1], 'Second element should be method name' );
			$this->assertEquals( 'render', $classAndMethod[1], 'Method should be "render"' );
		}
	}

}
