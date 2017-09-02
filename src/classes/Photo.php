<?php

class Photo
{

	public function get_html_tag()
	{
		return '<img src="/photos/'.$this->path.'">';
	}	

	public function get_lazy_html_tag()
	{
		return '<img data-src="/photos/'.$this->path.'">';
	}	
}