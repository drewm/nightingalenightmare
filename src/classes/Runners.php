<?php

class Runners 
{
	private $db;

	public function __construct( $db )
	{
		$this->db = $db;
	}

	public function get_results()
	{
		$runners = $this->db->query('SELECT * FROM runners WHERE finish_time IS NOT NULL ORDER BY finish_time ASC')
					->fetchAll(PDO::FETCH_CLASS, 'Runner');

		return $runners;
	}

	public function get_prize_winners()
	{
		$winners = [];
		$bibs    = [999];

		// top men
		$topmen = $this->db->query('SELECT * FROM runners 
										WHERE finish_time IS NOT NULL 
											AND gender = "M"
										ORDER BY finish_time ASC
										LIMIT 3')
					->fetchAll(PDO::FETCH_CLASS, 'Runner');

		if (count($topmen)) {
			$i = 1;
			foreach($topmen as $man) {
				$bibs[] = $man->bib;
				$man->db = $this->db;
				$man->load_photo('mugshot');
				$winners['m'.$i] = $man;
				$i++;
			}
 		}

 		// top women
		$women = $this->db->query('SELECT * FROM runners 
										WHERE finish_time IS NOT NULL 
											AND gender = "F"
										ORDER BY finish_time ASC
										LIMIT 3')
					->fetchAll(PDO::FETCH_CLASS, 'Runner');

		if (count($women)) {
			$i = 1;
			foreach($women as $woman) {
				$bibs[] = $woman->bib;
				$woman->db = $this->db;
				$woman->load_photo('mugshot');
				$winners['f'.$i] = $woman;
				$i++;
			}
 		}

 		$genders = ['m', 'f'];

 		foreach($genders as $g) {
 			// 1st runner 40-49
	 		$runner = $this->db->query('SELECT * FROM runners 
											WHERE finish_time IS NOT NULL 
												AND gender = "'.strtoupper($g).'"
												AND bib NOT IN ('.implode(', ', $bibs).')
												AND age BETWEEN 40 AND 49
											ORDER BY finish_time ASC
											LIMIT 1')
						->fetchAll(PDO::FETCH_CLASS, 'Runner');
			if ($runner) {
				$bibs[] = $runner[0]->bib;
				$runner[0]->db = $this->db;
				$runner[0]->load_photo('mugshot');
				$winners[$g.'4049'] = $runner[0];
			}

			// 1st runner 50-59
	 		$runner = $this->db->query('SELECT * FROM runners 
											WHERE finish_time IS NOT NULL 
												AND gender = "'.strtoupper($g).'"
												AND bib NOT IN ('.implode(', ', $bibs).')
												AND age BETWEEN 50 AND 59
											ORDER BY finish_time ASC
											LIMIT 1')
						->fetchAll(PDO::FETCH_CLASS, 'Runner');
			if ($runner) {
				$bibs[] = $runner[0]->bib;
				$runner[0]->db = $this->db;
				$runner[0]->load_photo('mugshot');
				$winners[$g.'5059'] = $runner[0];
			}

			// 1st runner 60+
	 		$runner = $this->db->query('SELECT * FROM runners 
											WHERE finish_time IS NOT NULL 
												AND gender = "'.strtoupper($g).'"
												AND bib NOT IN ('.implode(', ', $bibs).')
												AND age >=60 
											ORDER BY finish_time ASC
											LIMIT 1')
						->fetchAll(PDO::FETCH_CLASS, 'Runner');
			if ($runner) {
				$bibs[] = $runner[0]->bib;
				$runner[0]->db = $this->db;
				$runner[0]->load_photo('mugshot');
				$winners[$g.'60'] = $runner[0];
			}

 		}


 		// Costumes
 		$runners = $this->db->query('SELECT * FROM runners 
										WHERE costume_prize IS NOT NULL 
										ORDER BY costume_prize ASC
										LIMIT 3')
					->fetchAll(PDO::FETCH_CLASS, 'Runner');

		if (count($runners)) {
			foreach($runners as $runner) {
				$bibs[] = $runner->bib;
				$runner->db = $this->db;
				$runner->load_photo('fancydress');
				$winners['c'.$runner->costume_prize] = $runner;
			}
 		}
 		


 		return $winners;


	}

	public function calc()
	{
		$runners = $this->db->query('SELECT * FROM runners  
										WHERE finish_time IS NOT NULL 
										ORDER BY finish_time ASC')
					->fetchAll(PDO::FETCH_CLASS, 'Runner');

		$i = 1;

		if (count($runners)) {
			foreach($runners as $Runner) {

				$sql = "UPDATE runners SET finish_position = ? WHERE id = ?";
				$this->db->prepare($sql)
					->execute([$i, $Runner->id]);

				$i++;
			}
		}
	}

	public function add_finish_time($bib, $finish_time)
	{
		$sql = "UPDATE runners SET finish_time = ? WHERE bib = ?";
		return  $this->db->prepare($sql)
					->execute([$finish_time, $bib]);
	}

	public function find_by_bib($bib)
	{
		$runners = $this->db->query('SELECT * FROM runners  
										WHERE bib = '.(int)$bib)
					->fetchAll(PDO::FETCH_CLASS, 'Runner');
		if ($runners) {
			return $runners[0];
		}

		return null;
	}

}