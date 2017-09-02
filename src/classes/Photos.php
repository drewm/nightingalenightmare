<?php

use Intervention\Image\ImageManager;

class Photos 
{
	private $db;

	private $import_folder;
	private $done_folder;
	private $photo_folder;
	private $photo_web_path;

	public function __construct( $db )
	{
		$this->db             = $db;
		$this->import_folder  = __DIR__ . '/../../photos-in/';
		$this->done_folder    = __DIR__ . '/../../photos-done/';
		$this->photo_folder   = __DIR__ . '/../../public/photos/';
		$this->photo_web_path = '/photos/';
	}

	public function import()
	{
		$escape_hatch = 0;

		$manager = new ImageManager(array('driver' => 'gd'));

		foreach (new DirectoryIterator($this->import_folder) as $fileInfo) {
			if ($escape_hatch >= 10) break;
		    if ($fileInfo->isDot()) continue;
		    if (!$fileInfo->isFile()) continue;
		    if (substr($fileInfo->getFilename(), 0, 1)==='.') continue;
		    echo $fileInfo->getFilename() . "<br>\n";
		   	//echo $fileInfo->getRealPath() . "<br>\n";

		    $image = $manager->make($fileInfo)->resize(2048, 2048, function ($constraint) {
					    $constraint->aspectRatio();
					});
		   	$image->save($this->photo_folder.$fileInfo->getFilename());


		   	// insert
		   	$sql = "INSERT INTO photos(path, w, h) VALUES(?,?,?)";
		 	$this->db->prepare($sql)
					->execute([$fileInfo->getFilename(), $image->width(), $image->height()]);


			rename($fileInfo->getRealPath(), $this->done_folder.$fileInfo->getFilename());

		   	$image = null;

		    $escape_hatch++;
		}
	}

	public function get_next_to_tag()
	{
		$photos = $this->db->query('SELECT * FROM photos  
										WHERE type IS NULL
										LIMIT 1')
					->fetchAll(PDO::FETCH_CLASS, 'Photo');
		if ($photos) {
			return $photos[0];
		}

		return null;
	}

	public function tag_photo($id, $bib = null, $type)
	{
		$sql = "UPDATE photos SET type = ?, bib = ? WHERE id = ?";
		return  $this->db->prepare($sql)
					->execute([$type, $bib, $id]);
	}

	public function get_one_for_runner($bib, $type)
	{
		$stmt = $this->db->prepare('SELECT * FROM photos  
										WHERE type = ?
										AND bib = ?
										ORDER BY rating DESC
										LIMIT 1');
		$stmt->execute([$type, $bib]);
		$photos = $stmt->fetchAll(PDO::FETCH_CLASS, 'Photo');
		if ($photos) {
			return $photos[0];
		}

		return null;
	}

	public function get_slideshow($type = null)
	{
		if ($type) {
			$stmt = $this->db->prepare('SELECT * FROM photos WHERE rating >=0 AND type = ? ORDER BY RAND()');
			$stmt->execute([$type]);
		} else {
			$stmt = $this->db->prepare('SELECT * FROM photos WHERE rating >=0 ORDER BY RAND()');
			$stmt->execute();
		}

		$photos = $stmt->fetchAll(PDO::FETCH_CLASS, 'Photo');
		if ($photos) {
			return $photos;
		}

		return null;
	}

	public function get_marshal_prize_contenders()
	{
		$stmt = $this->db->prepare('SELECT * FROM photos  
										WHERE type = ? AND prize_contender = ?');
		$stmt->execute(['marshal', '1']);
		$photos = $stmt->fetchAll(PDO::FETCH_CLASS, 'Photo');
		if ($photos) {
			return $photos;
		}

		return null;
	}

}