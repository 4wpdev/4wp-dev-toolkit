<?php
/**
 * Base unit test case with Brain Monkey (no WordPress loaded).
 *
 * @package 4wpdev/4wp-dev-toolkit
 */

declare( strict_types=1 );

namespace ForWP\DevToolkit\Testing;

use Brain\Monkey;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Sets up and tears down Brain Monkey around every test.
 */
abstract class UnitTestCase extends TestCase {

	/**
	 * Set up Brain Monkey.
	 */
	protected function set_up(): void {
		parent::set_up();
		Monkey\setUp();
	}

	/**
	 * Tear down Brain Monkey.
	 */
	protected function tear_down(): void {
		Monkey\tearDown();
		parent::tear_down();
	}
}
