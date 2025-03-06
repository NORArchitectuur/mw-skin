<?php

namespace MediaWiki\Skin\NORA\Tests;

use MediaWiki\Skin\NORA\Hooks;
use MediaWikiIntegrationTestCase;
use ReflectionClass;
use MediaWiki\HookContainer\HookContainer;
use MediaWiki\MediaWikiServices;

/**
 * @covers \MediaWiki\Skin\NORA\Hooks
 */
class HooksTest extends MediaWikiIntegrationTestCase {

	public function testCanConstruct(): void {
		$hooks = new Hooks();
		$this->assertTrue( true );
	}

	public function testRegister(): void {
		// Create mock objects
		$hookContainer = $this->createMock( HookContainer::class );
		$services = $this->createMock( MediaWikiServices::class );

		// Configure mock services to return mock hook container
		$services->method( 'getHookContainer' )
			->willReturn( $hookContainer );

		// Replace the global service with our mock
		$this->setService( 'HookContainer', $hookContainer );

		// We expect register() to be called for each handler
		$hookContainer->expects( $this->atLeastOnce() )
			->method( 'register' );

		// Create hooks instance and call register
		$hooks = new Hooks();
		$hooks->register();
	}

	public function testRegisterHandlers(): void {
		// Create hooks instance
		$hooks = new Hooks();

		// Use reflection to access private property
		$reflection = new ReflectionClass( Hooks::class );
		$handlersProperty = $reflection->getProperty( 'handlers' );
		$handlersProperty->setAccessible( true );

		// Get the handlers array
		$handlers = $handlersProperty->getValue( $hooks );

		// Assert handlers are registered
		$this->assertIsArray( $handlers );
		$this->assertNotEmpty( $handlers, 'Handlers array should not be empty' );

		// Check structure of handlers array
		foreach ( $handlers as $hookName => $hookCallbacks ) {
			$this->assertIsString( $hookName, 'Hook name should be a string' );
			$this->assertIsArray( $hookCallbacks, 'Hook callbacks should be an array' );

			foreach ( $hookCallbacks as $callback ) {
				$this->assertIsArray( $callback, 'Each callback should be an array' );
				$this->assertCount( 2, $callback, 'Callback should have two elements [class, method]' );
				$this->assertIsString( $callback[0], 'First element should be class name' );
				$this->assertIsString( $callback[1], 'Second element should be method name' );
				$this->assertStringStartsWith( 'on', $callback[1], 'Method name should start with "on"' );
			}
		}
	}

	public function testHookFiles(): void {
		// Test that hook files actually exist
		$hooks = new Hooks();
		$reflection = new ReflectionClass( Hooks::class );
		$handlersProperty = $reflection->getProperty( 'handlers' );
		$handlersProperty->setAccessible( true );

		$handlers = $handlersProperty->getValue( $hooks );

		foreach ( $handlers as $hookName => $hookCallbacks ) {
			foreach ( $hookCallbacks as $callback ) {
				$className = $callback[0];
				// Check that class exists
				$this->assertTrue(
					class_exists( $className ),
					"Hook handler class $className should exist"
				);

				// Check that method exists
				$methodName = $callback[1];
				$this->assertTrue(
					method_exists( $className, $methodName ),
					"Method $methodName should exist in class $className"
				);
			}
		}
	}
}