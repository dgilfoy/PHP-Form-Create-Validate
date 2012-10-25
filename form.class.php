<?php

require_once 'validate.class.php';
require_once 'input.class.php';

class FormCreate extends ValidateForm {
    
    protected $form_data;
    protected $inputs;
    protected $post_status;
	protected $form_method;
	protected $valid = false;
    
    public function __construct( $method = 'POST' ) {
		parent::__construct( );
		$this->form_method = $method;
		$this->post_status = ( $_SERVER['REQUEST_METHOD'] == strtoupper( $method ) ) ? true : false;
    }
	
    public function new_field( $type, $name, $atts ) {
		$field = new FormField( $type, $name );
		$this->inputs[ $name ] = $field->build( $atts );
    }
    
    public function new_fields( array $fields ) {
		if( 1 > count( $fields ) )
			return false;
        foreach ( $fields as $field ) {
            $this->new_field( $field['type'], $field['name'], $field['atts'] );
        }
        return true;
    }
	
	public function validate_fields(){
		$this->valid = new ValidateForm();
		$this->inputs = $this->validate_array( $this->inputs );
	}
	
	public function no_errors(){
		if ( ! $this->valid )
			return false;
		return $this->valid->has_errors();
	}
	
	public function output_field( $field_name, $atts = array() ){
		extract( $args = array_merge( array(
			'echo' => true,
			'before' => '<p class="error">',
			'after' => '</p>'
		), $atts ) );
		$output = $this->inputs[ $field_name ]['output'];
		if( $this->no_errors() && isset( $this->inputs[ $field_name]['error'] ) )
			$output .= $before . $this->inputs[ $field_name ]['error'] . $after;
		if ( $echo ){
			echo $output;
		}else{
			return $output;
		}
	}
	
}

