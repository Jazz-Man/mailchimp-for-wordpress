<?php

/**
 * Class FormTest
 * @ignore
 */
class FormTest extends PHPUnit_Framework_TestCase {

	public function __construct() {
	}

	/**
	 * @covers MC4WP_Form::__construct
	 */
	public function test_constructor() {
		$id = 12;
		mock_get_post( array( 'ID' => $id ) );
		$form = new MC4WP_Form( $id );
		$this->assertEquals( $id, $form->ID );
		$this->assertEquals( $id, $form->post->ID );
	}

	/**
	 * @covers MC4WP_Form::has_field_type
	 */
	public function test_has_field_type() {
		mock_get_post(
			array(
				'post_content' => '<input type="email" name="EMAIL" />'
			)
		);

		$form = new MC4WP_Form( 1 );
		$this->assertTrue( $form->has_field_type( 'email' ) );
		$this->assertFalse( $form->has_field_type( 'date' ) );


		mock_get_post(
			array(
				'post_content' => '<input type="email" name="EMAIL" /><input type="date" name="EMAIL" /><input type="url" name="EMAIL" />'
			)
		);
		$form = new MC4WP_Form( 1 );
		$this->assertTrue( $form->has_field_type( 'email' ) );
		$this->assertTrue( $form->has_field_type( 'date' ) );
		$this->assertTrue( $form->has_field_type( 'url' ) );
		$this->assertFalse( $form->has_field_type( 'number' ) );
	}

	/**
	 * @covers MC4WP_Form::get_field_types
	 */
	public function test_get_field_types() {
		$types = array( 'number', 'email', 'date', 'url' );
		mock_get_post(
			array(
				'post_content' => '<input type="number"><input type="email" name="EMAIL" /><input type="date" name="EMAIL" /><input type="url" name="EMAIL" />'
			)
		);
		$form = new MC4WP_Form(15);
		$this->assertEquals( $form->get_field_types(), $types );

		mock_get_post(
			array(
				'post_content' => '',
			)
		);
		$form = new MC4WP_Form(1);
		$this->assertEmpty( $form->get_field_types() );
	}

	/**
	 * @covers MC4WP_Form::is_valid
	 */
	public function test_is_valid() {
		$form = new MC4WP_Form(1);
		$this->assertTrue( $form->is_valid() );

		// empty data should not validate
		$request = new MC4WP_Request();
		$form = new MC4WP_Form(1);
		$form->handle_request( $request );
		$valid = $form->is_valid();
		$this->assertFalse( $valid );

		// errors array should have been filled
		$this->assertNotEmpty( $form->errors );

//		// with lists and mocked nonce, form should be valid
		// @todo fix this test
//		define( 'WP_CACHE', true );
//		$valid_data = array(
//			'email' => 'johngreene@hotmail.com',
//			'_mc4wp_lists' => array( 'list-id' ),
//			'_mc4wp_timestamp' => time() - 100
//		);
//
//		$request = new MC4WP_Request( array(), $valid_data );
//		$form = new MC4WP_Form(1);
//		$form->handle_request( $request );
//		$this->assertTrue( $form->is_valid() );

		// todo: required fields
	}

	/**
	 * @covers MC4WP_Form::has_errors
	 */
	public function test_has_errors() {
		$form = new MC4WP_Form(1);
		$form->errors = array( 'required_field_missing' );
		$this->assertTrue( $form->has_errors() );

		$form->errors = array();
		$this->assertFalse( $form->has_errors() );
	}

	/**
	 * @covers MC4WP_Form::handle_request
	 */
	public function test_handle_request() {
		$form = new MC4WP_Form(15);
		$data = array(
			'EMAIL' => 'value'
		);
		$request = new MC4WP_Request( array(), $data );
		$form->handle_request( $request );

		// form should show as submitted
		$this->assertTrue( $form->is_submitted );

		// data should have been filled
		$this->assertNotEmpty( $form->data );
		$this->assertEquals( $form->data, $data );


		// data should have been uppercased
		$form = new MC4WP_Form(15);
		$data = array(
			'email' => 'value'
		);
		$data_uppercased = array_change_key_case( $data, CASE_UPPER );
		$request = new MC4WP_Request( array(), $data );
		$form->handle_request( $request );
		$this->assertEquals( $form->data, $data_uppercased );
	}


}