<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Template {

	/*	since Libraries can't use '$this', create an instance (CI super object)
	*	e.g.: $this->CI =& get_instance();
	*
	*	When loading data into a view via the Template, it looks like this
	*	inside the controller:
	*	$data = array();
	*	$data['news'] = $this->Index->getNews();
	*	$this->template->load('template/tmp', 'index', $data);
	*	It is referred to as '$view_data' in the load function below
	*/
	
	var $data = array();
	
	function Template() 
    {	
        $this->_loadDefaults();
    }
	
	function _loadDefaults() 
    {
		$this->data['css'] = '';
        $this->data['js'] = '';
    }
	
	function set($name, $value)
	{
		$this->data[$name] = $value;
	}

	function load($template = '', $view = '' , $view_data = array(), $return = FALSE)
	{               
		$this->CI =& get_instance();
		$this->set('contents', $this->CI->load->view($view, $view_data, TRUE));			
		return $this->CI->load->view($template, $this->data, $return);
	}
	
	function addCSS($filename)
	{
		$this->CI =& get_instance();
		$this->data['css'] .= $this->CI->load->view('template/css', array('filename' => $filename), true);
	}

    function addJS($filename)
    {
		$this->CI =& get_instance();
        $this->data['js'] .= $this->CI->load->view('template/js', array('filename' => $filename), true);
    }
}

/* End of file Template.php */
/* Location: ./system/application/libraries/Template.php */