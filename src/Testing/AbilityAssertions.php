<?php
/**
 * PHPUnit assertions for WordPress Abilities API (integration tests, WordPress 6.9+ loaded).
 *
 * Usage in a WP_UnitTestCase:
 *   use \ForWP\DevToolkit\Testing\AbilityAssertions;
 *   $this->assertAbilityContract( 'forwp-seo-helper/get-meta', 'forwp-seo-helper', true );
 *
 * @package 4wpdev/4wp-dev-toolkit
 */

declare( strict_types=1 );

namespace ForWP\DevToolkit\Testing;

// phpcs:disable WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid -- PHPUnit assertion naming.

/**
 * Checks registration and the 4WP ability contract (docs/ABILITY-CONTRACT.md in 4wp-mcp-connector).
 */
trait AbilityAssertions {

	/**
	 * Ability is registered.
	 *
	 * @param string $name Ability name.
	 */
	public function assertAbilityRegistered( string $name ): \WP_Ability {
		$this->assertTrue( function_exists( 'wp_get_ability' ), 'Abilities API is not available (WordPress 6.9+).' );

		$ability = wp_get_ability( $name );
		$this->assertInstanceOf( \WP_Ability::class, $ability, sprintf( 'Ability "%s" is not registered.', $name ) );

		return $ability;
	}

	/**
	 * Ability follows the 4WP contract: namespace/action, category, label, description,
	 * object schemas, annotations.
	 *
	 * @param string $name     Ability name.
	 * @param string $category Expected category slug.
	 * @param bool   $read_only Expected meta.annotations.readonly.
	 */
	public function assertAbilityContract( string $name, string $category, bool $read_only ): void {
		$ability = $this->assertAbilityRegistered( $name );

		$this->assertMatchesRegularExpression( '#^[a-z0-9-]+/[a-z0-9-]+$#', $name, 'Name must be namespace/action.' );
		$this->assertSame( $category, $ability->get_category(), 'Unexpected category.' );
		$this->assertNotSame( '', trim( $ability->get_label() ), 'Label is empty.' );
		$this->assertNotSame( '', trim( $ability->get_description() ), 'Description is empty.' );

		$schemas = array(
			'input'  => $ability->get_input_schema(),
			'output' => $ability->get_output_schema(),
		);
		foreach ( $schemas as $kind => $schema ) {
			$this->assertNotEmpty( $schema, sprintf( '%s_schema is missing.', $kind ) );
			$this->assertSame( 'object', $schema['type'] ?? null, sprintf( '%s_schema must be an object.', $kind ) );
		}

		$annotations = $ability->get_meta()['annotations'] ?? array();
		foreach ( array( 'readonly', 'destructive', 'idempotent' ) as $key ) {
			$this->assertArrayHasKey( $key, $annotations, sprintf( 'meta.annotations.%s is missing.', $key ) );
		}
		$this->assertSame( $read_only, (bool) $annotations['readonly'], 'Unexpected meta.annotations.readonly.' );
	}

	/**
	 * Permission for a user (0 = logged out).
	 *
	 * @param string              $name     Ability name.
	 * @param int                 $user_id  User ID.
	 * @param bool                $expected Expected result.
	 * @param array<string,mixed> $input    Input passed to permission_callback.
	 */
	public function assertAbilityPermission( string $name, int $user_id, bool $expected, array $input = array() ): void {
		$ability  = $this->assertAbilityRegistered( $name );
		$previous = get_current_user_id();

		wp_set_current_user( $user_id );
		$result = $ability->check_permissions( array() === $input ? null : $input );
		wp_set_current_user( $previous );

		$this->assertSame( $expected, true === $result, sprintf( 'Permission for user %d on "%s".', $user_id, $name ) );
	}
}
