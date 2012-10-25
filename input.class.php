<?php

class FormField {
    
	protected $type;
	protected $name;
	protected $element;
    
    public function __construct( $type, $name ) {
        $this->name = $name;
		$this->type = $type;
    }
    
	public function build( $args = array( ) ) {
		$this->set_element( $this->type, $this->name, $args );
		return array(  
			'output' => $this->element->build_element( ),
			'rules' => $this->element->get_rules(),
			'value' => $this->element->get_value(),
			'label' => $this->element->get_label()
		);
	}
	
	protected function set_element( $type, $name, $args ){
		$class = ucwords( $type ) . 'Element' ;
		if ( ! class_exists( $class ) )
			return false;
		$this->element = new $class( $name, $args );
	}
	
}

abstract class FormElement {
	
	var $name;
	var $args;
	var $label;
	
	public function __construct( $name, $args=array( ) ) {
		$this->name = $name;
		$this->args = $this->set_defaults( $args );
		$this->set_label();
		$this->set_value();
		return $this;
	}
	
	public function set_label( ) {
		if ( ! $this->args['label'] ) {
			$this->label = '';
			return $this;
		}
		$label = new FormLabel( $this->args['id'], $this->args['label'] );
		$label->set_containing_element( $this->args['label_before'], $this->args['label_after'] );
		$this->label = $label->set_label();
		return $this;
	}
	
	public function get_label(){
		return $this->args['label'];
	}
	
	public function get_rules(){
		return $this->args['rules'];
	}
	
    abstract protected function build_element( );

	public function build_element_group( ) {}
	
	public function set_value( ){
		if ( 'POST' == strtoupper( $this->args['method'] ) )
			return ( isset( $_POST[ $this->name ] ) ) ? $_POST[ $this->name ] : $this->args['value'];
		elseif ( 'GET' == strtoupper( $this->args['method'] ) )
			$this->value = ( isset( $_GET[ $this->name ] ) ) ? $_GET[ $this->name ] : $this->args['value'];
		else
			return $this->args['value'];
	}
	
	public function get_value(){
		return $this->args['value'];
	}
	
	protected function set_defaults( $new_args = array( ) ) {
		$defaults = array(
			'method' => 'post',
			'label' => ucwords( str_replace( '-', ' ', $this->name ) ),
			'rules' => '',
			'class' => false,
			'id' => $this->name . 'Element',
			'value' => '',
			'before' => '',
			'after' => '',
			'label_first' => true,
			'label_before' => '',
			'label_after' => ''
		);
		return array_merge( $defaults, $new_args);
	}
	
}

class FormLabel {
	
	var $forElement;
	var $text;
	var $before;
	var $after;
	
	public function __construct( $for, $text ) {
		$this->forElement = $for;
		$this->text = $text;
		return $this;
	}
	
	public function set_containing_element( $before, $after ) {
		$this->before = $before;
		$this->after = $after;
		return $this;
	}
	
	public function set_label() {
		$label = '<label for="' . $this->forElement . '">' . $this->text . '</label>';
		return $this->before . $label . $this->after;
	}
}

/**
 *	Text Element for Form
 */
class TextElement extends FormElement {
	
	public function build_element( ) {
		$field = '<input type="text" class="' . $this->args['class'] . '" id="' . $this->args['id'] . '" value="' . $this->set_value( ) . '"/>';
		$output = ( $this->args['label_first'] ) ? $this->label . $field : $field . $this->label;
		return $this->args['before'] . $output. $this->args['after'];
	}
}