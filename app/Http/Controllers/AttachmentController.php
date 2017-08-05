<?php

namespace Castle\Http\Controllers;

use Carbon;
use Castle\Attachment;
use Castle\Http\Requests;
use File;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Storage;

class AttachmentController extends Controller
{
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth');
	}

	/**
	 * Attaches a file (or an array of files) to an owner object.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public static function attach($file, $owner)
	{
		//
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		$this->authorize('view', Attachment::class);

		$files = Storage::disk('attachments')->allFiles('/');
		$attachments = collect($files)->map(function($att) {
			return new Attachment($att);
		})->sortByDesc(function($att) {
			return $att->date;
		});

		$paginator = new LengthAwarePaginator(
			$attachments->forPage(LengthAwarePaginator::resolveCurrentPage(), 50),
			$attachments->count(),
			50
		);

		$paginator->setPath(route('attachments.index'))
			->appends($request->all());

		return view('attachments.index', ['attachments' => $paginator]);
	}

	/**
	 * Download the specified resource.
	 *
	 * @param  string  $attachment
	 * @return \Illuminate\Http\Response
	 */
	public function download($attachment)
	{
		$this->authorize('view', Attachment::class);

		$storage = Storage::disk('attachments');

		if (!$storage->has($attachment)) {
			return response(view('attachments.404'), 404);
		}

		$age = Carbon\Carbon::createFromTimestamp(
			$storage->lastModified($attachment)
		);

		return response()->stream(function() use ($attachment, $storage) {
			$output = fopen('php://output', 'w');
			fwrite($output, $storage->get($attachment));
			fclose($output);
		}, 200, [
			'Last-Modified' => $age->toRfc2822String(),
			'Expires' => $age->addMonth(),
			'Cache-Control' => 'must-revalidate',
			'Content-Disposition' => 'attachment; filename="'.utf8_encode(basename($attachment)).'"',
			'Content-Length' => $storage->size($attachment),
			'Content-Type' => $storage->mimeType($attachment),
		]);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  string  $attachment
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($attachment)
	{
		$this->authorize('delete', Attachment::class);

		Storage::disk('attachments')->delete($attachment);

		return redirect()->route('attachments.index')
			->with('alert-success', 'Attachment deleted!');
	}
}
