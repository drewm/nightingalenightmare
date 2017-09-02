<?php

class Runner
{

	public $photo = null;
	
	public function load_photo($type)
	{
		$Photos = new Photos($this->db);
		$this->photo = $Photos->get_one_for_runner($this->bib, $type);
	}
}