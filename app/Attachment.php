<?php

namespace Castle;

use Carbon;
use Exception;
use Storage;

class Attachment
{
	/**
	 * @var \League\Flysystem
	 */
	protected $filesystem;

	/**
	 * @var string Path to the file, relative to the $this->filesystem root.
	 */
	protected $path;

	/**
	 * Returns a collection of all attachments.
	 *
	 * @return \Illuminate\Collection
	 */
	public static function all()
	{
		$files = $this->filesystem->allFiles('/');
		return collect($files)->map(function ($f) {
			return new static($f);
		});
	}

	/**
	 * Returns the number of attachments.
	 *
	 * @return int
	 */
	public static function count()
	{
		return count($this->filesystem->allFiles('/'));
	}

	/**
	 * Finds an attachment by path name, or null if it does not exist.
	 *
	 * @return Attachment|null
	 */
	public static function find($path)
	{
		return $this->filesystem->has($path) ?
			new static($path) :
			null;
	}

	/**
	 * Gets the storage adapter.
	 *
	 * @return \League\Flysystem
	 */
	protected static function getStorage()
	{
		return Storage::disk('attachments');
	}

	/**
	 * Creates a new attachment from a path.
	 *
	 * @return $this
	 */
	public function __construct($path)
	{
		$this->filesystem = static::getStorage();
		$this->path = $path;
	}

	/**
	 * Prints the attachment's path.
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->path;
	}

	/**
	 * Return properties dynamically.
	 *
	 * @return string
	 */
	public function __get($property)
	{
		$method = 'get' . ucfirst($property) . 'Attribute';

		if (method_exists($this, $method)) {
			return $this->$method();
		}

		throw new Exception('Unknown property "'.$property.'"');
	}

	/**
	 * Returns the path to the attachment relative to the root storage.
	 *
	 * @return string
	 */
	public function getPathAttribute()
	{
		return $this->path;
	}

	/**
	 * Returns the contents of the attachment.
	 *
	 * @return string
	 */
	public function getContentAttribute()
	{
		return $this->filesystem->read($this->path);
	}

	/**
	 * Returns the name of the attachment.
	 *
	 * @return string
	 */
	public function getNameAttribute()
	{
		return basename($this->path);
	}

	/**
	 * Returns the file extension of the attachment, if there is one.
	 *
	 * @return string
	 */
	public function getExtensionAttribute()
	{
		return ($p = strrpos($this->name, '.')) !== false ?
			substr($this->name, $p + 1) :
			'';
	}

	/**
	 * Returns the name of the directory in which the attachment is stored.
	 *
	 * @return string
	 */
	public function getDirectoryAttribute()
	{
		return dirname($this->path);
	}

	/**
	 * Returns the attachment's MIME type.
	 *
	 * @return string
	 */
	public function getTypeAttribute()
	{
		try {
			return $this->filesystem->getMimeType($this->path);
		} catch (Exception $e) {
			//
		}
	}

	/**
	 * Generates an icon for use by Glyphicons based on a given file.
	 *
	 * @return string
	 */
	public function getIconAttribute()
	{
		switch ($this->extension) {
			case 'crt':
			case 'csr':
			case 'key':
			case 'pem':
			case 'pgp':
				return 'certificate';

			case 'bz2':
			case 'gz':
			case 'tar':
			case 'z':
			case 'zip':
				return 'compressed';

			case 'gif':
			case 'jpeg':
			case 'jpg':
			case 'png':
			case 'tif':
			case 'tiff':
				return 'picture';

			case 'aif':
			case 'aiff':
			case 'flac':
			case 'm4a':
			case 'mp3':
			case 'ogg':
			case 'wav':
				return 'music';

			case 'markdown':
			case 'md':
			case 'doc':
			case 'txt':
				return 'file';
		}

		if ($mime = $this->type) {
			list($category, $type) = explode('/', $mime, 2);

			switch ($type) {
				case 'x-whatever':
					return 'paperclip';
			}

			switch ($category) {
				case 'audio':
					return 'music';

				case 'image':
					return 'picture';

				case 'text':
					return 'file';
			}
		}

		return 'paperclip';
	}

	/**
	 * Returns the timestamp at which the attachment was last modified.
	 *
	 * @return \Carbon\Carbon
	 */
	public function getDateAttribute()
	{
		try {
			return Carbon\Carbon::createFromTimestamp(
				$this->filesystem->getTimestamp($this->path)
			);
		} catch (Exception $e) {
			//
		}
	}

	/**
	 * Returns the size of the attachment in bytes.
	 *
	 * @return integer
	 */
	public function getSizeAttribute()
	{
		try {
			return $this->filesystem->getSize($this->path);
		} catch (Exception $e) {
			//
		}
	}

	/**
	 * Deletes the attachment from the filesystem.
	 *
	 * @return bool
	 */
	protected function delete()
	{
		try {
			return $this->filesystem->delete($this->path, $to);
		} catch (Exception $e) {
			//
		}
	}

	/**
	 * Renames the attachment on the filesystem.
	 *
	 * @return $this
	 */
	protected function rename($to)
	{
		try {
			$this->filesystem->rename($this->path, $to);
			$this->path = $to;
			return $this;
		} catch (Exception $e) {
			//
		}
	}
}
