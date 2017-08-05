<?php

namespace Castle\Behaviors;

use Castle\Attachment;
use Exception;
use File;
use Illuminate\Http\UploadedFile;
use Log;
use RuntimeException;
use Session;
use Storage;
use Traversable;

trait Attachable
{
	public function getAttachmentsAttribute()
	{
		if (empty($this->attributes['attachments'])) {
			return $this->attributes['attachments'] = collect();
		}

		$attachments = json_decode($this->attributes['attachments'], true);

		if (($e = json_last_error()) != JSON_ERROR_NONE) {
			throw new RuntimeException('Could not parse attachments list');
		}

		return collect($attachments)->map(function($att) {
			return new Attachment($att);
		});
	}

	public function setAttachmentsAttribute($value)
	{
		$storage = Storage::disk('attachments');
		$attachments = [];

		$directory = method_exists($this, 'getAttachmentDirectoryAttribute') ?
			$this->getAttachmentDirectoryAttribute() :
			'';

		if (!$value) {
			return $this->attributes['attachments'] = json_encode([]);
		}

		if (!(is_array($value) or $value instanceOf Traversable)) {
			$value = [$value];
		}

		foreach ($value as $file) {
			if ($file instanceOf UploadedFile and $file->isValid()) {
				try {
					$name = $directory . '/' . utf8_encode($file->getClientOriginalName());
					$contents = file_get_contents($file->getRealPath());

					if ($storage->has($name)) {
						Log::info('overwriting existing attachment "'.$name.'"');
					}

					if (in_array($name, $attachments) !== false) {
						$index = array_search($name, $attachments);
						unset($attachments[$index]);
					}

					$storage->put($name, $contents);
				} catch (Exception $exception) {
					Log::notice('could not upload attachment: '.$exception->getMessage());
					Session::flash('alert-danger', 'Could not upload attachment: '.$exception->getMessage());
				} finally {
					File::delete($file->getRealPath());
					$file = $name;
				}
			}

			if (!in_array($file, $attachments)) {
				$attachments[] = $file;
			}
		}

		return $this->attributes['attachments'] = json_encode($attachments);
	}
}
