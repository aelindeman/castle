<?php

namespace Castle\Behaviors;

use Illuminate\Support\Collection;

trait ChecksForEncryptedMetadata
{
	/**
	 * Checks if the metadata field appears to be encrypted.
	 *
	 * @return bool
	 */
	public function hasCorrectlyEncryptedMetadata()
	{
		$meta = $this->getAttribute('metadata');

		if ($meta instanceOf Collection and $meta->keys()->all() === [0]) {
			$meta = implode('', $meta->all());
		}

		if (($meta = base64_decode($meta, true)) !== false) {
			if (json_decode($meta) and json_last_error() === JSON_ERROR_NONE) {
				return true;
			}
		}

		return false;
	}
}
